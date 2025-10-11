<?php
// Check if the backup module exists
$backupModulePath = __DIR__ . '/../../Modules/Backup/Restore/restore-backup.php';

if (file_exists($backupModulePath)) {
    // Include the backup restore file
    @include_once $backupModulePath;
} else {
    @include_once __DIR__ . '/module-not-found.php';
}
