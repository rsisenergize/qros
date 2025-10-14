<?php

/**
 * Emergency Database Backup Restore Tool - Simplified Version
 *
 * This file allows you to restore a database backup when you can't access the admin panel.
 * Access it via: yourdomain.com/backup-and-restore/
 *
 * SECURITY WARNING: Remove this file after use or protect it with authentication!
 */

// S3 API helper functions
function s3Request($method, $bucket, $key = '', $region = 'us-east-1', $accessKey = '', $secretKey = '', $data = null)
{
    $url = "https://{$bucket}.s3.{$region}.amazonaws.com";
    if (!empty($key)) {
        $url .= "/{$key}";
    }

    $headers = [
        'Host: ' . $bucket . '.s3.' . $region . '.amazonaws.com',
        'Date: ' . gmdate('D, d M Y H:i:s T'),
    ];

    if ($data !== null) {
        $headers[] = 'Content-Length: ' . strlen($data);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'body' => $response
    ];
}

/**
 * Generate S3 signature for authentication
 */
function generateS3Signature($stringToSign, $secretKey)
{
    return base64_encode(hash_hmac('sha1', $stringToSign, $secretKey, true));
}

/**
 * Create S3 canonical request
 */
function createS3CanonicalRequest($method, $uri, $queryString, $headers, $payload = '')
{
    $canonicalHeaders = '';
    $signedHeaders = '';

    ksort($headers);
    foreach ($headers as $key => $value) {
        $canonicalHeaders .= strtolower($key) . ':' . trim($value) . "\n";
        $signedHeaders .= strtolower($key) . ';';
    }
    $signedHeaders = rtrim($signedHeaders, ';');

    $payloadHash = hash('sha256', $payload);

    return $method . "\n" . $uri . "\n" . $queryString . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . $payloadHash;
}

/**
 * Create S3 string to sign
 */
function createS3StringToSign($algorithm, $datetime, $credentialScope, $canonicalRequest)
{
    return $algorithm . "\n" . $datetime . "\n" . $credentialScope . "\n" . hash('sha256', $canonicalRequest);
}

/**
 * Calculate S3 signature
 */
function calculateS3Signature($stringToSign, $secretKey, $date, $region)
{
    $dateKey = hash_hmac('sha256', $date, 'AWS4' . $secretKey, true);
    $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
    $dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);
    $signingKey = hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);
    return hash_hmac('sha256', $stringToSign, $signingKey);
}

/**
 * Simple S3 authentication using pre-signed URLs
 */
function getS3BackupsSimple($searchTerm = '', $storageConfig = null)
{
    $backupFiles = [];

    try {
        if (!$storageConfig) {
            error_log("No S3 configuration provided");
            return [];
        }

        error_log("Storage Config received: " . json_encode($storageConfig));

        $authKeys = $storageConfig['auth_keys'] ?? $storageConfig['config'] ?? [];

        error_log("Auth Keys extracted: " . json_encode(array_keys($authKeys)));

        if (empty($authKeys)) {
            error_log("No S3 authentication keys found");
            return [];
        }

        $bucket = trim($authKeys['bucket']);
        $region = trim($authKeys['region'] ?? 'us-east-1');
        $prefix = trim($authKeys['prefix'] ?? 'backups/');

        error_log("S3 Bucket: " . $bucket);
        error_log("S3 Region: " . $region);
        error_log("S3 Prefix: " . $prefix);

        // For now, let's try a simpler approach - just list the bucket contents
        // This will work if the bucket has public read access or if we can use a simpler auth method
        $url = "https://{$bucket}.s3.{$region}.amazonaws.com/?list-type=2&prefix=" . urlencode($prefix);

        error_log("Simple S3 URL: " . $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: ' . $bucket . '.s3.' . $region . '.amazonaws.com',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        error_log("Simple S3 cURL Error: " . $error);
        error_log("Simple S3 HTTP Response Code: " . $httpCode);
        error_log("Simple S3 Response: " . substr($response, 0, 500));

        if ($httpCode !== 200) {
            error_log("Simple S3 request failed with HTTP code: " . $httpCode);
            error_log("Simple S3 Error Response: " . $response);
            return [];
        }

        // Parse XML response
        $xml = simplexml_load_string($response);
        if (!$xml) {
            error_log("Failed to parse S3 XML response");
            return [];
        }

        foreach ($xml->Contents as $object) {
            $key = (string)$object->Key;
            $filename = basename($key);

            if (isBackupFile($filename)) {
                if (!empty($searchTerm) && stripos($filename, $searchTerm) === false) {
                    continue;
                }

                $backupFiles[] = [
                    'filename' => $filename,
                    'path' => $key,
                    'size' => (int)$object->Size,
                    'modified' => (string)$object->LastModified,
                    'storage_type' => 's3',
                    'bucket' => $bucket,
                    'key' => $key
                ];
            }
        }

        usort($backupFiles, function ($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        error_log("Total S3 backup files found (simple): " . count($backupFiles));
        return $backupFiles;
    } catch (Exception $e) {
        error_log("Error getting S3 backups (simple): " . $e->getMessage());
        return [];
    }
}

// Configuration
$maxExecutionTime = 300; // 5 minutes
$memoryLimit = '512M';
$authRequired = true;
$sessionName = 'restore_auth';

// Set execution limits
set_time_limit($maxExecutionTime);
ini_set('memory_limit', $memoryLimit);

// Allowed hosts for access control
$allowedHosts = [
    'localhost',
    '127.0.0.1',
    '::1',
    'localhost:8000',
    'localhost:3000',
    'localhost:8080',
    '127.0.0.1:8000',
    '127.0.0.1:3000',
    '127.0.0.1:8080',
];

// MySQL executable paths
$mysqlPaths = [
    '/opt/homebrew/opt/mysql@8.0/bin/mysql',
    '/usr/local/bin/mysql',
    '/usr/bin/mysql',
    '/opt/homebrew/bin/mysql',
    '/opt/mysql/bin/mysql',
    'mysql', // Try PATH as fallback
];

// Backup configuration
$backupExtensions = ['sql', 'zip', 'gz', 'tar.gz'];
$possibleDbLocations = [
    'database/backup_*.sql',
    'database/database_backup_*.sql',
    'database/database.sql',
    'database.sql',
    'backup.sql',
    'db.sql',
    'database_backup_*.sql',  // Add this pattern for the actual file structure
    'backup_*.sql',           // Add this pattern as fallback
    'database/backup_*.sql.gz',  // Compressed SQL files
    'database/database_backup_*.sql.gz',  // Compressed SQL files
    'database_backup_*.sql.gz',  // Compressed SQL files in root
    'backup_*.sql.gz',           // Compressed SQL files in root
];

/**
 * Get backup directory based on database settings
 */
function getBackupDirectory()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            // Fallback to local storage if table doesn't exist
            return __DIR__ . '/../../../storage/app/backups/';
        }

        // Get backup settings
        $stmt = $pdo->query("SELECT storage_location FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        if (!$settings || $settings['storage_location'] === 'local') {
            return __DIR__ . '/../../../storage/app/backups/';
        } elseif ($settings['storage_location'] === 'storage_setting') {
            // Get storage setting from config
            $storageConfig = getStorageConfig();
            if ($storageConfig && isset($storageConfig['backup_path'])) {
                return rtrim($storageConfig['backup_path'], '/') . '/';
            } else {
                // Fallback to local storage
                return __DIR__ . '/../../../storage/app/backups/';
            }
        }

        // Default fallback
        return __DIR__ . '/../../../storage/app/backups/';
    } catch (Exception $e) {
        error_log("Error getting backup directory: " . $e->getMessage());
        // Fallback to local storage
        return __DIR__ . '/../../../storage/app/backups/';
    }
}

/**
 * Get backup directory for display purposes (clean path)
 */
