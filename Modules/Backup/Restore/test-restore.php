<?php

/**
 * Test Restore Process
 * This script tests the actual restore process with the compressed SQL file
 */

echo "=== Test Restore Process ===\n\n";

// Include the restore functions
require_once __DIR__ . '/restore-backup.php';

try {
    // Get database config
    $dbConfig = getDatabaseConfig();

    echo "Database: {$dbConfig['database']}\n";
    echo "Host: {$dbConfig['host']}:{$dbConfig['port']}\n\n";

    // Find the backup file
    $backupDir = __DIR__ . '/../../../storage/app/backups/';
    $backupFiles = glob($backupDir . 'combined_backup_*.tar.gz');

    if (empty($backupFiles)) {
        echo "❌ No backup files found\n";
        exit;
    }

    $backupFile = $backupFiles[0];
    echo "✓ Using backup file: " . basename($backupFile) . "\n\n";

    // Test 1: Extract the backup
    echo "1. Testing Backup Extraction...\n";
    $projectRoot = __DIR__ . '/../../../';

    // Extract to project root
    $extractCommand = "cd " . escapeshellarg($projectRoot) . " && tar -xzf " . escapeshellarg($backupFile) . " 2>&1";
    $extractOutput = shell_exec($extractCommand);

    if ($extractOutput) {
        echo "⚠️  Extract warnings: $extractOutput\n";
    } else {
        echo "✓ Extraction completed\n";
    }

    // Test 2: Look for the database file
    echo "\n2. Looking for Database File...\n";
    $compressedSqlFiles = glob($projectRoot . 'database_backup_*.sql.gz');
    $uncompressedSqlFiles = glob($projectRoot . 'database_backup_*.sql');

    echo "Compressed SQL files found: " . count($compressedSqlFiles) . "\n";
    foreach ($compressedSqlFiles as $file) {
        echo "  - " . basename($file) . " (" . number_format(filesize($file) / 1024, 2) . " KB)\n";
    }

    echo "Uncompressed SQL files found: " . count($uncompressedSqlFiles) . "\n";
    foreach ($uncompressedSqlFiles as $file) {
        echo "  - " . basename($file) . " (" . number_format(filesize($file) / 1024, 2) . " KB)\n";
    }

    // Test 3: Test database connection
    echo "\n3. Testing Database Connection...\n";
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

    // Test 4: Test restore from compressed file
    if (!empty($compressedSqlFiles)) {
        echo "\n4. Testing Restore from Compressed File...\n";
        $sqlFile = $compressedSqlFiles[0];
        echo "✓ Testing restore from: " . basename($sqlFile) . "\n";

        // Test the restoreFromGzip function
        $result = restoreFromGzip($sqlFile, $dbConfig);
        echo "✓ Restore result: " . ($result['success'] ? "SUCCESS" : "FAILED") . "\n";
        echo "✓ Message: " . $result['message'] . "\n";

        // Check if data changed
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $newResult = $stmt->fetch();
        echo "✓ Users count after restore: " . $newResult['count'] . "\n";

        if ($newResult['count'] != $result['count']) {
            echo "✓ Database was updated!\n";
        } else {
            echo "⚠️  Database count unchanged (may be same data)\n";
        }
    } else {
        echo "\n4. No compressed SQL files found to test\n";
    }

    // Cleanup
    echo "\n5. Cleaning Up...\n";
    $filesToClean = array_merge($compressedSqlFiles, $uncompressedSqlFiles);
    foreach ($filesToClean as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "✓ Cleaned up: " . basename($file) . "\n";
        }
    }

    // Also clean up any nested files backup
    $nestedFiles = glob($projectRoot . 'files_backup_*.tar.gz');
    foreach ($nestedFiles as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "✓ Cleaned up: " . basename($file) . "\n";
        }
    }

    echo "\n=== Test Complete ===\n";
    echo "This test verifies that the restore process can find and process\n";
    echo "the compressed SQL file from the backup.\n";
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
