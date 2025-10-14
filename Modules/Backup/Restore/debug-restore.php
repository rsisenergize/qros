<?php
/**
 * Debug Restore Process Script
 * This script will trace exactly what happens during the restore process
 */

echo "=== Debug Restore Process ===\n\n";

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

    echo "Database: {$dbConfig['database']}\n";
    echo "Host: {$dbConfig['host']}:{$dbConfig['port']}\n\n";

    // Step 1: Check what backup files are available
    echo "1. Checking Available Backup Files...\n";
    $backupDir = __DIR__ . '/../../../storage/app/backups/';
    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        $backupFiles = array_filter($files, function($file) use ($backupDir) {
            return $file !== '.' && $file !== '..' && is_file($backupDir . $file);
        });

        echo "Found " . count($backupFiles) . " files in backup directory:\n";
        foreach ($backupFiles as $file) {
            $filePath = $backupDir . $file;
            $size = filesize($filePath);
            $modified = date('Y-m-d H:i:s', filemtime($filePath));
            echo "- $file ($size bytes, modified: $modified)\n";
        }
    } else {
        echo "❌ Backup directory not found: $backupDir\n";
    }
    echo "\n";

    // Step 2: Check if there are any extracted files from recent restore
    echo "2. Checking for Extracted Files...\n";
    $projectRoot = __DIR__ . '/../../../';

    // Look for SQL files that might have been extracted
    $sqlFiles = glob($projectRoot . '*.sql');
    $sqlGzFiles = glob($projectRoot . '*.sql.gz');
    $databaseFiles = glob($projectRoot . 'database*.sql');
    $backupFiles = glob($projectRoot . 'backup*.sql');

    echo "SQL files in project root:\n";
    foreach ($sqlFiles as $file) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "- " . basename($file) . " ($size bytes, modified: $modified)\n";
    }

    echo "Compressed SQL files:\n";
    foreach ($sqlGzFiles as $file) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "- " . basename($file) . " ($size bytes, modified: $modified)\n";
    }

    echo "Database backup files:\n";
    foreach ($databaseFiles as $file) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "- " . basename($file) . " ($size bytes, modified: $modified)\n";
    }
    echo "\n";

    // Step 3: Check temporary directory for extracted files
    echo "3. Checking Temporary Directory...\n";
    $tempDir = sys_get_temp_dir();
    $tempFiles = glob($tempDir . '/decompressed_sql_*');
    $tempConfigFiles = glob($tempDir . '/mysql_config_*');

    echo "Temporary SQL files:\n";
    foreach ($tempFiles as $file) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "- " . basename($file) . " ($size bytes, modified: $modified)\n";
    }

    echo "Temporary config files:\n";
    foreach ($tempConfigFiles as $file) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "- " . basename($file) . " ($size bytes, modified: $modified)\n";
    }
    echo "\n";

    // Step 4: Test database connection
    echo "4. Testing Database Connection...\n";
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✓ Database connection successful\n";

    // Check current data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✓ Current users count: " . $result['count'] . "\n";
    echo "\n";

    // Step 5: Simulate the restore process with a test
    echo "5. Testing SQL Execution...\n";

    // Create a test table to see if SQL execution works
    $testTableName = 'test_restore_debug_' . time();
    $createTableSql = "CREATE TABLE IF NOT EXISTS `$testTableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_column VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    try {
        $pdo->exec($createTableSql);
        echo "✓ SQL execution test successful\n";

        // Clean up test table
        $pdo->exec("DROP TABLE IF EXISTS `$testTableName`");
        echo "✓ Test table cleaned up\n";
    } catch (Exception $e) {
        echo "❌ SQL execution test failed: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Step 6: Check for any error logs
    echo "6. Checking Error Logs...\n";
    $logFiles = [
        '/var/log/apache2/error.log',
        '/var/log/nginx/error.log',
        '/var/log/mysql/error.log',
        __DIR__ . '/../../../storage/logs/laravel.log'
    ];

    foreach ($logFiles as $logFile) {
        if (file_exists($logFile)) {
            $lastLines = shell_exec("tail -n 20 " . escapeshellarg($logFile) . " 2>/dev/null");
            if (!empty($lastLines)) {
                echo "Recent entries in " . basename($logFile) . ":\n";
                echo $lastLines . "\n";
            }
        }
    }
    echo "\n";

    // Step 7: Check PHP error log
    echo "7. Checking PHP Error Log...\n";
    $phpErrorLog = ini_get('error_log');
    if ($phpErrorLog && file_exists($phpErrorLog)) {
        $lastLines = shell_exec("tail -n 10 " . escapeshellarg($phpErrorLog) . " 2>/dev/null");
        if (!empty($lastLines)) {
            echo "Recent PHP errors:\n";
            echo $lastLines . "\n";
        }
    } else {
        echo "PHP error log not found or not configured\n";
    }
    echo "\n";

    echo "=== Debug Complete ===\n";
    echo "This information will help identify why the restore is not working.\n";
    echo "Look for:\n";
    echo "1. Missing SQL files after extraction\n";
    echo "2. SQL execution errors\n";
    echo "3. File permission issues\n";
    echo "4. Database connection problems\n";

} catch (Exception $e) {
    echo "❌ Debug failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