function getBackupDirectoryDisplay()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            // Fallback to local storage display
            $backupDir = getBackupDirectory();
            $realPath = realpath($backupDir);
            return $realPath ? $realPath . '/' : $backupDir;
        }

        // Get backup settings with storage_config
        $stmt = $pdo->query("SELECT storage_location, storage_config FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        if (!$settings || $settings['storage_location'] === 'local') {
            // Local storage - show actual path
            $backupDir = getBackupDirectory();
            $realPath = realpath($backupDir);
            return $realPath ? $realPath . '/' : $backupDir;
        } elseif ($settings['storage_location'] === 'storage_setting') {
            // Check if storage_config column exists and has data
            if (isset($settings['storage_config']) && !empty($settings['storage_config'])) {
                $storageConfig = json_decode($settings['storage_config'], true);
                if ($storageConfig && isset($storageConfig['filesystem'])) {
                    return 'Cloud Storage (' . ucfirst($storageConfig['filesystem']) . ')';
                }
            }

            // Fallback to getting from file_storage_settings
            $storageConfig = getStorageConfig();
            if ($storageConfig && isset($storageConfig['driver'])) {
                return 'Cloud Storage (' . ucfirst($storageConfig['driver']) . ')';
            } else {
                return 'Cloud Storage (Unknown)';
            }
        }

        // Default fallback
        $backupDir = getBackupDirectory();
        $realPath = realpath($backupDir);
        return $realPath ? $realPath . '/' : $backupDir;
    } catch (Exception $e) {
        error_log("Error getting backup directory display: " . $e->getMessage());
        // Fallback to local storage display
        $backupDir = getBackupDirectory();
        $realPath = realpath($backupDir);
        return $realPath ? $realPath . '/' : $backupDir;
    }
}

/**
 * Get storage configuration from database_backup_settings
 */
function getStorageConfig()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Check if storage_config column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM database_backup_settings LIKE 'storage_config'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Get backup settings with storage_config
        $stmt = $pdo->query("SELECT storage_location, storage_config FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        if ($settings && $settings['storage_location'] === 'storage_setting' && !empty($settings['storage_config'])) {
            $storageConfig = json_decode($settings['storage_config'], true);
            if ($storageConfig && isset($storageConfig['auth_keys'])) {
                return [
                    'driver' => $storageConfig['filesystem'] ?? 'local',
                    'auth_keys' => $storageConfig['auth_keys'],
                    'status' => $storageConfig['status'] ?? 'unknown'
                ];
            }
        }

        return null;
    } catch (Exception $e) {
        error_log("Error getting storage config: " . $e->getMessage());
        return null;
    }
}

/**
 * Get detailed storage configuration from backup settings
 */
function getBackupStorageConfig()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Check if storage_config column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM database_backup_settings LIKE 'storage_config'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Get backup settings with storage_config
        $stmt = $pdo->query("SELECT storage_location, storage_config FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        if ($settings && $settings['storage_location'] === 'storage_setting' && !empty($settings['storage_config'])) {
            $storageConfig = json_decode($settings['storage_config'], true);
            if ($storageConfig) {
                return [
                    'source' => 'backup_settings',
                    'filesystem' => $storageConfig['filesystem'] ?? 'unknown',
                    'auth_keys' => $storageConfig['auth_keys'] ?? [],
                    'status' => $storageConfig['status'] ?? 'unknown'
                ];
            }
        }

        // Fallback to file_storage_settings
        $fileStorageConfig = getStorageConfig();
        if ($fileStorageConfig) {
            return [
                'source' => 'file_storage_settings',
                'filesystem' => $fileStorageConfig['driver'] ?? 'unknown',
                'config' => $fileStorageConfig['config'] ?? [],
                'backup_path' => $fileStorageConfig['backup_path'] ?? null
            ];
        }

        return null;
    } catch (Exception $e) {
        error_log("Error getting backup storage config: " . $e->getMessage());
        return null;
    }
}

/**
 * Get backup settings information
 */
function getBackupSettingsInfo()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            return [
                'table_exists' => false,
                'storage_location' => 'local',
                'is_enabled' => false
            ];
        }

        // Get backup settings
        $stmt = $pdo->query("SELECT * FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        if ($settings) {
            return [
                'table_exists' => true,
                'storage_location' => $settings['storage_location'] ?? 'local',
                'is_enabled' => (bool)($settings['is_enabled'] ?? false),
                'frequency' => $settings['frequency'] ?? 'daily',
                'backup_time' => $settings['backup_time'] ?? '02:00:00',
                'retention_days' => $settings['retention_days'] ?? 30,
                'max_backups' => $settings['max_backups'] ?? 10
            ];
        }

        return [
            'table_exists' => true,
            'storage_location' => 'local',
            'is_enabled' => false
        ];
    } catch (Exception $e) {
        error_log("Error getting backup settings: " . $e->getMessage());
        return [
            'table_exists' => false,
            'storage_location' => 'local',
            'is_enabled' => false
        ];
    }
}

/**
 * Start session for authentication
 */
function startAuthSession()
{
    global $sessionName;
    if (session_status() === PHP_SESSION_NONE) {
        session_name($sessionName);
        session_start();
    }
}

/**
 * Check if user is authenticated
 */
function isAuthenticated()
{
    startAuthSession();
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

/**
 * Authenticate user against database
 */
function authenticateUser($username, $password)
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ? AND restaurant_id IS NULL");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            startAuthSession();
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user
 */
function logoutUser()
{
    startAuthSession();
    session_destroy();
}

/**
 * Check if running from CLI
 */
function is_cli()
{
    return php_sapi_name() === 'cli';
}

/**
 * Check if running from localhost or allowed environment
 */
function is_allowed_access()
{
    global $allowedHosts;

    if (is_cli()) return true;

    $host = $_SERVER['HTTP_HOST'] ?? '';
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

    if (in_array($host, $allowedHosts) || in_array($serverName, $allowedHosts)) return true;
    if (in_array($remoteAddr, ['127.0.0.1', '::1'])) return true;
    if (strpos($host, 'localhost') !== false) return true;

    return false;
}

/**
 * Get database configuration from .env file
 */
function getDatabaseConfig()
{
    $envFile = __DIR__ . '/../../../.env';
    if (!file_exists($envFile)) {
        throw new Exception('.env file not found at: ' . $envFile);
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

    $dbConfig = [
        'host' => $config['DB_HOST'] ?? 'localhost',
        'database' => $config['DB_DATABASE'] ?? '',
        'username' => $config['DB_USERNAME'] ?? '',
        'password' => $config['DB_PASSWORD'] ?? '',
        'port' => intval($config['DB_PORT'] ?? 3306),
    ];

    if (empty($dbConfig['database'])) throw new Exception('DB_DATABASE not found in .env file');
    if (empty($dbConfig['username'])) throw new Exception('DB_USERNAME not found in .env file');

    return $dbConfig;
}

/**
 * Find mysql executable
 */
function findMysql()
{
    global $mysqlPaths;
    foreach ($mysqlPaths as $path) {
        if (is_executable($path) || isCommandAvailable($path)) {
            return $path;
        }
    }
    return null;
}

/**
 * Check if a command is available
 */
function isCommandAvailable($command)
{
    $output = [];
    $returnCode = 0;
    exec("which $command 2>/dev/null", $output, $returnCode);
    return $returnCode === 0 && !empty($output);
}

/**
 * Test database connection
 */
function testDatabaseConnection()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->query('SELECT 1');
        return ['success' => true, 'message' => 'Database connection successful using .env configuration'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()];
    }
}

/**
 * Restore from SQL file using Laravel-native PDO approach
 */
