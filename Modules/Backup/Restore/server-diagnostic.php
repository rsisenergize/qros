<?php

/**
 * Server Diagnostic Script for Ubuntu MySQL Issues
 * Upload this to your Ubuntu server and run it to diagnose the restore issue
 */

echo "=== Ubuntu Server MySQL Diagnostic ===\n\n";

// Get database config from .env
function getDatabaseConfig()
{
    $envFile = __DIR__ . '/../../../.env';
    if (!file_exists($envFile)) {
        throw new Exception('.env file not found');
    }

    $envContent = file_get_contents($envFile);
    $config = [];

    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
            ) {
                $value = substr($value, 1, -1);
            }

            $config[$key] = $value;
        }
    }

    return [
        'host' => $config['DB_HOST'] ?? 'localhost',
        'database' => $config['DB_DATABASE'] ?? '',
        'username' => $config['DB_USERNAME'] ?? '',
        'password' => $config['DB_PASSWORD'] ?? '',
        'port' => intval($config['DB_PORT'] ?? 3306),
    ];
}

try {
    $dbConfig = getDatabaseConfig();

    echo "Database Configuration:\n";
    echo "Host: {$dbConfig['host']}\n";
    echo "Port: {$dbConfig['port']}\n";
    echo "Database: {$dbConfig['database']}\n";
    echo "Username: {$dbConfig['username']}\n";
    echo "Password: " . (empty($dbConfig['password']) ? 'Empty' : 'Set (hidden)') . "\n\n";

    // Test 1: Basic PDO Connection
    echo "1. Testing PDO Connection...\n";
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✓ PDO connection successful\n\n";

    // Test 2: Simple Query
    echo "2. Testing Simple Query...\n";
    $result = $pdo->query('SELECT 1 as test')->fetch();
    echo "✓ Simple query successful: " . $result['test'] . "\n\n";

    // Test 3: Check MySQL User Plugin
    echo "3. Checking MySQL User Authentication Plugin...\n";
    $stmt = $pdo->query("SELECT user, host, plugin FROM mysql.user WHERE user = '{$dbConfig['username']}'");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        echo "User: {$user['user']}@{$user['host']} - Plugin: {$user['plugin']}\n";
        if ($user['plugin'] === 'caching_sha2_password') {
            echo "⚠️  This user is using caching_sha2_password plugin (MySQL 8.0 default)\n";
            echo "   This can cause issues with mysql command-line tool\n\n";
        }
    }

    // Test 4: Try to execute a larger SQL statement
    echo "4. Testing SQL Statement Execution...\n";
    $testSql = "CREATE TABLE IF NOT EXISTS test_restore_diagnostic (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_column VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    try {
        $pdo->exec($testSql);
        echo "✓ SQL statement execution successful\n\n";
    } catch (Exception $e) {
        echo "❌ SQL statement execution failed: " . $e->getMessage() . "\n\n";
    }

    // Test 5: Check if mysql command works
    echo "5. Testing MySQL Command Line Tool...\n";
    $mysqlPath = null;
    $mysqlPaths = [
        '/usr/bin/mysql',
        '/usr/local/bin/mysql',
        '/opt/homebrew/bin/mysql',
        'mysql'
    ];

    foreach ($mysqlPaths as $path) {
        if (is_executable($path) || shell_exec("which $path 2>/dev/null")) {
            $mysqlPath = $path;
            break;
        }
    }

    if ($mysqlPath) {
        echo "MySQL found at: $mysqlPath\n";

        // Test mysql command with password
        $testCommand = "echo 'SELECT 1;' | $mysqlPath -u{$dbConfig['username']} -p{$dbConfig['password']} -h{$dbConfig['host']} -P{$dbConfig['port']} {$dbConfig['database']} 2>&1";
        $output = shell_exec($testCommand);

        if (strpos($output, 'ERROR 1045') !== false) {
            echo "❌ MySQL command failed with authentication error\n";
            echo "   This confirms the MySQL 8.0 authentication issue\n\n";
        } else {
            echo "✓ MySQL command works\n\n";
        }
    } else {
        echo "❌ MySQL command not found\n\n";
    }

    // Test 6: Check for backup files
    echo "6. Checking Backup Files...\n";
    $backupDir = __DIR__ . '/../../../storage/app/backups/';
    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        $backupFiles = array_filter($files, function ($file) use ($backupDir) {
            return $file !== '.' && $file !== '..' && is_file($backupDir . $file);
        });
        echo "Found " . count($backupFiles) . " backup files\n";

        foreach (array_slice($backupFiles, 0, 5) as $file) {
            echo "- $file\n";
        }
        if (count($backupFiles) > 5) {
            echo "... and " . (count($backupFiles) - 5) . " more\n";
        }
    } else {
        echo "❌ Backup directory not found: $backupDir\n";
    }

    echo "\n=== Diagnostic Complete ===\n";
    echo "If you see authentication errors in step 5, the issue is MySQL 8.0's\n";
    echo "caching_sha2_password plugin. The restore should work with the\n";
    echo "Laravel-native approach, but the mysql command-line fallback will fail.\n";
} catch (Exception $e) {
    echo "❌ Diagnostic failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
