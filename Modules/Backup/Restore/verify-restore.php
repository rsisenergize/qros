<?php
/**
 * Database Restore Verification Script
 * Run this after a restore to verify if the database was actually updated
 */

echo "=== Database Restore Verification ===\n\n";

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

    // Connect to database
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Check 1: Get all tables
    echo "1. Checking Database Tables...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Found " . count($tables) . " tables in database\n\n";

    // Check 2: Check some key tables for data
    $keyTables = ['users', 'restaurants', 'branches', 'menu_items', 'orders'];
    echo "2. Checking Key Tables for Data...\n";

    foreach ($keyTables as $table) {
        if (in_array($table, $tables)) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $result = $stmt->fetch();
            $count = $result['count'];
            echo "✓ Table '$table': $count records\n";
        } else {
            echo "❌ Table '$table': Not found\n";
        }
    }
    echo "\n";

    // Check 3: Check recent activity
    echo "3. Checking Recent Activity...\n";

    // Check users table for recent activity
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
        $result = $stmt->fetch();
        $recentUsers = $result['count'];
        echo "✓ Recent users (last 24h): $recentUsers\n";
    }

    // Check orders table for recent activity
    if (in_array('orders', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
        $result = $stmt->fetch();
        $recentOrders = $result['count'];
        echo "✓ Recent orders (last 24h): $recentOrders\n";
    }
    echo "\n";

    // Check 4: Check backup-related tables
    echo "4. Checking Backup System...\n";

    if (in_array('database_backups', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM database_backups");
        $result = $stmt->fetch();
        $backupCount = $result['count'];
        echo "✓ Database backups table: $backupCount records\n";

        // Get latest backup
        $stmt = $pdo->query("SELECT filename, created_at FROM database_backups ORDER BY created_at DESC LIMIT 1");
        $latestBackup = $stmt->fetch();
        if ($latestBackup) {
            echo "✓ Latest backup: {$latestBackup['filename']} ({$latestBackup['created_at']})\n";
        }
    } else {
        echo "❌ Database backups table: Not found\n";
    }
    echo "\n";

    // Check 5: Check for any obvious signs of restore
    echo "5. Looking for Restore Indicators...\n";

    // Check if there are any test tables from restore
    $stmt = $pdo->query("SHOW TABLES LIKE 'test_restore_%'");
    $testTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($testTables)) {
        echo "⚠️  Found test tables from restore process: " . implode(', ', $testTables) . "\n";
        echo "   These should be cleaned up after restore\n";
    } else {
        echo "✓ No test tables found (good)\n";
    }

    // Check for any temporary files
    $tempDir = sys_get_temp_dir();
    $tempFiles = glob($tempDir . '/decompressed_sql_*');
    if (!empty($tempFiles)) {
        echo "⚠️  Found temporary SQL files: " . count($tempFiles) . " files\n";
    } else {
        echo "✓ No temporary SQL files found (good)\n";
    }
    echo "\n";

    // Check 6: Database size and structure
    echo "6. Database Structure Check...\n";

    $stmt = $pdo->query("SELECT
        table_schema as database_name,
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'database_size_mb'
        FROM information_schema.tables
        WHERE table_schema = '{$dbConfig['database']}'
        GROUP BY table_schema");
    $dbSize = $stmt->fetch();

    if ($dbSize) {
        echo "✓ Database size: {$dbSize['database_size_mb']} MB\n";
    }

    // Check total rows across all tables
    $totalRows = 0;
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $result = $stmt->fetch();
        $totalRows += $result['count'];
    }
    echo "✓ Total records across all tables: " . number_format($totalRows) . "\n";
    echo "\n";

    // Summary
    echo "=== Verification Summary ===\n";
    echo "✓ Database connection: Working\n";
    echo "✓ Tables found: " . count($tables) . "\n";
    echo "✓ Total records: " . number_format($totalRows) . "\n";

    if ($totalRows > 0) {
        echo "✅ Database appears to have data\n";
        echo "   If you expected different data, the restore may not have worked properly\n";
    } else {
        echo "❌ Database appears to be empty\n";
        echo "   The restore definitely did not work\n";
    }

    echo "\nTo verify if restore worked:\n";
    echo "1. Check if your expected data is present\n";
    echo "2. Look for recent timestamps in key tables\n";
    echo "3. Compare with a known good backup\n";

} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