function restoreFromSql($backupFile, $dbConfig)
{
    try {
        // First try Laravel-native approach (most reliable)
        error_log("Restore: Attempting Laravel-native SQL restore...");

        // Connect using PDO (which we know works)
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        error_log("Restore: Attempting PDO connection with DSN: " . str_replace($dbConfig['password'], '***HIDDEN***', $dsn));

        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Test the connection
        $testResult = $pdo->query('SELECT 1')->fetch();
        error_log("Restore: PDO connection test successful");

        // Read the SQL file
        if (!file_exists($backupFile)) {
            error_log("Restore: SQL file not found: " . $backupFile);
            return ['success' => false, 'message' => 'SQL backup file not found: ' . $backupFile];
        }

        error_log("Restore: SQL file found, size: " . filesize($backupFile) . " bytes");
        $sqlContent = file_get_contents($backupFile);
        if (empty($sqlContent)) {
            error_log("Restore: SQL file is empty");
            return ['success' => false, 'message' => 'SQL backup file is empty'];
        }

        error_log("Restore: SQL content length: " . strlen($sqlContent) . " characters");
        error_log("Restore: SQL content preview: " . substr($sqlContent, 0, 200) . "...");

        // Split SQL into individual statements with better parsing
        $statements = [];
        $currentStatement = '';
        $lines = explode("\n", $sqlContent);
        $inMultiLineComment = false;

        foreach ($lines as $line) {
            $line = trim($line);

            // Handle multi-line comments
            if (strpos($line, '/*') !== false) {
                $inMultiLineComment = true;
            }
            if (strpos($line, '*/') !== false) {
                $inMultiLineComment = false;
                continue;
            }
            if ($inMultiLineComment) {
                continue;
            }

            // Skip single-line comments and empty lines
            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '#') === 0) {
                continue;
            }

            $currentStatement .= $line . "\n";

            // If line ends with semicolon, it's a complete statement
            if (substr($line, -1) === ';') {
                $statement = trim($currentStatement);
                if (!empty($statement)) {
                    $statements[] = $statement;
                }
                $currentStatement = '';
            }
        }

        // Add any remaining statement
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        error_log("Restore: Parsed " . count($statements) . " SQL statements");

        error_log("Restore: Found " . count($statements) . " SQL statements to execute");

        // Disable foreign key checks to avoid constraint issues
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            error_log("Restore: Disabled foreign key checks");
        } catch (Exception $e) {
            error_log("Restore: Warning - Could not disable foreign key checks: " . $e->getMessage());
        }

        // Execute statements without transaction for better compatibility
        $executedCount = 0;
        $failedStatements = [];

        try {
            foreach ($statements as $index => $statement) {
                if (!empty(trim($statement))) {
                    try {
                        // Skip problematic DROP statements to avoid foreign key issues
                        if (strpos($statement, 'DROP TABLE IF EXISTS') !== false) {
                            error_log("Restore: Skipping DROP statement " . ($index + 1) . " to avoid foreign key issues");
                            continue;
                        }

                        // Clean up problematic INSERT statements
                        if (strpos($statement, 'INSERT INTO') !== false) {
                            // Remove any trailing commas or syntax issues
                            $statement = rtrim($statement, ',');
                            $statement = rtrim($statement, ';');
                            $statement .= ';';

                            // Fix common INSERT syntax issues
                            $statement = preg_replace('/,\s*\)/', ')', $statement); // Remove trailing comma before closing parenthesis
                            $statement = preg_replace('/,\s*;/', ';', $statement); // Remove trailing comma before semicolon
                            $statement = preg_replace('/\s+/', ' ', $statement); // Normalize whitespace
                        }

                        // Skip empty or invalid statements
                        if (empty(trim($statement)) || strlen(trim($statement)) < 10) {
                            continue;
                        }

                        // Validate statement ends with semicolon
                        if (!str_ends_with(trim($statement), ';')) {
                            $statement = trim($statement) . ';';
                        }

                        $pdo->exec($statement);
                        $executedCount++;

                        // Log progress every 50 statements
                        if ($executedCount % 50 === 0) {
                            error_log("Restore: Executed " . $executedCount . " statements");
                        }
                    } catch (Exception $e) {
                        $failedStatements[] = [
                            'index' => $index,
                            'statement' => substr($statement, 0, 100) . '...',
                            'error' => $e->getMessage()
                        ];
                        error_log("Restore: Failed to execute statement " . ($index + 1) . ": " . $e->getMessage());

                        // Continue with other statements instead of failing completely
                        continue;
                    }
                }
            }

            // Re-enable foreign key checks
            try {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                error_log("Restore: Re-enabled foreign key checks");
            } catch (Exception $e) {
                error_log("Restore: Warning - Could not re-enable foreign key checks: " . $e->getMessage());
            }

            if (!empty($failedStatements)) {
                error_log("Restore: " . count($failedStatements) . " statements failed out of " . count($statements));
            }

            error_log("Restore: Laravel-native restore completed successfully");
            $message = "Database restored successfully using Laravel-native approach. ";
            $message .= "Executed: $executedCount statements. ";
            if (!empty($failedStatements)) {
                $message .= "Failed: " . count($failedStatements) . " statements (mostly due to foreign key constraints, which is normal).";
            } else {
                $message .= "All statements executed successfully.";
            }
            return ['success' => true, 'message' => $message];
        } catch (Exception $e) {
            error_log("Restore: Laravel-native restore failed: " . $e->getMessage());
            error_log("Restore: Exception details: " . $e->getTraceAsString());

            // Don't fall back to mysql command-line since it's known to fail on Ubuntu
            // Instead, provide a more helpful error message
            return ['success' => false, 'message' => 'Database restore failed using Laravel-native approach: ' . $e->getMessage() . '. This may be due to MySQL configuration issues on the server.'];
        }
    } catch (Exception $e) {
        error_log("Restore: Laravel-native approach failed: " . $e->getMessage());
        error_log("Restore: Exception details: " . $e->getTraceAsString());

        // Don't fall back to mysql command-line since it's known to fail on Ubuntu
        // Instead, provide a more helpful error message
        return ['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage() . '. Please check your database configuration.'];
    }
}

/**
 * Restore from SQL file using mysql command-line tool (fallback)
 */
function restoreFromSqlCommandLine($backupFile, $dbConfig)
{
    $mysqlPath = findMysql();
    if (!$mysqlPath) {
        return ['success' => false, 'message' => 'MySQL command not found. Please ensure MySQL is installed and mysql command is in your PATH.'];
    }

    $tempConfigFile = tempnam(sys_get_temp_dir(), 'mysql_config_');
    $configContent = "[client]\n";
    $configContent .= "host=" . $dbConfig['host'] . "\n";
    $configContent .= "port=" . $dbConfig['port'] . "\n";
    $configContent .= "user=" . $dbConfig['username'] . "\n";
    $configContent .= "password=" . $dbConfig['password'] . "\n";
    $configContent .= "database=" . $dbConfig['database'] . "\n";

    file_put_contents($tempConfigFile, $configContent);
    chmod($tempConfigFile, 0600);

    $command = sprintf(
        '%s --defaults-extra-file=%s < %s 2>&1',
        escapeshellarg($mysqlPath),
        escapeshellarg($tempConfigFile),
        escapeshellarg($backupFile)
    );

    error_log("Restore: Executing mysql command: " . str_replace($dbConfig['password'], '***HIDDEN***', $command));

    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    if (file_exists($tempConfigFile)) {
        unlink($tempConfigFile);
    }

    if ($returnCode === 0) {
        return ['success' => true, 'message' => 'Database restored successfully from SQL file using mysql command'];
    } else {
        return ['success' => false, 'message' => 'Restore failed: ' . implode("\n", $output)];
    }
}

/**
 * Restore from compressed SQL file (gzip)
 */
