<?php

namespace Modules\Backup\Console\Commands;

use Illuminate\Console\Command;

class FixMySQLAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:fix-mysql-auth {--password= : MySQL root password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix MySQL 8.0 authentication issues for backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== MySQL Authentication Fix ===');

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

        // Check if we can connect via PDO
        $this->info("\n1. Testing PDO connection...");
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->query('SELECT 1 as test');
            $result = $stmt->fetch();

            if ($result && $result['test'] == 1) {
                $this->info("✓ PDO connection successful");
            } else {
                $this->error("✗ PDO connection failed");
                return;
            }
        } catch (\Exception $e) {
            $this->error("✗ PDO connection failed: " . $e->getMessage());
            return;
        }

        // Check current authentication plugin
        $this->info("\n2. Checking authentication plugin...");
        try {
            $stmt = $pdo->query("SELECT user, host, plugin FROM mysql.user WHERE user = '{$username}'");
            $userInfo = $stmt->fetch();

            if ($userInfo) {
                $this->info("User: " . $userInfo['user']);
                $this->info("Host: " . $userInfo['host']);
                $this->info("Plugin: " . $userInfo['plugin']);

                if ($userInfo['plugin'] === 'caching_sha2_password') {
                    $this->warn("⚠ User is using caching_sha2_password plugin (MySQL 8.0 default)");
                    $this->info("This can cause issues with mysqldump authentication");

                    // Ask for confirmation
                    if (!$this->confirm('Do you want to change the authentication plugin to mysql_native_password?')) {
                        $this->info("Operation cancelled");
                        return;
                    }

                    // Change authentication plugin
                    $this->info("\n3. Changing authentication plugin...");
                    $pdo->exec("ALTER USER '{$username}'@'localhost' IDENTIFIED WITH mysql_native_password BY '{$password}'");
                    $this->info("✓ Authentication plugin changed to mysql_native_password");

                    // Verify the change
                    $stmt = $pdo->query("SELECT user, host, plugin FROM mysql.user WHERE user = '{$username}'");
                    $userInfo = $stmt->fetch();
                    $this->info("New plugin: " . $userInfo['plugin']);

                } else {
                    $this->info("✓ User is already using mysql_native_password or other compatible plugin");
                }
            } else {
                $this->error("✗ Could not find user information");
            }

        } catch (\Exception $e) {
            $this->error("✗ Could not check authentication plugin: " . $e->getMessage());
            return;
        }

        // Test mysqldump after fix
        $this->info("\n4. Testing mysqldump after fix...");
        $mysqldumpPath = $this->findMysqldump();
        if ($mysqldumpPath) {
            $command = "{$mysqldumpPath} --user={$username} --password={$password} --single-transaction --no-data --databases {$database}";

            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode === 0) {
                $this->info("✓ mysqldump authentication successful");
                $this->info("✓ Backup should now work properly");
            } else {
                $this->error("✗ mysqldump still failing (return code: {$returnCode})");
                $this->error("Output: " . implode("\n", array_slice($output, 0, 3)));
            }
        }

        $this->info("\n=== Fix Complete ===");
        $this->info("If mysqldump still fails, try these additional steps:");
        $this->info("1. Restart MySQL: sudo systemctl restart mysql");
        $this->info("2. Check MySQL logs: sudo tail -f /var/log/mysql/error.log");
        $this->info("3. Verify user permissions: mysql -u root -p -e 'SHOW GRANTS FOR root@localhost'");
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
