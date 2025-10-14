<?php

namespace Modules\Backup\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Backup\Models\DatabaseBackup;
use Modules\Backup\Models\DatabaseBackupSetting;
use App\Models\StorageSetting;
use ZipArchive;

class CreateDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--type=manual : Type of backup (manual/scheduled)} {--include-files=false : Whether to include application files} {--include-modules=false : Whether to include Modules folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup with various options';

    const EXCLUDED_FILES = [
        '.git',
        '.gitignore',
        '.env',
        'storage/framework/down',
        'storage/framework/maintenance.php',
        'storage/framework/sessions',
        'storage/framework/cache',
        'storage/framework/testing',
        'storage/framework/views',
        'storage/logs',
        'storage/app',
        'bootstrap/cache',
        'node_modules',
        'public/installer',
        'public/backup',
        'files',
        'hot',
        '.cursor',
        'composer.lock',
        'package-lock.json',
        'yarn.lock',
        '.DS_Store',
        'Thumbs.db',
        '*.log',
        '*.tmp',
        '*.cache',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $this->info('----------------------------------------');
        $this->info('Starting database backup process...');

        $backupType = $this->option('type');
        $includeFilesOption = $this->option('include-files');
        $includeFiles = $includeFilesOption === 'true';
        $includeModulesOption = $this->option('include-modules');
        $includeModules = $includeModulesOption === 'true';

        // Get backup settings
        $backupSettings = DatabaseBackupSetting::getSettings();

        // Check if files should be included (either from command line or database settings)
        $includeFiles = $includeFiles || $backupSettings->include_files;

        // Check if modules should be included (either from command line or database settings)
        $includeModules = $includeModules || $backupSettings->include_modules;

        $this->info("Include files setting: " . ($includeFiles ? 'Yes' : 'No'));
        $this->info("Include modules setting: " . ($includeModules ? 'Yes' : 'No'));

        // Read version from version.txt file
        $version = $this->getApplicationVersion();

        // Get storage location from backup settings
        $storageLocation = $backupSettings->storage_location ?? 'local';

        // If storage_setting is selected, get the actual storage configuration
        if ($storageLocation === 'storage_setting') {
            $storageSetting = StorageSetting::where('status', 'enabled')->first();
            $storageLocation = $storageSetting ? $storageSetting->filesystem : 'local';
        }

        // Create backup record with version in filename
        $versionSuffix = ($version !== 'N/A') ? '_v' . $version : '';
        $backup = DatabaseBackup::create([
            'filename' => 'backup' . $versionSuffix . '_' . now()->format('Y-m-d_H-i-s') . '.sql',
            'file_path' => '',
            'status' => 'in_progress',
            'backup_type' => $backupType,
            'version' => $version,
            'stored_on' => $storageLocation,
        ]);

        try {
            DB::enableQueryLog();

            // Get database configuration
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");

            // Create backup directory based on storage location
            if ($storageLocation === 'local') {
                $backupDir = storage_path('app/backups');
                if (!file_exists($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
            } else {
                // For cloud storage, we'll use the configured storage
                $backupDir = storage_path('app/backups');
                if (!file_exists($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
            }

            // Generate backup filename
            $filename = $backup->filename;
            $filePath = $backupDir . '/' . $filename;

            // Create the database backup
            $this->createBackup($config, $filePath);

            // If this is a database-only backup (no files included), compress it
            if (!$includeFiles) {
                $this->info('Compressing database backup...');
                $compressedPath = $this->compressDatabaseBackup($filePath);
                if ($compressedPath) {
                    $filePath = $compressedPath;
                    $filename = basename($compressedPath);
                    $this->info('Database backup compressed successfully: ' . $filename);
                } else {
                    $this->warn('Failed to compress database backup, keeping uncompressed version');
                }
            }

            // Handle file backup if enabled
            if ($includeFiles) {
                $this->info('Creating complete application backup...');
                $this->info('Backup directory: ' . $backupDir);

                // Create files backup first (without the database backup)
                $filesBackupPath = $this->createFilesBackup($backupDir, $includeModules);

                // If files were backed up, create a fresh database backup for the combined archive
                if ($filesBackupPath) {
                    $this->info('Files backup created successfully: ' . basename($filesBackupPath));

                    // Create a fresh database backup for the combined archive
                    $combinedDbFilename = 'database_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
                    $combinedDbPath = $backupDir . '/' . $combinedDbFilename;
                    $this->createBackup($config, $combinedDbPath);

                    $this->info('Fresh database backup created for combined archive: ' . $combinedDbFilename);

                    $combinedArchive = $this->createCombinedBackup($combinedDbPath, $filesBackupPath, $backupDir);
                    if ($combinedArchive) {
                        $this->info('Combined backup created: ' . basename($combinedArchive));
                        $filePath = $combinedArchive;
                        $filename = basename($combinedArchive);
                    } else {
                        $this->warn('Failed to create combined backup');
                    }
                } else {
                    $this->warn('Failed to create files backup');
                }
            } else {
                $this->info('File backup is disabled');
            }

            // Get file size
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

            // If using cloud storage, move file to cloud storage
            if ($storageLocation !== 'local') {
                $cloudPath = $this->moveToCloudStorage($filePath, $filename, $backupSettings);
                $filePath = $cloudPath['path'];
                $storageType = $cloudPath['storage'];
            } else {
                $storageType = 'local';
            }

            // Update backup record with correct filename and extension
            $finalFilename = $filename;
            if ($includeFiles && !str_ends_with($filename, '.zip') && !str_ends_with($filename, '.tar.gz')) {
                // For combined backups, add the appropriate extension
                if (strpos($filePath, '.tar.gz') !== false) {
                    $finalFilename = $filename . '.tar.gz';
                } else {
                    $finalFilename = $filename . '.zip';
                }
            }

            $backup->update([
                'filename' => $finalFilename,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'status' => 'completed',
                'completed_at' => now(),
                'stored_on' => $storageType,
            ]);

            // Clean old backups
            $this->cleanOldBackups();

            $this->info("Backup completed successfully: {$filename}");
            $this->info("File size: " . $this->formatBytes($fileSize));
            $this->info("Storage location: " . ($storageLocation === 'local' ? 'Local' : 'Cloud Storage'));
            if ($includeFiles) {
                $this->info("Files included: Yes");
                $this->info("Modules included: " . ($includeModules ? 'Yes' : 'No'));
            } else {
                $this->info("Database backup compressed: Yes");
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Database backup failed: ' . $errorMessage);

            $backup->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
            ]);

            $this->error('Backup failed: ' . $errorMessage);

            return Command::FAILURE;
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $memoryUsed = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);
        $queryCount = count(DB::getQueryLog());

        $this->line("<fg=green>✓</> <fg=blue>Completed in</> <fg=yellow>{$executionTime}s</> <fg=white>|</> <fg=yellow>{$memoryUsed}MB</> <fg=white>|</> <fg=yellow>{$queryCount}</> <fg=blue>queries</>");

        return Command::SUCCESS;
    }

    /**
     * Create backup based on database type
     */
    private function createBackup($config, $filePath)
    {
        $driver = $config['driver'];

        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                $this->createMysqlBackup($config, $filePath);
                break;
            case 'pgsql':
                $this->createPostgresBackup($config, $filePath);
                break;
            case 'sqlite':
                $this->createSqliteBackup($config, $filePath);
                break;
            default:
                throw new \Exception("Unsupported database driver: {$driver}");
        }
    }

    /**
     * Create MySQL/MariaDB backup
     */
    private function createMysqlBackup($config, $filePath)
    {
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // Find mysqldump executable
        $mysqldumpPath = $this->findMysqldump();

        if (!$mysqldumpPath) {
            throw new \Exception("mysqldump command not found. Please ensure MySQL is installed and mysqldump is in your PATH.");
        }

        $this->info("Using mysqldump at: {$mysqldumpPath}");
        $this->info("Database: {$database}");
        $this->info("Host: {$host}:{$port}");
        $this->info("Username: {$username}");
        $this->info("Password: " . (empty($password) ? 'Empty' : 'Set (hidden)'));
        $this->info("Config source: .env file via Laravel config");

        // Test connection first
        $this->testMysqlConnection($host, $port, $username, $password, $database);

        // Create backup directory if it doesn't exist
        $backupDir = dirname($filePath);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Try Laravel-native backup first (most reliable)
        $this->info("Trying Laravel-native database backup...");
        try {
            $this->createLaravelNativeBackup($config, $filePath);
            $this->info("✓ Laravel-native backup completed successfully");
            return;
        } catch (\Exception $e) {
            $this->warn("Laravel-native backup failed: " . $e->getMessage());
            $this->info("Falling back to mysqldump methods...");
        }

        // Try different authentication methods for Ubuntu
        $methods = [
            'socket_auth' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'socket_auth'),
            'socket_no_password' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'socket_no_password'),
            'tcp_auth' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'tcp_auth'),
            'tcp_no_password' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'tcp_no_password'),
            'password_file' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'password_file'),
            'config_file' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'config_file'),
            'mysql8_fix' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'mysql8_fix'),
        ];

        $lastError = null;
        foreach ($methods as $method => $command) {
            try {
                $this->info("Trying authentication method: {$method}");
                $this->executeCommand($command);
                $this->info("Backup completed successfully using method: {$method}");
                return;
            } catch (\Exception $e) {
                $lastError = $e;
                $this->warn("Method {$method} failed: " . $e->getMessage());
                continue;
            }
        }

        // If all methods failed, try to fix MySQL 8.0 authentication
        $this->info("All standard methods failed. Attempting MySQL 8.0 authentication fix...");
        try {
            $this->fixMySQL8Authentication($host, $port, $username, $password, $database);

            // Wait a moment for the change to take effect
            sleep(2);

            // Try multiple methods after the fix
            $retryMethods = [
                'socket_auth' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'socket_auth'),
                'tcp_auth' => $this->buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, 'tcp_auth'),
            ];

            foreach ($retryMethods as $method => $command) {
                try {
                    $this->info("Retrying with method: {$method}");
                    $this->executeCommand($command);
                    $this->info("Backup completed successfully after authentication fix using method: {$method}");
                    return;
                } catch (\Exception $e) {
                    $this->warn("Retry method {$method} failed: " . $e->getMessage());
                    continue;
                }
            }

            $this->warn("All retry methods failed after authentication fix");
        } catch (\Exception $e) {
            $this->warn("Authentication fix failed: " . $e->getMessage());
        }

        // If all methods failed, provide detailed troubleshooting information
        $this->error("All authentication methods failed. Troubleshooting steps:");
        $this->error("1. Check if MySQL is running: sudo systemctl status mysql");
        $this->error("2. Check MySQL user permissions: mysql -u root -p -e 'SHOW GRANTS FOR root@localhost'");
        $this->error("3. Check if mysqldump has proper permissions");
        $this->error("4. Try running mysqldump manually to test");
        $this->error("5. Check MySQL configuration: sudo cat /etc/mysql/mysql.conf.d/mysqld.cnf | grep bind-address");
        $this->error("6. Verify .env file has correct DB_PASSWORD");
        $this->error("7. Fix MySQL 8.0 authentication: sudo mysql -u root -p -e 'ALTER USER root@localhost IDENTIFIED WITH mysql_native_password BY \"your_password\"'");

        throw new \Exception("All authentication methods failed. Last error: " . $lastError->getMessage());
    }

    /**
     * Create database backup using Laravel's native PDO functionality
     */
    private function createLaravelNativeBackup($config, $filePath)
    {
        $this->info("Creating Laravel-native database backup...");

        try {
            // Connect using PDO (which we know works)
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);

            $sqlContent = '';

            // Get all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $this->info("Processing table: {$table}");

                // Get table structure
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $sqlContent .= "\n-- Table structure for table `{$table}`\n";
                $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sqlContent .= $createTable['Create Table'] . ";\n\n";

                // Get table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();
                if (!empty($rows)) {
                    $sqlContent .= "-- Dumping data for table `{$table}`\n";

                    // Process in chunks to avoid memory issues
                    $chunkSize = 1000;
                    $chunks = array_chunk($rows, $chunkSize);

                    foreach ($chunks as $chunk) {
                        $sqlContent .= "INSERT INTO `{$table}` VALUES\n";
                        $values = [];

                        foreach ($chunk as $row) {
                            $rowValues = [];
                            foreach ($row as $value) {
                                if ($value === null) {
                                    $rowValues[] = 'NULL';
                                } else {
                                    $rowValues[] = $pdo->quote($value);
                                }
                            }
                            $values[] = '(' . implode(', ', $rowValues) . ')';
                        }

                        $sqlContent .= implode(",\n", $values) . ";\n\n";
                    }
                }
            }

            // Add database creation and use statements
            $databaseName = $config['database'];
            $finalSql = "-- Laravel Native Database Backup\n";
            $finalSql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            $finalSql .= "-- Database: {$databaseName}\n\n";
            $finalSql .= "CREATE DATABASE IF NOT EXISTS `{$databaseName}`;\n";
            $finalSql .= "USE `{$databaseName}`;\n\n";
            $finalSql .= $sqlContent;

            // Write to file
            if (file_put_contents($filePath, $finalSql) === false) {
                throw new \Exception("Failed to write backup file");
            }

            $this->info("✓ Laravel-native backup created successfully: " . basename($filePath));
        } catch (\Exception $e) {
            throw new \Exception("Laravel-native backup failed: " . $e->getMessage());
        }
    }

    /**
     * Test MySQL connection before attempting backup
     */
    private function testMysqlConnection($host, $port, $username, $password, $database)
    {
        $this->info("Testing MySQL connection...");

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);

            $stmt = $pdo->query('SELECT 1');
            $result = $stmt->fetch();

            if ($result) {
                $this->info("✓ MySQL connection test successful");
            } else {
                throw new \Exception("Connection test failed - no result returned");
            }
        } catch (\Exception $e) {
            $this->warn("MySQL connection test failed: " . $e->getMessage());
            $this->warn("This might be due to PDO vs mysqldump authentication differences");
        }
    }

    /**
     * Build mysqldump command with different authentication methods
     */
    private function buildMysqlDumpCommand($mysqldumpPath, $host, $port, $username, $password, $database, $filePath, $method)
    {
        $command = "{$mysqldumpPath}";

        switch ($method) {
            case 'socket_auth':
                // Try socket authentication with password
                $command .= " --user={$username}";
                if (!empty($password)) {
                    $command .= " --password={$password}";
                }
                break;

            case 'socket_no_password':
                // Try socket authentication without password
                $command .= " --user={$username}";
                break;

            case 'tcp_auth':
                // Try TCP connection with password
                $command .= " --host={$host} --port={$port} --user={$username}";
                if (!empty($password)) {
                    $command .= " --password={$password}";
                }
                break;

            case 'tcp_no_password':
                // Try TCP connection without password
                $command .= " --host={$host} --port={$port} --user={$username}";
                break;

            case 'password_file':
                // Create temporary password file
                $tempPasswordFile = tempnam(sys_get_temp_dir(), 'mysql_pass_');
                file_put_contents($tempPasswordFile, $password ?: '');
                chmod($tempPasswordFile, 0600);
                $command .= " --host={$host} --port={$port} --user={$username} --defaults-extra-file={$tempPasswordFile}";
                break;

            case 'config_file':
                // Create temporary MySQL config file
                $tempConfigFile = tempnam(sys_get_temp_dir(), 'mysql_config_');
                $configContent = "[client]\n";
                $configContent .= "host={$host}\n";
                $configContent .= "port={$port}\n";
                $configContent .= "user={$username}\n";
                if (!empty($password)) {
                    $configContent .= "password={$password}\n";
                }
                file_put_contents($tempConfigFile, $configContent);
                chmod($tempConfigFile, 0600);
                $command .= " --defaults-extra-file={$tempConfigFile}";
                break;

            case 'mysql8_fix':
                // This method is for fixing MySQL 8.0 authentication issues.
                // It creates a temporary user with mysql_native_password.
                $tempUser = 'temp_user_' . uniqid();
                $tempPassword = 'temp_pass_' . uniqid();
                $command = "{$mysqldumpPath} --user={$username} --password={$password} --host={$host} --port={$port} --single-transaction --routines --triggers --add-drop-database --databases {$database} > {$filePath}";
                break;
        }

        $command .= " --single-transaction --routines --triggers --add-drop-database --databases {$database} > {$filePath}";

        return $command;
    }

    /**
     * Find mysqldump executable
     */
    private function findMysqldump()
    {
        // Common paths for mysqldump (including Ubuntu paths)
        $possiblePaths = [
            '/usr/bin/mysqldump',           // Ubuntu/Debian default
            '/usr/local/bin/mysqldump',     // Common installation
            '/opt/homebrew/opt/mysql@8.0/bin/mysqldump', // macOS Homebrew
            '/opt/homebrew/bin/mysqldump',  // macOS Homebrew
            '/opt/mysql/bin/mysqldump',     // Custom installation
            '/usr/local/mysql/bin/mysqldump', // Custom installation
            'mysqldump', // Try PATH as fallback
        ];

        $this->info("Searching for mysqldump executable...");

        foreach ($possiblePaths as $path) {
            $this->info("Checking: {$path}");

            if (is_executable($path)) {
                $this->info("Found executable mysqldump at: {$path}");
                return $path;
            }

            if ($this->isCommandAvailable($path)) {
                $this->info("Found mysqldump in PATH: {$path}");
                return $path;
            }
        }

        // Try to find it using 'which' command
        $output = [];
        $returnCode = 0;
        exec('which mysqldump 2>/dev/null', $output, $returnCode);

        if ($returnCode === 0 && !empty($output)) {
            $mysqldumpPath = trim($output[0]);
            if (is_executable($mysqldumpPath)) {
                $this->info("Found mysqldump using 'which' command: {$mysqldumpPath}");
                return $mysqldumpPath;
            }
        }

        $this->error("mysqldump not found in any of the expected locations");
        return null;
    }

    /**
     * Check if required commands are available on the server
     */
    private function checkServerCompatibility()
    {
        $useGzip = false;

        // Check for tar command
        $tarAvailable = $this->isCommandAvailable('tar');
        if (!$tarAvailable) {
            $this->warn('tar command not available, will use ZIP compression');
            return false;
        }

        // Check for gzip command
        $gzipAvailable = $this->isCommandAvailable('gzip');
        if (!$gzipAvailable) {
            $this->warn('gzip command not available, will use ZIP compression');
            return false;
        }

        $this->info('Server supports gzip compression');
        return true;
    }

    /**
     * Check if a command is available
     */
    private function isCommandAvailable($command)
    {
        $output = [];
        $returnCode = 0;

        exec("which {$command} 2>/dev/null", $output, $returnCode);

        return $returnCode === 0 && !empty($output);
    }

    /**
     * Create PostgreSQL backup
     */
    private function createPostgresBackup($config, $filePath)
    {
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // Set password environment variable
        putenv("PGPASSWORD={$password}");

        $command = "pg_dump --host={$host} --port={$port} --username={$username} --dbname={$database} --file={$filePath}";

        $this->info("Executing PostgreSQL backup command...");
        $this->executeCommand($command);

        // Clear password from environment
        putenv("PGPASSWORD");
    }

    /**
     * Create SQLite backup
     */
    private function createSqliteBackup($config, $filePath)
    {
        $database = $config['database'];

        if (!file_exists($database)) {
            throw new \Exception("SQLite database file not found: {$database}");
        }

        // For SQLite, we can simply copy the file
        if (!copy($database, $filePath)) {
            throw new \Exception("Failed to copy SQLite database file");
        }

        $this->info("SQLite database copied successfully");
    }

    /**
     * Execute shell command
     */
    private function executeCommand($command)
    {
        $output = [];
        $returnCode = 0;

        $this->info("Executing command: " . str_replace($this->getPasswordFromCommand($command), '***HIDDEN***', $command));

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            $errorOutput = implode("\n", $output);
            $this->error("Command output: " . $errorOutput);
            throw new \Exception("Command failed with return code {$returnCode}: {$errorOutput}");
        }

        $this->info("Command executed successfully");
    }

    /**
     * Extract password from command for logging (hide sensitive data)
     */
    private function getPasswordFromCommand($command)
    {
        if (preg_match('/--password=([^\s]+)/', $command, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * Clean old backups based on settings
     */
    private function cleanOldBackups()
    {
        $settings = DatabaseBackupSetting::getSettings();

        if (!$settings->retention_days && !$settings->max_backups) {
            return;
        }

        // Remove backups older than retention days
        if ($settings->retention_days) {
            $cutoffDate = now()->subDays($settings->retention_days);
            $oldBackups = DatabaseBackup::completed()
                ->where('created_at', '<', $cutoffDate)
                ->get();

            foreach ($oldBackups as $backup) {
                $this->deleteBackupFile($backup);
                $backup->delete();
            }
        }

        // Keep only max_backups number of backups
        if ($settings->max_backups) {
            $totalBackups = DatabaseBackup::completed()->count();

            if ($totalBackups > $settings->max_backups) {
                $excessCount = $totalBackups - $settings->max_backups;
                $excessBackups = DatabaseBackup::completed()
                    ->orderBy('created_at', 'asc')
                    ->limit($excessCount)
                    ->get();

                foreach ($excessBackups as $backup) {
                    $this->deleteBackupFile($backup);
                    $backup->delete();
                }
            }
        }
    }

    /**
     * Delete backup file from storage
     */
    private function deleteBackupFile($backup)
    {
        try {
            if ($backup->stored_on === 'local') {
                // Delete from local storage
                if (file_exists($backup->file_path)) {
                    unlink($backup->file_path);
                    $this->info("Deleted local backup file: " . basename($backup->file_path));
                }
            } else {
                // Delete from cloud storage
                $storageSetting = StorageSetting::where('status', 'enabled')->first();
                if ($storageSetting && $storageSetting->filesystem === $backup->stored_on) {
                    if (Storage::disk($backup->stored_on)->exists($backup->file_path)) {
                        Storage::disk($backup->stored_on)->delete($backup->file_path);
                        $this->info("Deleted cloud backup file: " . basename($backup->file_path) . " from " . $backup->stored_on);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->warn("Failed to delete backup file: " . $e->getMessage());
        }
    }

    /**
     * Move backup file to cloud storage
     */
    private function moveToCloudStorage($localFilePath, $filename, $backupSettings)
    {
        try {
            // Check if backup settings has storage configuration
            if ($backupSettings->storage_location === 'storage_setting' && !empty($backupSettings->storage_config)) {
                $storageConfig = $backupSettings->storage_config;

                if (isset($storageConfig['filesystem']) && $storageConfig['filesystem'] !== 'local') {
                    // Use the storage configuration from backup settings
                    $filesystem = $storageConfig['filesystem'];

                    // Create the cloud path
                    $cloudPath = 'backups/' . $filename;

                    // Upload to cloud storage
                    $contents = file_get_contents($localFilePath);
                    Storage::disk($filesystem)->put($cloudPath, $contents);

                    // Delete local file after successful upload
                    if (file_exists($localFilePath)) {
                        unlink($localFilePath);
                    }

                    $this->info("Backup uploaded to cloud storage using backup settings: {$cloudPath}");

                    return ['path' => $cloudPath, 'storage' => $filesystem];
                }
            }

            // Fallback to file storage settings
            $storageSetting = StorageSetting::where('status', 'enabled')->first();

            if (!$storageSetting || $storageSetting->filesystem === 'local') {
                // If no cloud storage is configured, keep it local
                return ['path' => $localFilePath, 'storage' => 'local'];
            }

            // Create the cloud path
            $cloudPath = 'backups/' . $filename;

            // Upload to cloud storage
            $contents = file_get_contents($localFilePath);
            Storage::disk($storageSetting->filesystem)->put($cloudPath, $contents);

            // Delete local file after successful upload
            if (file_exists($localFilePath)) {
                unlink($localFilePath);
            }

            $this->info("Backup uploaded to cloud storage using file storage settings: {$cloudPath}");

            return ['path' => $cloudPath, 'storage' => $storageSetting->filesystem];
        } catch (\Exception $e) {
            $this->warn("Failed to upload to cloud storage: " . $e->getMessage());
            $this->warn("Keeping backup in local storage");
            return ['path' => $localFilePath, 'storage' => 'local'];
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Remove directory recursively
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }

    /**
     * Create files backup with maximum compression and hardcoded exclusions
     */
    private function createFilesBackup($backupDir, $includeModules = false)
    {
        $version = $this->getApplicationVersion();
        $versionSuffix = ($version !== 'N/A') ? '_v' . $version : '';
        $filesBackupName = 'files_backup' . $versionSuffix . '_' . now()->format('Y-m-d_H-i-s');
        $filesBackupPath = $backupDir . '/' . $filesBackupName;

        // Check server compatibility first
        $useGzip = $this->checkServerCompatibility();

        // Build exclusion list based on modules setting
        $exclusions = self::EXCLUDED_FILES;
        if (!$includeModules) {
            // Instead of excluding the entire Modules folder, we'll exclude its contents
            // but keep the folder structure
            $exclusions[] = 'Modules/*';
        }

        // Exclude any existing backup files to prevent including old database backups
        $exclusions[] = 'storage/app/backups/*';
        $exclusions[] = '*.sql';
        $exclusions[] = '*.sql.gz';
        $exclusions[] = '*.zip';
        $exclusions[] = '*.tar.gz';

        $this->info("Creating application backup with exclusions...");
        $this->info("Include modules: " . ($includeModules ? 'Yes' : 'No'));
        $this->info("Excluded items: " . implode(', ', $exclusions));

        try {
            if ($useGzip) {
                return $this->createGzipBackup($filesBackupPath, $backupDir, $exclusions);
            } else {
                return $this->createZipBackup($filesBackupPath, $backupDir, $exclusions);
            }
        } catch (\Exception $e) {
            $this->error("Failed to create application backup: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create gzip backup
     */
    private function createGzipBackup($filesBackupPath, $backupDir, $exclusions = null)
    {
        $tarPath = $filesBackupPath . '.tar';
        $gzipPath = $filesBackupPath . '.tar.gz';

        // Use provided exclusions or fall back to default
        if ($exclusions === null) {
            $exclusions = self::EXCLUDED_FILES;
        }

        // Create tar file first
        $tarCommand = "cd " . base_path() . " && tar -cf {$tarPath}";

        // Build tar command with exclusions
        foreach ($exclusions as $excludeItem) {
            // Handle wildcard patterns for tar
            if (strpos($excludeItem, '*') !== false) {
                // For tar, we need to handle wildcards differently
                $tarCommand .= " --exclude='{$excludeItem}'";
            } else {
                $tarCommand .= " --exclude='{$excludeItem}'";
            }
        }

        // Add all remaining files
        $tarCommand .= " .";

        $this->info("Creating tar archive with exclusions...");
        $this->executeCommand($tarCommand);

        if (!file_exists($tarPath)) {
            throw new \Exception("Failed to create tar file: {$tarPath}");
        }

        // If Modules folder is excluded but we want to keep the structure,
        // we need to manually add an empty Modules folder
        if (in_array('Modules/*', $exclusions)) {
            $this->info("Adding empty Modules folder structure to tar archive...");
            // Create a temporary Modules directory structure
            $tempModulesDir = $backupDir . '/temp_modules';
            if (!is_dir($tempModulesDir)) {
                mkdir($tempModulesDir, 0755, true);
            }
            $addModulesCommand = "cd " . base_path() . " && tar -rf {$tarPath} -C {$backupDir} temp_modules";
            $this->executeCommand($addModulesCommand);
            // Clean up temporary directory
            if (is_dir($tempModulesDir)) {
                $this->removeDirectory($tempModulesDir);
            }
        }

        // Compress with gzip using maximum compression
        $gzipCommand = "gzip -9 -c {$tarPath} > {$gzipPath}";
        $this->info("Compressing with gzip (maximum compression)...");
        $this->executeCommand($gzipCommand);

        // Remove the uncompressed tar file
        if (file_exists($tarPath)) {
            unlink($tarPath);
        }

        if (!file_exists($gzipPath)) {
            throw new \Exception("Failed to create gzip file: {$gzipPath}");
        }

        $this->info("Gzip backup created successfully: " . basename($gzipPath));
        return $gzipPath;
    }

    /**
     * Create ZIP backup
     */
    private function createZipBackup($filesBackupPath, $backupDir, $exclusions = null)
    {
        $zipPath = $filesBackupPath . '.zip';

        // Use provided exclusions or fall back to default
        if ($exclusions === null) {
            $exclusions = self::EXCLUDED_FILES;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("Could not create ZIP file");
        }

        $this->info("Creating ZIP archive with exclusions...");
        $this->addDirectoryToZip($zip, base_path(), '', $exclusions);

        $zip->close();

        if (!file_exists($zipPath)) {
            throw new \Exception("Failed to create ZIP file: {$zipPath}");
        }

        $this->info("ZIP backup created successfully: " . basename($zipPath));
        return $zipPath;
    }

    /**
     * Add directory to ZIP with exclusions
     */
    private function addDirectoryToZip($zip, $dir, $relativePath, $exclusions)
    {
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . '/' . $file;
            $zipPath = $relativePath ? $relativePath . '/' . $file : $file;

            // Check if this file/directory should be excluded
            if ($this->shouldExclude($zipPath, $exclusions)) {
                $this->info("Excluding: {$zipPath}");
                continue;
            }

            if (is_dir($filePath)) {
                // For Modules folder, we want to keep the folder structure but exclude contents
                if ($zipPath === 'Modules' && in_array('Modules/*', $exclusions)) {
                    // Create an empty Modules folder in the ZIP
                    $zip->addEmptyDir($zipPath);
                    $this->info("Creating empty Modules folder structure");
                } else {
                    $this->addDirectoryToZip($zip, $filePath, $zipPath, $exclusions);
                }
            } else {
                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    /**
     * Check if a file/directory should be excluded
     */
    private function shouldExclude($path, $exclusions)
    {
        foreach ($exclusions as $exclusion) {
            // Handle wildcard patterns
            if (strpos($exclusion, '*') !== false) {
                // Convert glob pattern to regex
                $pattern = preg_quote($exclusion, '#');
                $pattern = str_replace(['\*', '\?'], ['.*', '.'], $pattern);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $path)) {
                    return true;
                }
            } else {
                // Exact match
                if ($path === $exclusion) {
                    return true;
                }

                // Check if path starts with exclusion (for directory exclusions)
                if (strpos($path, $exclusion . '/') === 0) {
                    return true;
                }

                // Check if path equals exclusion (for exact directory match)
                if ($path === $exclusion) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Create combined backup archive
     */
    private function createCombinedBackup($databaseBackupPath, $filesBackupPath, $backupDir)
    {
        try {
            $version = $this->getApplicationVersion();
            $versionSuffix = ($version !== 'N/A') ? '_v' . $version : '';
            $timestamp = now()->format('Y-m-d_H-i-s');

            // Check if we're using gzip or ZIP based on file extension
            $useGzip = strpos($filesBackupPath, '.tar.gz') !== false;

            // Compress the database backup
            $compressedDbPath = null;
            if (file_exists($databaseBackupPath)) {
                $this->info("Compressing database backup...");
                $compressedDbPath = $this->compressDatabaseBackup($databaseBackupPath);
                if (!$compressedDbPath) {
                    throw new \Exception("Failed to compress database backup");
                }
            }

            if ($useGzip) {
                $combinedName = 'combined_backup' . $versionSuffix . '_' . $timestamp . '.tar.gz';
                $combinedPath = $backupDir . '/' . $combinedName;

                // Create tar command for combined backup
                $tarCommand = "cd {$backupDir} && tar -czf {$combinedName}";

                // Add compressed database backup
                if ($compressedDbPath && file_exists($compressedDbPath)) {
                    $tarCommand .= " " . basename($compressedDbPath);
                }

                // Add files backup
                if (file_exists($filesBackupPath)) {
                    $tarCommand .= " " . basename($filesBackupPath);
                }

                $this->info("Creating combined backup archive...");
                $this->executeCommand($tarCommand);
            } else {
                $combinedName = 'combined_backup' . $versionSuffix . '_' . $timestamp . '.zip';
                $combinedPath = $backupDir . '/' . $combinedName;

                $zip = new ZipArchive();
                if ($zip->open($combinedPath, ZipArchive::CREATE) !== TRUE) {
                    throw new \Exception("Could not create combined backup file");
                }

                // Add compressed database backup
                if ($compressedDbPath && file_exists($compressedDbPath)) {
                    $zip->addFile($compressedDbPath, 'database/' . basename($compressedDbPath));
                }

                // Add files backup
                if (file_exists($filesBackupPath)) {
                    $zip->addFile($filesBackupPath, 'files/' . basename($filesBackupPath));
                }

                $zip->close();
            }

            // Clean up individual files
            if (file_exists($databaseBackupPath)) {
                unlink($databaseBackupPath);
                $this->info("Cleaned up temporary database backup: " . basename($databaseBackupPath));
            }
            if ($compressedDbPath && file_exists($compressedDbPath)) {
                unlink($compressedDbPath);
                $this->info("Cleaned up temporary compressed database backup: " . basename($compressedDbPath));
            }
            if (file_exists($filesBackupPath)) {
                unlink($filesBackupPath);
                $this->info("Cleaned up temporary files backup: " . basename($filesBackupPath));
            }

            return $combinedPath;
        } catch (\Exception $e) {
            $this->error("Failed to create combined backup: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Read version from version.txt file
     */
    private function getApplicationVersion()
    {
        $versionFile = public_path('version.txt');
        if (file_exists($versionFile)) {
            $version = trim(file_get_contents($versionFile));
            if (!empty($version)) {
                $this->info("Application version: " . $version);
                return $version;
            }
        }

        $this->warn("Version file not found or empty, using 'N/A'");
        return 'N/A';
    }

    /**
     * Compress the database backup file using gzip.
     * This method is called when --include-files is false.
     */
    private function compressDatabaseBackup($filePath)
    {
        $gzipPath = $filePath . '.gz';
        $this->info("Compressing database backup: " . basename($filePath));

        try {
            $command = "gzip -9 -c {$filePath} > {$gzipPath}";
            $this->executeCommand($command);

            // Delete the original uncompressed file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->info("Database backup compressed successfully: " . basename($gzipPath));
            return $gzipPath;
        } catch (\Exception $e) {
            $this->warn("Failed to compress database backup: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fix MySQL 8.0 authentication issues
     */
    private function fixMySQL8Authentication($host, $port, $username, $password, $database)
    {
        $this->info("Attempting to fix MySQL 8.0 authentication...");

        try {
            // Connect using PDO (which works)
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            // Check current authentication plugin
            $stmt = $pdo->query("SELECT user, host, plugin FROM mysql.user WHERE user = '{$username}'");
            $userInfo = $stmt->fetch();

            if ($userInfo && $userInfo['plugin'] === 'caching_sha2_password') {
                $this->info("User {$username} is using caching_sha2_password plugin");
                $this->info("Attempting to change to mysql_native_password...");

                // Change authentication plugin to mysql_native_password
                $pdo->exec("ALTER USER '{$username}'@'localhost' IDENTIFIED WITH mysql_native_password BY '{$password}'");
                $this->info("Authentication plugin changed to mysql_native_password");
            } else {
                $this->info("User {$username} is already using mysql_native_password or other plugin");
            }
        } catch (\Exception $e) {
            $this->warn("Could not fix authentication automatically: " . $e->getMessage());
            $this->info("Please run manually: sudo mysql -u root -p -e 'ALTER USER root@localhost IDENTIFIED WITH mysql_native_password BY \"your_password\"'");
        }
    }
}
