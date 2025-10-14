<?php

namespace Modules\Backup\Console\Commands;

use Illuminate\Console\Command;

class UbuntuTroubleshoot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:ubuntu-troubleshoot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ubuntu-specific MySQL backup troubleshooting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Ubuntu MySQL Backup Troubleshooting ===');

        // Get database configuration
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $this->info("Database: {$database}");
        $this->info("Host: {$host}:{$port}");
        $this->info("Username: {$username}");
        $this->info("Password: " . (empty($password) ? 'Empty' : 'Set (hidden)'));

        // Test 1: Check system information
        $this->info("\n1. System Information:");
        $this->executeSystemCommand('uname -a', 'System info');
        $this->executeSystemCommand('lsb_release -a', 'Ubuntu version');

        // Test 2: Check MySQL service
        $this->info("\n2. MySQL Service Status:");
        $this->executeSystemCommand('systemctl status mysql', 'MySQL service status');
        $this->executeSystemCommand('systemctl status mariadb', 'MariaDB service status');

        // Test 3: Check MySQL processes
        $this->info("\n3. MySQL Processes:");
        $this->executeSystemCommand('ps aux | grep mysql', 'MySQL processes');

        // Test 4: Check MySQL socket
        $this->info("\n4. MySQL Socket:");
        $socketPaths = [
            '/var/run/mysqld/mysqld.sock',
            '/tmp/mysql.sock',
            '/var/lib/mysql/mysql.sock',
            '/opt/lampp/var/mysql/mysql.sock',
        ];

        foreach ($socketPaths as $socketPath) {
            if (file_exists($socketPath)) {
                $this->info("✓ MySQL socket found: {$socketPath}");
                $this->executeSystemCommand("ls -la {$socketPath}", "Socket permissions");
                break;
            }
        }

        // Test 5: Check mysqldump availability
        $this->info("\n5. mysqldump Availability:");
        $this->executeSystemCommand('which mysqldump', 'mysqldump location');
        $this->executeSystemCommand('mysqldump --version', 'mysqldump version');

        // Test 6: Check MySQL configuration
        $this->info("\n6. MySQL Configuration:");
        $configFiles = [
            '/etc/mysql/mysql.conf.d/mysqld.cnf',
            '/etc/mysql/my.cnf',
            '/etc/my.cnf',
        ];

        foreach ($configFiles as $configFile) {
            if (file_exists($configFile)) {
                $this->info("✓ MySQL config found: {$configFile}");
                $this->executeSystemCommand("grep -E 'bind-address|port|socket' {$configFile}", "Key config values");
                break;
            }
        }

        // Test 7: Check MySQL user permissions
        $this->info("\n7. MySQL User Permissions:");
        if (!empty($password)) {
            $this->executeSystemCommand("mysql -u {$username} -p{$password} -e 'SHOW GRANTS FOR CURRENT_USER()'", 'Current user grants');
        } else {
            $this->executeSystemCommand("mysql -u {$username} -e 'SHOW GRANTS FOR CURRENT_USER()'", 'Current user grants (no password)');
        }

        // Test 8: Manual mysqldump tests
        $this->info("\n8. Manual mysqldump Tests:");
        $mysqldumpPath = $this->findMysqldump();
        if ($mysqldumpPath) {
            $tests = [
                'Socket auth with password' => "{$mysqldumpPath} --user={$username} --password={$password} --single-transaction --no-data --databases {$database}",
                'Socket auth without password' => "{$mysqldumpPath} --user={$username} --single-transaction --no-data --databases {$database}",
                'TCP auth with password' => "{$mysqldumpPath} --host={$host} --port={$port} --user={$username} --password={$password} --single-transaction --no-data --databases {$database}",
                'TCP auth without password' => "{$mysqldumpPath} --host={$host} --port={$port} --user={$username} --single-transaction --no-data --databases {$database}",
            ];

            foreach ($tests as $description => $command) {
                $this->info("\nTesting: {$description}");
                $output = [];
                $returnCode = 0;
                exec($command . ' 2>&1', $output, $returnCode);

                if ($returnCode === 0) {
                    $this->info("✓ SUCCESS");
                } else {
                    $this->error("✗ FAILED (return code: {$returnCode})");
                    $this->error("Output: " . implode("\n", array_slice($output, 0, 3)));
                }
            }
        }

        // Test 9: Check file permissions
        $this->info("\n9. File Permissions:");
        $backupDir = storage_path('app/backups');
        $this->executeSystemCommand("ls -la " . dirname($backupDir), 'Backup directory permissions');
        $this->executeSystemCommand("whoami", 'Current user');
        $this->executeSystemCommand("groups", 'User groups');

        // Test 10: Check PHP configuration
        $this->info("\n10. PHP Configuration:");
        $this->executeSystemCommand('php -v', 'PHP version');
        $this->executeSystemCommand('php -m | grep -i mysql', 'MySQL PHP extensions');
        $this->executeSystemCommand('php -i | grep -E "memory_limit|max_execution_time"', 'PHP limits');

        $this->info("\n=== Troubleshooting Complete ===");
        $this->info("Common Ubuntu MySQL issues and solutions:");
        $this->info("1. If MySQL service not running: sudo systemctl start mysql");
        $this->info("2. If permission denied: sudo chown -R www-data:www-data /var/lib/mysql");
        $this->info("3. If socket not found: sudo mkdir -p /var/run/mysqld && sudo chown mysql:mysql /var/run/mysqld");
        $this->info("4. If mysqldump not found: sudo apt-get install mysql-client");
        $this->info("5. If authentication fails: sudo mysql -u root -p -e 'ALTER USER root@localhost IDENTIFIED WITH mysql_native_password BY \"password\"'");
    }

    private function executeSystemCommand($command, $description)
    {
        $this->info("\n{$description}:");
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0) {
            foreach (array_slice($output, 0, 5) as $line) {
                $this->info("  " . $line);
            }
            if (count($output) > 5) {
                $this->info("  ... (showing first 5 lines)");
            }
        } else {
            $this->warn("  Command failed (return code: {$returnCode})");
            foreach (array_slice($output, 0, 3) as $line) {
                $this->warn("  " . $line);
            }
        }
    }

    private function findMysqldump()
    {
        $possiblePaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/opt/mysql@8.0/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            'mysqldump',
        ];

        foreach ($possiblePaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        // Try which command
        $output = [];
        $returnCode = 0;
        exec('which mysqldump 2>/dev/null', $output, $returnCode);

        if ($returnCode === 0 && !empty($output)) {
            $mysqldumpPath = trim($output[0]);
            if (is_executable($mysqldumpPath)) {
                return $mysqldumpPath;
            }
        }

        return null;
    }
}
