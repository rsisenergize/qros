<?php

/**
 * Test TAR.GZ Extraction Script
 * This script will manually extract a backup file and show exactly what's inside
 */

echo "=== Test TAR.GZ Extraction ===\n\n";

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

    // Step 1: Find the backup file
    echo "1. Finding Backup File...\n";
    $backupDir = __DIR__ . '/../../../storage/app/backups/';
    $backupFiles = glob($backupDir . 'combined_backup_*.tar.gz');

    if (empty($backupFiles)) {
        echo "❌ No combined backup files found\n";
        exit;
    }

    $backupFile = $backupFiles[0]; // Use the most recent one
    echo "✓ Found backup file: " . basename($backupFile) . "\n";
    echo "✓ File size: " . number_format(filesize($backupFile) / 1024 / 1024, 2) . " MB\n\n";

    // Step 2: List contents of TAR.GZ without extracting
    echo "2. Listing TAR.GZ Contents...\n";
    $projectRoot = __DIR__ . '/../../../';
    $command = "cd " . escapeshellarg($projectRoot) . " && tar -tzf " . escapeshellarg($backupFile) . " 2>&1";
    $output = shell_exec($command);

    if ($output) {
        echo "✓ TAR.GZ contents:\n";
        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (!empty($line)) {
                echo "  - $line\n";
            }
        }
    } else {
        echo "❌ Failed to list TAR.GZ contents\n";
    }
    echo "\n";

    // Step 3: Extract to a temporary directory
    echo "3. Extracting to Temporary Directory...\n";
    $tempDir = sys_get_temp_dir() . '/restore_test_' . time();
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    $extractCommand = "cd " . escapeshellarg($tempDir) . " && tar -xzf " . escapeshellarg($backupFile) . " 2>&1";
    $extractOutput = shell_exec($extractCommand);

    if (is_dir($tempDir)) {
        echo "✓ Extracted to: $tempDir\n";

        // List all extracted files
        $extractedFiles = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($tempDir . '/', '', $file->getPathname());
                $extractedFiles[] = $relativePath;
            }
        }

        echo "✓ Found " . count($extractedFiles) . " extracted files:\n";
        foreach (array_slice($extractedFiles, 0, 20) as $file) {
            echo "  - $file\n";
        }
        if (count($extractedFiles) > 20) {
            echo "  ... and " . (count($extractedFiles) - 20) . " more files\n";
        }

        // Look for SQL files specifically
        $sqlFiles = array_filter($extractedFiles, function ($file) {
            return strpos($file, '.sql') !== false;
        });

        echo "\n✓ SQL files found: " . count($sqlFiles) . "\n";
        foreach ($sqlFiles as $file) {
            echo "  - $file\n";
        }

        // Look for database files specifically
        $databaseFiles = array_filter($extractedFiles, function ($file) {
            return strpos($file, 'database') !== false || strpos($file, 'backup') !== false;
        });

        echo "\n✓ Database-related files found: " . count($databaseFiles) . "\n";
        foreach ($databaseFiles as $file) {
            echo "  - $file\n";
        }
    } else {
        echo "❌ Failed to extract TAR.GZ\n";
        echo "Extract output: $extractOutput\n";
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
    echo "✓ Current users count: " . $result['count'] . "\n\n";

    // Step 5: If SQL files were found, test restoring one
    if (!empty($sqlFiles)) {
        echo "5. Testing SQL File Restore...\n";
        $firstSqlFile = $tempDir . '/' . $sqlFiles[0];

        if (file_exists($firstSqlFile) && is_file($firstSqlFile)) {
            echo "✓ Testing restore from: " . basename($sqlFiles[0]) . "\n";
            echo "✓ File size: " . number_format(filesize($firstSqlFile) / 1024, 2) . " KB\n";

            // Check if it's a compressed file
            if (strpos($firstSqlFile, '.gz') !== false) {
                echo "✓ File is compressed (.gz)\n";

                // Test decompression
                $decompressedContent = shell_exec("gunzip -c " . escapeshellarg($firstSqlFile) . " 2>/dev/null");
                if ($decompressedContent) {
                    $firstLines = implode("\n", array_slice(explode("\n", $decompressedContent), 0, 10));
                    echo "✓ Decompressed content preview (first 10 lines):\n";
                    echo $firstLines . "\n\n";

                    // Test if it's a valid SQL file
                    if (strpos($decompressedContent, 'CREATE TABLE') !== false || strpos($decompressedContent, 'INSERT INTO') !== false) {
                        echo "✓ File appears to be a valid SQL backup\n";
                    } else {
                        echo "❌ File doesn't appear to be a valid SQL backup\n";
                    }
                } else {
                    echo "❌ Failed to decompress file\n";
                }
            } else {
                // Read first few lines to see content
                $content = file_get_contents($firstSqlFile);
                $firstLines = implode("\n", array_slice(explode("\n", $content), 0, 10));
                echo "✓ First 10 lines:\n";
                echo $firstLines . "\n\n";

                // Test if it's a valid SQL file
                if (strpos($content, 'CREATE TABLE') !== false || strpos($content, 'INSERT INTO') !== false) {
                    echo "✓ File appears to be a valid SQL backup\n";
                } else {
                    echo "❌ File doesn't appear to be a valid SQL backup\n";
                }
            }
        } else {
            echo "❌ SQL file not found at expected location or is not a file\n";
        }
    } else {
        echo "5. No SQL files found to test\n";
    }

    // Cleanup
    if (is_dir($tempDir)) {
        shell_exec("rm -rf " . escapeshellarg($tempDir));
        echo "\n✓ Cleaned up temporary directory\n";
    }

    echo "\n=== Test Complete ===\n";
    echo "This test shows exactly what's inside the backup file and whether\n";
    echo "the SQL files are being extracted properly.\n";
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
