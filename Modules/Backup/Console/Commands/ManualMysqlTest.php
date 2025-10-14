<?php

namespace Modules\Backup\Console\Commands;

use Illuminate\Console\Command;

class ManualMysqlTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:manual-mysql-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manual MySQL connection test for troubleshooting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Manual MySQL Test ===');

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

        // Test 1: Check if mysqldump exists
        $this->info("\n1. Checking mysqldump availability...");
        $mysqldumpPath = $this->findMysqldump();
        if ($mysqldumpPath) {
            $this->info("✓ mysqldump found at: {$mysqldumpPath}");
        } else {
            $this->error("✗ mysqldump not found");
            return;
        }

        // Test 2: Test different mysqldump commands
        $this->info("\n2. Testing different mysqldump authentication methods...");

        $tests = [
            'Socket authentication' => "{$mysqldumpPath} --user={$username} --password={$password} --single-transaction --no-data --databases {$database}",
            'TCP connection' => "{$mysqldumpPath} --host={$host} --port={$port} --user={$username} --password={$password} --single-transaction --no-data --databases {$database}",
            'No password (socket)' => "{$mysqldumpPath} --user={$username} --single-transaction --no-data --databases {$database}",
            'No password (TCP)' => "{$mysqldumpPath} --host={$host} --port={$port} --user={$username} --single-transaction --no-data --databases {$database}",
        ];

        foreach ($tests as $description => $command) {
            $this->info("\nTesting: {$description}");
            $this->info("Command: " . str_replace($password, '***HIDDEN***', $command));

            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode === 0) {
                $this->info("✓ SUCCESS");
            } else {
                $this->error("✗ FAILED (return code: {$returnCode})");
                $this->error("Output: " . implode("\n", $output));
            }
        }

        // Test 3: Check MySQL service status
        $this->info("\n3. Checking MySQL service status...");
        $output = [];
        $returnCode = 0;
        exec('systemctl status mysql 2>&1', $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✓ MySQL service is running");
        } else {
            $this->warn("⚠ MySQL service status check failed");
            $this->info("Output: " . implode("\n", array_slice($output, 0, 5)));
        }

        // Test 4: Check MySQL socket
        $this->info("\n4. Checking MySQL socket...");
        $socketPaths = [
            '/var/run/mysqld/mysqld.sock',
            '/tmp/mysql.sock',
            '/var/lib/mysql/mysql.sock',
        ];

        foreach ($socketPaths as $socketPath) {
            if (file_exists($socketPath)) {
                $this->info("✓ MySQL socket found: {$socketPath}");
                break;
            }
        }

        // Test 5: Manual connection test
        $this->info("\n5. Manual connection test...");
        $manualCommand = "mysql -u {$username} -p{$password} -e 'SELECT 1 as test' 2>&1";
        $output = [];
        $returnCode = 0;
        exec($manualCommand, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✓ Manual MySQL connection successful");
        } else {
            $this->error("✗ Manual MySQL connection failed");
            $this->error("Output: " . implode("\n", $output));
        }

        $this->info("\n=== Test Complete ===");
        $this->info("If all tests fail, try these solutions:");
        $this->info("1. sudo systemctl restart mysql");
        $this->info("2. Check MySQL user permissions");
        $this->info("3. Verify MySQL is configured to accept connections");
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
