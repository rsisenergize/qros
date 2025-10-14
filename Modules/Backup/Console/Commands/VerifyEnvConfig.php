<?php

namespace Modules\Backup\Console\Commands;

use Illuminate\Console\Command;

class VerifyEnvConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:verify-env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify database configuration from .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Database Configuration Verification ===');

        // Get database configuration
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        $this->info("Database driver: " . $config['driver']);
        $this->info("Database host: " . $config['host']);
        $this->info("Database port: " . $config['port']);
        $this->info("Database name: " . $config['database']);
        $this->info("Database username: " . $config['username']);
        $this->info("Database password: " . (empty($config['password']) ? 'Empty' : 'Set (hidden)'));

        // Check if .env file exists
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $this->info("✓ .env file exists");

            // Read .env file directly to verify
            $envContent = file_get_contents($envFile);
            $envLines = explode("\n", $envContent);

            $dbHost = null;
            $dbPort = null;
            $dbDatabase = null;
            $dbUsername = null;
            $dbPassword = null;

            foreach ($envLines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;

                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove quotes if present
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
                    ) {
                        $value = substr($value, 1, -1);
                    }

                    switch ($key) {
                        case 'DB_HOST':
                            $dbHost = $value;
                            break;
                        case 'DB_PORT':
                            $dbPort = $value;
                            break;
                        case 'DB_DATABASE':
                            $dbDatabase = $value;
                            break;
                        case 'DB_USERNAME':
                            $dbUsername = $value;
                            break;
                        case 'DB_PASSWORD':
                            $dbPassword = $value;
                            break;
                    }
                }
            }

            $this->info("\nDirect .env file values:");
            $this->info("DB_HOST: " . ($dbHost ?: 'Not set'));
            $this->info("DB_PORT: " . ($dbPort ?: 'Not set'));
            $this->info("DB_DATABASE: " . ($dbDatabase ?: 'Not set'));
            $this->info("DB_USERNAME: " . ($dbUsername ?: 'Not set'));
            $this->info("DB_PASSWORD: " . (empty($dbPassword) ? 'Empty' : 'Set (hidden)'));

            // Compare with config values
            $this->info("\nConfiguration comparison:");
            $this->info("Host match: " . ($config['host'] === $dbHost ? '✓' : '✗'));
            $this->info("Port match: " . ($config['port'] == $dbPort ? '✓' : '✗'));
            $this->info("Database match: " . ($config['database'] === $dbDatabase ? '✓' : '✗'));
            $this->info("Username match: " . ($config['username'] === $dbUsername ? '✓' : '✗'));
            $this->info("Password match: " . (empty($config['password']) === empty($dbPassword) ? '✓' : '✗'));
        } else {
            $this->error("✗ .env file not found");
        }

        // Test database connection
        $this->info("\nTesting database connection...");
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->query('SELECT 1 as test');
            $result = $stmt->fetch();

            if ($result && $result['test'] == 1) {
                $this->info("✓ Database connection successful");
            } else {
                $this->error("✗ Database connection failed - unexpected result");
            }
        } catch (\Exception $e) {
            $this->error("✗ Database connection failed: " . $e->getMessage());
        }

        $this->info("\n=== Verification Complete ===");
    }
}