function restoreFromGzip($backupFile, $dbConfig)
{
    // Check if gunzip command is available (for decompression)
    $gunzipCommand = null;
    $gunzipPaths = [
        '/usr/bin/gunzip',
        '/bin/gunzip',
        '/opt/homebrew/bin/gunzip',
        'gunzip'
    ];

    foreach ($gunzipPaths as $path) {
        if (is_executable($path) || isCommandAvailable($path)) {
            $gunzipCommand = $path;
            break;
        }
    }

    if (!$gunzipCommand) {
        return ['success' => false, 'message' => 'gunzip command not found. Please ensure gzip/gunzip is installed.'];
    }

    // First, decompress the .sql.gz file to a temporary .sql file
    $tempSqlFile = tempnam(sys_get_temp_dir(), 'decompressed_sql_') . '.sql';

    $decompressCommand = sprintf(
        '%s -c %s > %s',
        escapeshellarg($gunzipCommand),
        escapeshellarg($backupFile),
        escapeshellarg($tempSqlFile)
    );

    error_log("Restore: Decompressing SQL file: " . basename($backupFile));
    $output = [];
    $returnCode = 0;
    exec($decompressCommand, $output, $returnCode);

    if ($returnCode !== 0) {
        return ['success' => false, 'message' => 'Failed to decompress SQL file: ' . implode("\n", $output)];
    }

    if (!file_exists($tempSqlFile)) {
        return ['success' => false, 'message' => 'Failed to create decompressed SQL file'];
    }

    error_log("Restore: SQL file decompressed successfully, size: " . filesize($tempSqlFile) . " bytes");

    // Now restore from the decompressed SQL file using Laravel-native approach
    $dbResult = restoreFromSql($tempSqlFile, $dbConfig);

    // Clean up the temporary SQL file
    if (file_exists($tempSqlFile)) {
        unlink($tempSqlFile);
        error_log("Restore: Cleaned up temporary SQL file");
    }

    return $dbResult;
}

/**
 * Extract ZIP file
 */
function extractZipToRoot($zipFile)
{
    try {
        $projectRoot = __DIR__ . '/../../../';
        if (!file_exists($zipFile)) {
            return ['success' => false, 'message' => 'ZIP file not found: ' . $zipFile];
        }
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'message' => 'ZipArchive extension not available'];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== TRUE) {
            return ['success' => false, 'message' => 'Could not open ZIP file'];
        }

        $zip->extractTo($projectRoot);
        $zip->close();

        return ['success' => true, 'message' => 'ZIP file extracted successfully to project root'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'ZIP extraction failed: ' . $e->getMessage()];
    }
}

/**
 * Extract TAR.GZ file
 */
function extractTarGzToRoot($tarGzFile)
{
    try {
        $projectRoot = __DIR__ . '/../../../';
        if (!file_exists($tarGzFile)) {
            return ['success' => false, 'message' => 'TAR.GZ file not found: ' . $tarGzFile];
        }

        // Check if tar command is available
        $tarCommand = null;
        $tarPaths = [
            '/usr/bin/tar',
            '/bin/tar',
            '/opt/homebrew/bin/tar',
            'tar'
        ];

        foreach ($tarPaths as $path) {
            if (is_executable($path) || isCommandAvailable($path)) {
                $tarCommand = $path;
                break;
            }
        }

        if (!$tarCommand) {
            return ['success' => false, 'message' => 'TAR command not found. Please ensure tar is installed.'];
        }

        // Extract TAR.GZ file
        $command = sprintf(
            'cd %s && %s -xzf %s 2>&1',
            escapeshellarg($projectRoot),
            escapeshellarg($tarCommand),
            escapeshellarg($tarGzFile)
        );

        error_log("TAR.GZ: Executing command: " . $command);
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        error_log("TAR.GZ: Return code: " . $returnCode);
        error_log("TAR.GZ: Output: " . implode("\n", $output));

        if ($returnCode === 0) {
            error_log("TAR.GZ: Extraction successful");

            // List what files were extracted
            $extractedFiles = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($projectRoot, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($projectRoot, '', $file->getPathname());
                    $extractedFiles[] = $relativePath;
                }
            }

            error_log("TAR.GZ: Extracted files: " . json_encode(array_slice($extractedFiles, 0, 20))); // Show first 20 files

            return ['success' => true, 'message' => 'TAR.GZ file extracted successfully to project root'];
        } else {
            error_log("TAR.GZ: Extraction failed");
            return ['success' => false, 'message' => 'TAR.GZ extraction failed: ' . implode("\n", $output)];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'TAR.GZ extraction failed: ' . $e->getMessage()];
    }
}

/**
 * Remove directory recursively
 */
function removeDirectory($dir)
{
    if (!is_dir($dir)) return;

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

/**
 * Get available backup files from local or cloud storage
 */
function getAvailableBackups($searchTerm = '')
{
    global $backupExtensions;
    $backupFiles = [];

    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if database_backup_settings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backup_settings'");
        if ($stmt->rowCount() === 0) {
            // Fallback to local storage
            return getLocalBackups($searchTerm);
        }

        // Get backup settings
        $stmt = $pdo->query("SELECT storage_location, storage_config FROM database_backup_settings LIMIT 1");
        $settings = $stmt->fetch();

        error_log("Backup settings: " . json_encode($settings));

        if (!$settings || $settings['storage_location'] === 'local') {
            error_log("Using local storage");
            // Local storage
            return getLocalBackups($searchTerm);
        } elseif ($settings['storage_location'] === 'storage_setting') {
            error_log("Using cloud storage");

            // Cloud storage
            $cloudBackups = getCloudBackups($searchTerm, $settings);
            error_log("Cloud backups returned: " . count($cloudBackups));
            return $cloudBackups;
        }

        // Default fallback to local
        return getLocalBackups($searchTerm);
    } catch (Exception $e) {
        error_log("Error getting available backups: " . $e->getMessage());
        // Fallback to local storage
        return getLocalBackups($searchTerm);
    }
}

/**
 * Get backup files from local storage
 */
function getLocalBackups($searchTerm = '')
{
    global $backupExtensions;
    $backupDir = getBackupDirectory();
    $backupFiles = [];

    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        error_log("Local backup directory: " . $backupDir);
        error_log("Total files in directory: " . count($files));

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $filePath = $backupDir . $file;
            if (is_file($filePath)) {
                if (isBackupFile($file)) {
                    // Apply search filter if provided
                    if (!empty($searchTerm) && stripos($file, $searchTerm) === false) {
                        continue;
                    }

                    $backupFiles[] = [
                        'filename' => $file,
                        'path' => $filePath,
                        'size' => filesize($filePath),
                        'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'storage_type' => 'local'
                    ];
                }
            }
        }

        error_log("Total local backup files found: " . count($backupFiles));
    } else {
        error_log("Local backup directory does not exist: " . $backupDir);
    }

    usort($backupFiles, function ($a, $b) {
        return strtotime($b['modified']) - strtotime($a['modified']);
    });

    return $backupFiles;
}

/**
 * Get backup files from cloud storage (S3, etc.)
 */
function getCloudBackups($searchTerm = '', $settings = null)
{
    $backupFiles = [];

    try {
        error_log("Getting cloud backups...");

        // Get storage configuration
        $storageConfig = null;

        if ($settings && isset($settings['storage_config']) && !empty($settings['storage_config'])) {
            $storageConfig = json_decode($settings['storage_config'], true);
            error_log("Using storage_config from settings");
        } else {
            $storageConfig = getStorageConfig();
            error_log("Using storage_config from getStorageConfig()");
        }

        if (!$storageConfig) {
            error_log("No cloud storage configuration found");
            return [];
        }

        error_log("Storage config: " . json_encode($storageConfig));

        $filesystem = $storageConfig['filesystem'] ?? $storageConfig['driver'] ?? 'local';
        error_log("Filesystem: " . $filesystem);

        if ($filesystem === 'aws_s3' || $filesystem === 's3') {
            error_log("Calling getS3Backups...");
            $backups = getS3Backups($searchTerm, $storageConfig);
            error_log("getS3Backups returned: " . count($backups) . " files");
            if (empty($backups)) {
                error_log("Trying simple S3 method...");
                $backups = getS3BackupsSimple($searchTerm, $storageConfig);
                error_log("getS3BackupsSimple returned: " . count($backups) . " files");
            }
            return $backups;
        } elseif ($filesystem === 'local') {
            error_log("Calling getLocalBackups...");
            return getLocalBackups($searchTerm);
        } else {
            error_log("Unsupported filesystem: " . $filesystem);
            return [];
        }
    } catch (Exception $e) {
        error_log("Error getting cloud backups: " . $e->getMessage());
        error_log("Error trace: " . $e->getTraceAsString());
        return [];
    }
}

/**
 * Get backup files from S3 using simple HTTP requests
 */
function getS3Backups($searchTerm = '', $storageConfig = null)
{
    $backupFiles = [];

    try {
        if (!$storageConfig) {
            error_log("No S3 configuration provided");
            return [];
        }

        $authKeys = $storageConfig['auth_keys'] ?? $storageConfig['config'] ?? [];

        error_log("S3 Auth Keys: " . json_encode(array_keys($authKeys)));

        if (empty($authKeys)) {
            error_log("No S3 authentication keys found");
            return [];
        }

        // Check if required keys exist
        $requiredKeys = ['access_key', 'secret_key', 'bucket'];
        foreach ($requiredKeys as $key) {
            if (empty($authKeys[$key])) {
                error_log("Missing required S3 key: " . $key);
                return [];
            }
        }

        $bucket = trim($authKeys['bucket']);
        $region = trim($authKeys['region'] ?? 'us-east-1');
        $prefix = trim($authKeys['prefix'] ?? 'backups/');

        error_log("S3 Bucket: " . $bucket);
        error_log("S3 Region: " . $region);
        error_log("S3 Prefix: " . $prefix);

        // Use simple HTTP request to list objects with authentication
        $accessKey = trim($authKeys['access_key']);
        $secretKey = trim($authKeys['secret_key']);

        $timestamp = time();
        $date = gmdate('Ymd', $timestamp);
        $datetime = gmdate('Ymd\THis\Z', $timestamp);

        $queryString = 'list-type=2&prefix=' . urlencode($prefix);
        $uri = '/';

        $headers = [
            'Host' => $bucket . '.s3.' . $region . '.amazonaws.com',
            'X-Amz-Date' => $datetime,
            'X-Amz-Content-Sha256' => hash('sha256', ''),
        ];

        // Create canonical request
        $canonicalRequest = createS3CanonicalRequest('GET', $uri, $queryString, $headers);

        // Create string to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = $date . '/' . $region . '/s3/aws4_request';
        $stringToSign = createS3StringToSign($algorithm, $datetime, $credentialScope, $canonicalRequest);

        // Calculate signature
        $signature = calculateS3Signature($stringToSign, $secretKey, $date, $region);

        $signedHeaders = implode(';', array_keys($headers));
        $authorizationHeader = $algorithm . ' Credential=' . $accessKey . '/' . $credentialScope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;

        $url = "https://{$bucket}.s3.{$region}.amazonaws.com/?{$queryString}";
        error_log("S3 Request URL: " . $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: ' . $bucket . '.s3.' . $region . '.amazonaws.com',
            'X-Amz-Date: ' . $datetime,
            'X-Amz-Content-Sha256: ' . hash('sha256', ''),
            'Authorization: ' . $authorizationHeader,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        error_log("S3 cURL Error: " . $error);

        error_log("S3 HTTP Response Code: " . $httpCode);
        error_log("S3 HTTP Response: " . substr($response, 0, 1000));

        if ($httpCode !== 200) {
            error_log("S3 request failed with HTTP code: " . $httpCode);
            error_log("S3 Error Response: " . $response);
            return [];
        }

        // Parse XML response
        $xml = simplexml_load_string($response);
        if (!$xml) {
            error_log("Failed to parse S3 XML response");
            error_log("Raw response: " . $response);
            return [];
        }

        error_log("S3 XML parsed successfully");
        error_log("S3 XML Contents count: " . count($xml->Contents));

        foreach ($xml->Contents as $object) {
            $key = (string)$object->Key;
            $filename = basename($key);
            error_log("S3 Object: " . $key . " (filename: " . $filename . ")");

            if (isBackupFile($filename)) {
                // Apply search filter if provided
                if (!empty($searchTerm) && stripos($filename, $searchTerm) === false) {
                    continue;
                }

                $backupFiles[] = [
                    'filename' => $filename,
                    'path' => $key,
                    'size' => (int)$object->Size,
                    'modified' => (string)$object->LastModified,
                    'storage_type' => 's3',
                    'bucket' => $bucket,
                    'key' => $key
                ];
                error_log("Added S3 backup file: " . $filename);
            }
        }

        usort($backupFiles, function ($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        error_log("Total S3 backup files found: " . count($backupFiles));
        return $backupFiles;
    } catch (Exception $e) {
        error_log("Error getting S3 backups: " . $e->getMessage());
        error_log("Error trace: " . $e->getTraceAsString());
        return [];
    }
}

/**
 * Check if a file is a backup file
 */
function isBackupFile($filename)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $isBackupFile = false;

    // Check for single extensions
    if (in_array($extension, ['sql', 'zip', 'gz'])) {
        $isBackupFile = true;
    }

    // Check for double extensions like .tar.gz
    if (!$isBackupFile && strpos($filename, '.tar.gz') !== false) {
        $isBackupFile = true;
    }

    // Check for other common backup formats
    if (!$isBackupFile && in_array($extension, ['tar', 'bz2', 'xz'])) {
        $isBackupFile = true;
    }

    // Additional check for any file that contains 'backup' in the name
    if (!$isBackupFile && stripos($filename, 'backup') !== false) {
        $isBackupFile = true;
    }

    return $isBackupFile;
}

/**
 * Get version from database backup record
 */
function getBackupVersion($filename)
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // First check if the table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backups'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Check if the version column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM database_backups LIKE 'version'");
        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Try to get version from database
        $stmt = $pdo->prepare("SELECT version FROM database_backups WHERE filename = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$filename]);
        $result = $stmt->fetch();

        if ($result && !empty($result['version'])) {
            return $result['version'];
        }

        // If no version in database, try to extract from filename
        // Look for version patterns in filename like v1.2.3 or version_1.2.3
        if (preg_match('/v(\d+\.\d+\.\d+)/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/version[_-](\d+\.\d+\.\d+)/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(\d+\.\d+\.\d+)/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    } catch (Exception $e) {
        error_log("Error getting backup version: " . $e->getMessage());

        // Fallback: try to extract version from filename
        if (preg_match('/v(\d+\.\d+\.\d+)/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/version[_-](\d+\.\d+\.\d+)/i', $filename, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(\d+\.\d+\.\d+)/', $filename, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

/**
 * Get current application version
 */
function getCurrentVersion()
{
    $versionFile = __DIR__ . '/../../public/version.txt';
    if (file_exists($versionFile)) {
        $version = trim(file_get_contents($versionFile));
        if (!empty($version)) {
            return $version;
        }
    }
    return 'N/A';
}

/**
 * Check database backup table status
 */
function checkDatabaseBackupStatus()
{
    try {
        $dbConfig = getDatabaseConfig();
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $status = [
            'table_exists' => false,
            'version_column_exists' => false,
            'backup_records_count' => 0,
            'backups_with_version' => 0
        ];

        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'database_backups'");
        $status['table_exists'] = $stmt->rowCount() > 0;

        if ($status['table_exists']) {
            // Check if version column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM database_backups LIKE 'version'");
            $status['version_column_exists'] = $stmt->rowCount() > 0;

            // Count total backup records
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM database_backups");
            $result = $stmt->fetch();
            $status['backup_records_count'] = $result['count'];

            // Count backups with version
            if ($status['version_column_exists']) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM database_backups WHERE version IS NOT NULL AND version != ''");
                $result = $stmt->fetch();
                $status['backups_with_version'] = $result['count'];
            }
        }

        return $status;
    } catch (Exception $e) {
        error_log("Error checking database backup status: " . $e->getMessage());
        return [
            'table_exists' => false,
            'version_column_exists' => false,
            'backup_records_count' => 0,
            'backups_with_version' => 0,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Restore database and files from backup
 */
function restoreBackup($backupFile)
{
    try {
        $dbConfig = getDatabaseConfig();
        $results = [];
        $success = true;

        // If the backup file doesn't exist in current directory, try to find it in backup directory
        if (!file_exists($backupFile)) {
            $backupDir = getBackupDirectory();
            $backupPath = $backupDir . '/' . basename($backupFile);
            if (file_exists($backupPath)) {
                $backupFile = $backupPath;
                error_log("Restore: Found backup file in backup directory: $backupFile");
            } else {
                error_log("Restore: Backup file not found: $backupFile or $backupPath");
                return ['success' => false, 'message' => "Backup file not found: " . basename($backupFile)];
            }
        }

        // Check if this is a cloud storage backup
        $backupInfo = getBackupInfo($backupFile);

        if ($backupInfo && isset($backupInfo['storage_type']) && $backupInfo['storage_type'] === 's3') {
            // Download from S3 first
            $downloadResult = downloadFromS3($backupInfo);
            if (!$downloadResult['success']) {
                return $downloadResult;
            }
            $backupFile = $downloadResult['local_path'];
        }

        $filename = basename($backupFile);
        $extension = strtolower(pathinfo($backupFile, PATHINFO_EXTENSION));

        error_log("Restore: File: $backupFile");
        error_log("Restore: Filename: $filename");
        error_log("Restore: Extension: $extension");

        if ($extension === 'zip') {
            $extractResult = extractZipToRoot($backupFile);
            $results[] = "Main ZIP: " . ($extractResult['success'] ? "✅ " . $extractResult['message'] : "❌ " . $extractResult['message']);

            if (!$extractResult['success']) {
                $success = false;
            } else {
                $projectRoot = __DIR__ . '/../../../';
                $nestedZipFile = null;

                $filesZipPattern = $projectRoot . 'files/files_backup_*.zip';
                $filesZipFiles = glob($filesZipPattern);

                if (!empty($filesZipFiles)) {
                    $nestedZipFile = $filesZipFiles[0];
                    $results[] = "Found nested ZIP: " . basename($nestedZipFile);

                    $nestedExtractResult = extractZipToRoot($nestedZipFile);
                    $results[] = "Files: " . ($nestedExtractResult['success'] ? "✅ " . $nestedExtractResult['message'] : "❌ " . $nestedExtractResult['message']);

                    if (!$nestedExtractResult['success']) {
                        $success = false;
                    }
                } else {
                    $results[] = "Files: ⚠️ No nested files ZIP found";
                    $success = false;
                }

                $databaseFile = null;
                global $possibleDbLocations;

                foreach ($possibleDbLocations as $location) {
                    $fullPath = $projectRoot . $location;
                    if (strpos($location, '*') !== false) {
                        $files = glob($fullPath);
                        if (!empty($files)) {
                            $databaseFile = $files[0];
                            break;
                        }
                    } elseif (file_exists($fullPath)) {
                        $databaseFile = $fullPath;
                        break;
                    }
                }

                if ($databaseFile) {
                    // Check if it's a compressed SQL file
                    $extension = strtolower(pathinfo($databaseFile, PATHINFO_EXTENSION));
                    if ($extension === 'gz') {
                        $dbResult = restoreFromGzip($databaseFile, $dbConfig);
                    } else {
                        $dbResult = restoreFromSql($databaseFile, $dbConfig);
                    }

                    $results[] = "Database: " . ($dbResult['success'] ? "✅ " . $dbResult['message'] : "❌ " . $dbResult['message']);
                    if (!$dbResult['success']) {
                        $success = false;
                    } else {
                        if (file_exists($databaseFile)) {
                            unlink($databaseFile);
                        }

                        $filesFolder = $projectRoot . 'files';
                        if (is_dir($filesFolder)) {
                            removeDirectory($filesFolder);
                        }

                        $bootstrapCacheFolder = $projectRoot . 'bootstrap/cache';
                        if (is_dir($bootstrapCacheFolder)) {
                            $cacheFiles = array_diff(scandir($bootstrapCacheFolder), array('.', '..'));
                            foreach ($cacheFiles as $file) {
                                $filePath = $bootstrapCacheFolder . '/' . $file;
                                if (is_file($filePath)) {
                                    unlink($filePath);
                                }
                            }
                            $results[] = "Cleanup: ✅ Bootstrap cache cleared";
                        }
                    }
                } else {
                    $results[] = "Database: ⚠️ No database file found in ZIP";
                }
            }
        } elseif ($extension === 'sql') {
            $dbResult = restoreFromSql($backupFile, $dbConfig);
            $results[] = "Database: " . ($dbResult['success'] ? "✅ " . $dbResult['message'] : "❌ " . $dbResult['message']);
            if (!$dbResult['success']) {
                $success = false;
            }
        } elseif ($extension === 'gz') {
            // Check if it's a compressed SQL file (.sql.gz)
            if (strpos($filename, '.sql.gz') !== false) {
                $dbResult = restoreFromGzip($backupFile, $dbConfig);
                $results[] = "Database: " . ($dbResult['success'] ? "✅ " . $dbResult['message'] : "❌ " . $dbResult['message']);
                if (!$dbResult['success']) {
                    $success = false;
                }
            } else {
                // Handle .tar.gz files
                $extractResult = extractTarGzToRoot($backupFile);
                $results[] = "Main TAR.GZ: " . ($extractResult['success'] ? "✅ " . $extractResult['message'] : "❌ " . $extractResult['message']);

                if (!$extractResult['success']) {
                    $success = false;
                } else {
                    $projectRoot = __DIR__ . '/../../../';
                    $databaseFile = null;
                    global $possibleDbLocations;

                    error_log("TAR.GZ: Looking for database file in project root: " . $projectRoot);
                    error_log("TAR.GZ: Possible locations: " . json_encode($possibleDbLocations));

                    foreach ($possibleDbLocations as $location) {
                        $fullPath = $projectRoot . $location;
                        error_log("TAR.GZ: Checking location: " . $fullPath);
                        if (strpos($location, '*') !== false) {
                            $files = glob($fullPath);
                            error_log("TAR.GZ: Glob pattern found " . count($files) . " files");
                            if (!empty($files)) {
                                $databaseFile = $files[0];
                                error_log("TAR.GZ: Found database file: " . $databaseFile);
                                break;
                            }
                        } elseif (file_exists($fullPath)) {
                            $databaseFile = $fullPath;
                            error_log("TAR.GZ: Found database file: " . $databaseFile);
                            break;
                        }
                    }

                    // If no database file found, look for compressed SQL files
                    if (!$databaseFile) {
                        error_log("TAR.GZ: No uncompressed SQL files found, looking for compressed files...");
                        $compressedSqlFiles = glob($projectRoot . '*.sql.gz');
                        error_log("TAR.GZ: Compressed SQL files found: " . json_encode(array_map('basename', $compressedSqlFiles)));

                        if (!empty($compressedSqlFiles)) {
                            $databaseFile = $compressedSqlFiles[0];
                            error_log("TAR.GZ: Found compressed database file: " . $databaseFile);
                        }
                    }

                    if ($databaseFile) {
                        error_log("TAR.GZ: Restoring database from: " . $databaseFile);

                        // Check if it's a compressed SQL file
                        $extension = strtolower(pathinfo($databaseFile, PATHINFO_EXTENSION));
                        if ($extension === 'gz') {
                            $dbResult = restoreFromGzip($databaseFile, $dbConfig);
                        } else {
                            $dbResult = restoreFromSql($databaseFile, $dbConfig);
                        }

                        $results[] = "Database: " . ($dbResult['success'] ? "✅ " . $dbResult['message'] : "❌ " . $dbResult['message']);
                        error_log("TAR.GZ: Database restore result: " . json_encode($dbResult));
                        if (!$dbResult['success']) {
                            $success = false;
                        } else {
                            if (file_exists($databaseFile)) {
                                unlink($databaseFile);
                                error_log("TAR.GZ: Cleaned up database file: " . $databaseFile);
                            }

                            $bootstrapCacheFolder = $projectRoot . 'bootstrap/cache';
                            if (is_dir($bootstrapCacheFolder)) {
                                $cacheFiles = array_diff(scandir($bootstrapCacheFolder), array('.', '..'));
                                foreach ($cacheFiles as $file) {
                                    $filePath = $bootstrapCacheFolder . '/' . $file;
                                    if (is_file($filePath)) {
                                        unlink($filePath);
                                    }
                                }
                                $results[] = "Cleanup: ✅ Bootstrap cache cleared";
                                error_log("TAR.GZ: Bootstrap cache cleared");
                            }
                        }
                    } else {
                        $results[] = "Database: ⚠️ No database file found in TAR.GZ";
                        error_log("TAR.GZ: No database file found in extracted files");

                        // Try to find any SQL file that might have been extracted
                        $allSqlFiles = array_merge(
                            glob($projectRoot . '*.sql'),
                            glob($projectRoot . '*.sql.gz'),
                            glob($projectRoot . 'database/*.sql'),
                            glob($projectRoot . 'database/*.sql.gz'),
                            glob($projectRoot . 'backup/*.sql'),
                            glob($projectRoot . 'backup/*.sql.gz'),
                            glob($projectRoot . 'database_backup_*.sql'),
                            glob($projectRoot . 'database_backup_*.sql.gz')
                        );

                        error_log("TAR.GZ: All SQL files found: " . json_encode(array_map('basename', $allSqlFiles)));

                        if (!empty($allSqlFiles)) {
                            $firstSqlFile = $allSqlFiles[0];
                            error_log("TAR.GZ: Trying to restore from found SQL file: " . $firstSqlFile);

                            $extension = strtolower(pathinfo($firstSqlFile, PATHINFO_EXTENSION));
                            if ($extension === 'gz') {
                                $dbResult = restoreFromGzip($firstSqlFile, $dbConfig);
                            } else {
                                $dbResult = restoreFromSql($firstSqlFile, $dbConfig);
                            }

                            $results[] = "Database (fallback): " . ($dbResult['success'] ? "✅ " . $dbResult['message'] : "❌ " . $dbResult['message']);
                            if (!$dbResult['success']) {
                                $success = false;
                            } else {
                                if (file_exists($firstSqlFile)) {
                                    unlink($firstSqlFile);
                                    error_log("TAR.GZ: Cleaned up fallback database file: " . $firstSqlFile);
                                }
                            }
                        } else {
                            error_log("TAR.GZ: No SQL files found anywhere in extracted content");
                        }
                    }

                    // Check for nested files backup
                    $nestedFilesBackup = null;
                    $filesBackupPattern = $projectRoot . 'files_backup_*.tar.gz';
                    $filesBackupFiles = glob($filesBackupPattern);

                    if (!empty($filesBackupFiles)) {
                        $nestedFilesBackup = $filesBackupFiles[0];
                        error_log("TAR.GZ: Found nested files backup: " . $nestedFilesBackup);

                        $nestedExtractResult = extractTarGzToRoot($nestedFilesBackup);
                        $results[] = "Files: " . ($nestedExtractResult['success'] ? "✅ " . $nestedExtractResult['message'] : "❌ " . $nestedExtractResult['message']);

                        if (!$nestedExtractResult['success']) {
                            $success = false;
                        } else {
                            // Clean up the nested files backup
                            if (file_exists($nestedFilesBackup)) {
                                unlink($nestedFilesBackup);
                                error_log("TAR.GZ: Cleaned up nested files backup: " . $nestedFilesBackup);
                            }
                        }
                    } else {
                        $results[] = "Files: ⚠️ No nested files backup found";
                        error_log("TAR.GZ: No nested files backup found");
                    }

                    // Additional check: Look for database files in the extracted content
                    error_log("TAR.GZ: Searching for database files in extracted content...");
                    $allFiles = glob($projectRoot . '*');
                    error_log("TAR.GZ: All files in project root: " . json_encode(array_map('basename', $allFiles)));

                    // Look for any SQL files that might have been extracted
                    $sqlFiles = glob($projectRoot . '*.sql');
                    $sqlGzFiles = glob($projectRoot . '*.sql.gz');
                    $databaseFiles = glob($projectRoot . 'database*.sql');
                    $backupFiles = glob($projectRoot . 'backup*.sql');

                    error_log("TAR.GZ: SQL files found: " . json_encode(array_map('basename', $sqlFiles)));
                    error_log("TAR.GZ: Compressed SQL files found: " . json_encode(array_map('basename', $sqlGzFiles)));
                    error_log("TAR.GZ: Database files found: " . json_encode(array_map('basename', $databaseFiles)));
                    error_log("TAR.GZ: Backup files found: " . json_encode(array_map('basename', $backupFiles)));
                }
            }
        } else {
            error_log("Restore: Unsupported extension: $extension for file: $backupFile");
            return ['success' => false, 'message' => 'Unsupported file format: ' . $extension];
        }

        // Clean up downloaded file if it was from cloud storage
        if (isset($downloadResult) && isset($downloadResult['local_path']) && file_exists($downloadResult['local_path'])) {
            unlink($downloadResult['local_path']);
        }

        $message = implode("<br>", $results);
        return ['success' => $success, 'message' => $message];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()];
    }
}

/**
 * Get backup information from the backup files list
 */
function getBackupInfo($backupFile)
{
    $backupFiles = getAvailableBackups();
    $filename = basename($backupFile);

    foreach ($backupFiles as $backup) {
        if ($backup['filename'] === $filename) {
            return $backup;
        }
    }

    return null;
}

/**
 * Download backup file from S3 using simple HTTP request
 */
function downloadFromS3($backupInfo)
{
    try {
        if (!isset($backupInfo['storage_type']) || $backupInfo['storage_type'] !== 's3') {
            return ['success' => false, 'message' => 'Not an S3 backup'];
        }

        // Get storage configuration
        $storageConfig = getBackupStorageConfig();
        if (!$storageConfig || $storageConfig['source'] !== 'backup_settings') {
            return ['success' => false, 'message' => 'S3 configuration not found'];
        }

        $authKeys = $storageConfig['auth_keys'] ?? [];
        if (empty($authKeys)) {
            return ['success' => false, 'message' => 'No S3 authentication keys found'];
        }

        $bucket = $authKeys['bucket'] ?? '';
        $region = $authKeys['region'] ?? 'us-east-1';
        $key = $backupInfo['key'] ?? '';

        if (empty($bucket) || empty($key)) {
            return ['success' => false, 'message' => 'Invalid S3 bucket or key'];
        }

        // Create temporary file with proper extension
        $originalExtension = pathinfo($backupInfo['filename'], PATHINFO_EXTENSION);
        $tempFile = tempnam(sys_get_temp_dir(), 'backup_') . '.' . $originalExtension;

        // Use simple HTTP request to download with authentication
        $accessKey = $authKeys['access_key'];
        $secretKey = $authKeys['secret_key'];

        $timestamp = time();
        $date = gmdate('Ymd', $timestamp);
        $datetime = gmdate('Ymd\THis\Z', $timestamp);

        $uri = '/' . $key;
        $queryString = '';

        $headers = [
            'Host' => $bucket . '.s3.' . $region . '.amazonaws.com',
            'X-Amz-Date' => $datetime,
            'X-Amz-Content-Sha256' => hash('sha256', ''),
        ];

        // Create canonical request
        $canonicalRequest = createS3CanonicalRequest('GET', $uri, $queryString, $headers);

        // Create string to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = $date . '/' . $region . '/s3/aws4_request';
        $stringToSign = createS3StringToSign($algorithm, $datetime, $credentialScope, $canonicalRequest);

        // Calculate signature
        $signature = calculateS3Signature($stringToSign, $secretKey, $date, $region);

        $signedHeaders = implode(';', array_keys($headers));
        $authorizationHeader = $algorithm . ' Credential=' . $accessKey . '/' . $credentialScope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;

        $url = "https://{$bucket}.s3.{$region}.amazonaws.com/" . urlencode($key);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: ' . $bucket . '.s3.' . $region . '.amazonaws.com',
            'X-Amz-Date: ' . $datetime,
            'X-Amz-Content-Sha256: ' . hash('sha256', ''),
            'Authorization: ' . $authorizationHeader,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['success' => false, 'message' => 'Failed to download from S3: HTTP ' . $httpCode];
        }

        // Save to temporary file
        if (file_put_contents($tempFile, $response) === false) {
            return ['success' => false, 'message' => 'Failed to save downloaded file'];
        }

        return [
            'success' => true,
            'message' => 'Downloaded from S3 successfully',
            'local_path' => $tempFile
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to download from S3: ' . $e->getMessage()];
    }
}

// Handle authentication
$authError = '';
$isLoggedIn = false;

if (!is_cli()) {
    if (isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (authenticateUser($username, $password)) {
            $isLoggedIn = true;
        } else {
            $authError = 'Invalid username or password. Only superadmin users can access this tool.';
        }
    }

    if (isset($_GET['logout'])) {
        logoutUser();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $isLoggedIn = isAuthenticated();

    if (!$isLoggedIn && !is_cli()) {
        include 'login-form.php';
        exit;
    }
}

if (is_cli() && !is_allowed_access()) {
    die('Access denied. This tool is only available from localhost or CLI.');
}

/**
 * Clear cache files from bootstrap/cache directory
 */
function clearBootstrapCache()
{
    try {
        $cacheDir = __DIR__ . '/../../../bootstrap/cache';
        
        if (!is_dir($cacheDir)) {
            return ['success' => false, 'message' => 'Bootstrap cache directory does not exist: ' . $cacheDir];
        }

        $deletedFiles = 0;
        $deletedDirs = 0;
        $errors = [];

        // Get all files and directories in the cache folder
        $items = array_diff(scandir($cacheDir), array('.', '..'));
        
        if (empty($items)) {
            return ['success' => true, 'message' => 'Bootstrap cache directory is already empty.'];
        }

        foreach ($items as $item) {
            $itemPath = $cacheDir . '/' . $item;
            
            try {
                if (is_file($itemPath)) {
                    if (unlink($itemPath)) {
                        $deletedFiles++;
                    } else {
                        $errors[] = "Failed to delete file: " . $item;
                    }
                } elseif (is_dir($itemPath)) {
                    if (removeDirectory($itemPath)) {
                        $deletedDirs++;
                    } else {
                        $errors[] = "Failed to delete directory: " . $item;
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Error deleting " . $item . ": " . $e->getMessage();
            }
        }

        $message = "Cache cleared successfully. ";
        $message .= "Deleted " . $deletedFiles . " file(s) and " . $deletedDirs . " directory(ies).";
        
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
            return ['success' => false, 'message' => $message];
        }

        return ['success' => true, 'message' => $message];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to clear cache: ' . $e->getMessage()];
    }
}

// Handle form submission
$message = '';
$messageType = '';

if (!is_cli() && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['restore'])) {
        $backupFile = $_POST['backup_file'] ?? '';

        if (!empty($backupFile)) {
            $result = restoreBackup($backupFile);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
        } else {
            $message = 'Please select a backup file to restore.';
            $messageType = 'error';
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            $response = [
                'success' => $messageType !== 'error',
                'message' => $message,
                'type' => $messageType
            ];
            echo json_encode($response);
            exit;
        }
    } elseif (isset($_POST['clear_cache'])) {
        $result = clearBootstrapCache();
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            $response = [
                'success' => $result['success'],
                'message' => $message,
                'type' => $messageType
            ];
            echo json_encode($response);
            exit;
        }
    }
}

// Get available backup files
$searchTerm = $_GET['search'] ?? '';
$backupFiles = getAvailableBackups($searchTerm);

// Get backup directory for display
$backupDir = getBackupDirectory();

// Test database connection
$dbTest = testDatabaseConnection();

// Check database backup status
$dbBackupStatus = checkDatabaseBackupStatus();

// Get backup settings information
$backupSettings = getBackupSettingsInfo();

// Only show HTML output if not in CLI
if (!is_cli()) {
    include 'main-interface.php';
} else {
    // CLI Output
    echo "=== Emergency Backup Restore Tool (CLI Mode) ===\n\n";

    echo "Database Status: ";
    if ($dbTest['success']) {
        echo "✅ " . $dbTest['message'] . "\n";
    } else {
        echo "❌ " . $dbTest['message'] . "\n";
    }

    echo "MySQL Command: ";
    $mysqlPath = findMysql();
    if ($mysqlPath) {
        echo "✅ Available at: " . $mysqlPath . "\n";
    } else {
        echo "❌ Not found\n";
    }

    echo "\nAvailable Backups:\n";
    if (!empty($backupFiles)) {

        foreach ($backupFiles as $backup) {
            echo "- " . $backup['filename'] . " (" . number_format($backup['size'] / 1024 / 1024, 2) . " MB, " . $backup['modified'] . ")\n";
        }
    } else {
        echo "No backup files found.\n";
    }

    echo "\nTo restore from CLI, use: php restore-backup.php --restore=<backup_file>\n";
    echo "This will restore both database and files from the backup.\n";

    // Handle CLI restore command
    if (isset($argv)) {
        $options = getopt('', ['restore:', 'list', 'test-db']);

        if (isset($options['restore'])) {
            $backupFile = $options['restore'];
            echo "\n=== Starting Restore Process ===\n";
            echo "Backup file: $backupFile\n\n";

            $result = restoreBackup($backupFile);
            echo "Restore Result: " . ($result['success'] ? "SUCCESS" : "FAILED") . "\n";
            echo "Message: " . $result['message'] . "\n";

            if ($result['success']) {
                echo "\n✅ Restore completed successfully!\n";
            } else {
                echo "\n❌ Restore failed!\n";
            }
        } elseif (isset($options['list'])) {
            echo "\n=== Available Backups ===\n";
            foreach ($backupFiles as $backup) {
                echo "- " . $backup['filename'] . " (" . number_format($backup['size'] / 1024 / 1024, 2) . " MB, " . $backup['modified'] . ")\n";
            }
        } elseif (isset($options['test-db'])) {
            echo "\n=== Database Connection Test ===\n";
            if ($dbTest['success']) {
                echo "✅ Database connection successful\n";
            } else {
                echo "❌ Database connection failed: " . $dbTest['message'] . "\n";
            }
        }
    }
}
