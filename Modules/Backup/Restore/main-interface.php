<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Backup Restore Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Emergency Backup Restore Tool</h1>
                        <p class="text-sm text-gray-500">Database and file restoration utility</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="clearCache()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear Cache
                    </button>
                    <a href="?logout=1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>

            <div class="px-6 py-6 space-y-6">
                <!-- Warning Alert -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">⚠️ WARNING</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This tool will completely replace your current database and files. Make sure you have a backup of your current data before proceeding. This action cannot be undone!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Messages -->
                <?php if (isset($message) && $message): ?>
                    <div class="<?php echo $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'; ?> border rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if ($messageType === 'success'): ?>
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm"><?php echo htmlspecialchars($message); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- System Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">Database Connection:</span>
                            <?php if ($dbTest['success']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Connected
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Failed
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">MySQL Command:</span>
                            <?php
                            $mysqlPath = findMysql();
                            if ($mysqlPath): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Available
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Not Found
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Database Backup Status (Debug Info) -->
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">Backup Table:</span>
                            <?php if ($dbBackupStatus['table_exists']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Exists (<?php echo $dbBackupStatus['backup_records_count']; ?> records)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Not Found
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Backup Directory Debug Info -->
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">Backup Directory:</span>
                            <span class="text-sm text-gray-600"><?php echo htmlspecialchars(getBackupDirectoryDisplay()); ?></span>
                        </div>

                        <!-- Storage Configuration Info -->
                        <?php
                        $storageConfig = getBackupStorageConfig();
                        if ($storageConfig):
                        ?>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-blue-700 w-48">Storage Config:</span>
                                <span class="text-sm text-gray-600">
                                    <?php echo ucfirst($storageConfig['filesystem']); ?>
                                    (<?php echo $storageConfig['source']; ?>)
                                    <?php if (isset($storageConfig['status'])): ?>
                                        - Status: <?php echo ucfirst($storageConfig['status']); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">Files Found:</span>
                            <span class="text-sm text-gray-600"><?php echo count($backupFiles); ?> backup files</span>
                        </div>

                        <!-- Backup Settings Info -->
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-blue-700 w-48">Backup Settings:</span>
                            <?php if ($backupSettings['table_exists']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $backupSettings['is_enabled'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $backupSettings['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                                </span>
                                <span class="ml-2 text-sm text-gray-600">
                                    (<?php echo ucfirst($backupSettings['storage_location']); ?> storage)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Not Configured
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>



                <!-- Backup Files -->
                <?php if (!empty($backupFiles)): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Available Backup Files</h3>
                                    <p class="text-sm text-gray-500">Click the restore button next to any backup file to restore database and files</p>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <span id="backupCount"><?php echo count($backupFiles); ?></span> backup<?php echo count($backupFiles) !== 1 ? 's' : ''; ?> found
                                </div>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text"
                                    id="searchBackups"
                                    placeholder="Search backup files..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button"
                                        id="clearSearch"
                                        class="text-gray-400 hover:text-gray-600 hidden"
                                        onclick="clearSearch()">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-200" id="backupFilesList">
                            <?php
                            $totalBackups = count($backupFiles);
                            $initialShow = 5;
                            $backupIndex = 0;
                            foreach ($backupFiles as $backup):
                                $backupVersion = getBackupVersion($backup['filename']);
                                $isNewerVersion = $backupVersion && version_compare($backupVersion, getCurrentVersion(), '>');
                                $isOlderVersion = $backupVersion && version_compare($backupVersion, getCurrentVersion(), '<');
                                $backupIndex++;
                                $showClass = $backupIndex <= $initialShow ? '' : 'hidden';
                            ?>
                                <div class="px-6 py-4 hover:bg-gray-50 backup-item <?php echo $showClass; ?>" data-filename="<?php echo htmlspecialchars(strtolower($backup['filename'])); ?>">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($backup['filename']); ?>
                                                    </div>
                                                    <?php if ($backupVersion): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $isNewerVersion ? 'bg-green-100 text-green-800' : ($isOlderVersion ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'); ?>">
                                                            v<?php echo htmlspecialchars($backupVersion); ?>
                                                            <?php if ($isNewerVersion): ?>
                                                                <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            <?php elseif ($isOlderVersion): ?>
                                                                <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Unknown
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Size: <?php echo number_format($backup['size'] / 1024 / 1024, 2); ?> MB |
                                                    Modified: <?php echo $backup['modified']; ?>
                                                    <?php if (isset($backup['storage_type'])): ?>
                                                        | Storage: <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium <?php echo $backup['storage_type'] === 's3' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?>">
                                                            <?php echo strtoupper($backup['storage_type']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($backupVersion): ?>
                                                        | Version: <?php echo $isNewerVersion ? 'Newer' : ($isOlderVersion ? 'Older' : 'Same'); ?> than current
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-4">
                                            <button type="button"
                                                onclick="restoreBackup('<?php echo htmlspecialchars($backup['path']); ?>', '<?php echo htmlspecialchars($backup['filename']); ?>')"
                                                <?php echo !$dbTest['success'] ? 'disabled' : ''; ?>
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Restore
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Show More/Less Button -->
                            <?php if ($totalBackups > $initialShow): ?>
                                <div class="px-6 py-4 text-center" id="showMoreContainer">
                                    <button type="button"
                                        id="showMoreBtn"
                                        onclick="toggleShowMore()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Show More (<?php echo $totalBackups - $initialShow; ?> more)
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Backup Files Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                No backup files were found in: <code class="bg-gray-100 px-1 rounded"><?php echo htmlspecialchars(getBackupDirectoryDisplay()); ?></code>
                            </p>
                            <p class="mt-1 text-sm text-gray-500">Please ensure your backup files are stored in the correct location.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h3 class="text-lg font-medium text-blue-900 mb-3">Instructions</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700">
                        <li>Review the current system version displayed above</li>
                        <li>Use the search bar to filter backup files by name</li>
                        <li>Browse the available backup files - each shows its version and comparison to current</li>
                        <li>Click "Show More" to view additional backup files (shows latest 5 by default)</li>
                        <li>Click the "Restore" button next to any backup file to begin restoration</li>
                        <li>Wait for the process to complete (this may take several minutes)</li>
                        <li>Check the result message that appears above</li>
                        <li><strong>IMPORTANT:</strong> Remove this file after use for security!</li>
                    </ol>
                    <div class="mt-3 p-3 bg-blue-100 rounded-md">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Version Information:</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Green</span> = Newer version than current</li>
                            <li><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Yellow</span> = Older version than current</li>
                            <li><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Blue</span> = Same version as current</li>
                            <li><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Gray</span> = Version unknown</li>
                        </ul>
                        <?php if (!$dbBackupStatus['version_column_exists'] && $dbBackupStatus['table_exists']): ?>
                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-xs text-yellow-800">
                                    <strong>Note:</strong> Version column is missing from database. Run the migration to enable version tracking:
                                    <code class="bg-yellow-100 px-1 rounded">php artisan migrate --path=Modules/Backup/Database/Migrations</code>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isShowingAll = false;
        let allBackupItems = [];
        let filteredBackupItems = [];

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing backup list...');
            initializeBackupList();
            setupSearch();
            console.log('Backup list initialized');
        });

        function initializeBackupList() {
            allBackupItems = Array.from(document.querySelectorAll('.backup-item'));
            filteredBackupItems = [...allBackupItems];
            console.log('Found', allBackupItems.length, 'backup items');
            updateBackupCount();
            updateShowMoreButton();
        }

        function setupSearch() {
            const searchInput = document.getElementById('searchBackups');
            const clearButton = document.getElementById('clearSearch');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                filterBackups(searchTerm);

                // Show/hide clear button
                if (searchTerm.length > 0) {
                    clearButton.classList.remove('hidden');
                } else {
                    clearButton.classList.add('hidden');
                }
            });

            // Handle Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }

        function filterBackups(searchTerm) {
            const backupItems = document.querySelectorAll('.backup-item');
            let visibleCount = 0;

            backupItems.forEach(item => {
                const filename = item.getAttribute('data-filename');
                const matches = searchTerm === '' || filename.includes(searchTerm);

                if (matches) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            // Update filtered items array
            filteredBackupItems = Array.from(backupItems).filter(item =>
                !item.classList.contains('hidden')
            );

            // Reset show more state when searching
            isShowingAll = false;
            updateShowMoreButton();
            updateBackupCount(visibleCount);
        }

        function clearSearch() {
            const searchInput = document.getElementById('searchBackups');
            searchInput.value = '';
            searchInput.focus();
            filterBackups('');
        }

        function toggleShowMore() {
            const allBackupItems = document.querySelectorAll('.backup-item');
            const showMoreBtn = document.getElementById('showMoreBtn');

            console.log('Toggle show more clicked. Current state:', isShowingAll);
            console.log('Total backup items:', allBackupItems.length);

            if (!isShowingAll) {
                // Show all items
                allBackupItems.forEach(item => {
                    item.classList.remove('hidden');
                });
                isShowingAll = true;
                showMoreBtn.innerHTML = `
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Show Less
                `;
                console.log('Now showing all items');
            } else {
                // Show only first 5 items
                let visibleCount = 0;
                allBackupItems.forEach(item => {
                    if (visibleCount < 5) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                isShowingAll = false;

                const totalItems = allBackupItems.length;
                const moreCount = totalItems - 5;

                showMoreBtn.innerHTML = `
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Show More (${moreCount} more)
                `;
                console.log('Now showing first 5 items, hiding', moreCount, 'items');
            }
        }

        function updateShowMoreButton() {
            const allItems = document.querySelectorAll('.backup-item');
            const visibleItems = document.querySelectorAll('.backup-item:not(.hidden)');
            const showMoreContainer = document.getElementById('showMoreContainer');
            const showMoreBtn = document.getElementById('showMoreBtn');

            // If there are 5 or fewer items total, hide the show more button
            if (allItems.length <= 5) {
                if (showMoreContainer) {
                    showMoreContainer.classList.add('hidden');
                }
                return;
            }

            // Show the container
            if (showMoreContainer) {
                showMoreContainer.classList.remove('hidden');
            }

            if (!isShowingAll) {
                showMoreBtn.innerHTML = `
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Show More (${allItems.length - 5} more)
                `;
            } else {
                showMoreBtn.innerHTML = `
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Show Less
                `;
            }
        }

        function updateBackupCount(count = null) {
            const countElement = document.getElementById('backupCount');
            if (countElement) {
                if (count !== null) {
                    countElement.textContent = count;
                } else {
                    const visibleItems = document.querySelectorAll('.backup-item:not(.hidden)');
                    countElement.textContent = visibleItems.length;
                }
            }
        }

        function clearCache() {
            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Clear Cache',
                text: 'Are you sure you want to clear all cache files? This will delete all files in the bootstrap/cache folder.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clear cache!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performClearCache();
                }
            });

            return false;
        }

        function performClearCache() {
            // Find the clear cache button
            const clearCacheBtn = document.querySelector('button[onclick="clearCache()"]');

            if (!clearCacheBtn) {
                alert('Error: Could not find the clear cache button');
                return false;
            }

            // Add loading state to the button
            const originalButtonText = clearCacheBtn.innerHTML;
            clearCacheBtn.disabled = true;
            clearCacheBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Clearing...
            `;

            // Show progress message
            const progressDiv = document.createElement('div');
            progressDiv.id = 'clearCacheProgress';
            progressDiv.className = 'bg-orange-50 border border-orange-200 rounded-md p-4';
            progressDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="animate-spin h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-orange-700">
                            <strong>🗑️ Clearing Cache:</strong> Deleting all files in bootstrap/cache folder. Please wait...
                        </p>
                    </div>
                </div>
            `;

            // Insert progress div after the first status message or at the top
            const firstStatusDiv = document.querySelector('.bg-green-50, .bg-red-50');
            if (firstStatusDiv) {
                firstStatusDiv.parentNode.insertBefore(progressDiv, firstStatusDiv.nextSibling);
            } else {
                const warningDiv = document.querySelector('.bg-yellow-50');
                if (warningDiv) {
                    warningDiv.parentNode.insertBefore(progressDiv, warningDiv.nextSibling);
                }
            }

            // Scroll to top to show progress
            window.scrollTo(0, 0);

            // Prepare form data
            const formData = new FormData();
            formData.append('clear_cache', '1');

            // Make AJAX request
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Remove progress div
                    if (progressDiv.parentNode) {
                        progressDiv.parentNode.removeChild(progressDiv);
                    }

                    // Show result with SweetAlert
                    if (data.type === 'success') {
                        Swal.fire({
                            title: 'Cache Cleared!',
                            text: data.message || 'All cache files have been successfully deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Cache Clear Failed',
                            html: `
                                <p>Failed to clear cache files.</p>
                                <details class="mt-3 text-left">
                                    <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium">View Error Details</summary>
                                    <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto text-left">${data.message}</pre>
                                </details>
                            `,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    // Remove progress div
                    if (progressDiv.parentNode) {
                        progressDiv.parentNode.removeChild(progressDiv);
                    }

                    // Show error with SweetAlert
                    Swal.fire({
                        title: 'Network Error',
                        html: `
                            <p>Failed to connect to the server while clearing cache.</p>
                            <details class="mt-3 text-left">
                                <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium">View Error Details</summary>
                                <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto text-left">${error.message}</pre>
                            </details>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    // Reset button state
                    clearCacheBtn.disabled = false;
                    clearCacheBtn.innerHTML = originalButtonText;
                });

            return false;
        }

        function restoreBackup(backupPath, backupFilename) {
            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Confirm Restore',
                text: `Are you absolutely sure you want to restore the backup "${backupFilename}"? This will overwrite all current data and files!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performRestore(backupPath, backupFilename);
                }
            });

            return false;
        }

        function performRestore(backupPath, backupFilename) {

            // Find the button that was clicked
            const buttons = document.querySelectorAll('button[onclick*="restoreBackup"]');
            let clickedButton = null;
            for (let button of buttons) {
                if (button.getAttribute('onclick').includes(backupPath)) {
                    clickedButton = button;
                    break;
                }
            }

            if (!clickedButton) {
                alert('Error: Could not find the restore button');
                return false;
            }

            // Add loading state to the clicked button
            const originalButtonText = clickedButton.innerHTML;
            clickedButton.disabled = true;
            clickedButton.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Restoring...
            `;

            // Disable all other restore buttons
            buttons.forEach(button => {
                if (button !== clickedButton) {
                    button.disabled = true;
                }
            });

            // Show progress message
            const progressDiv = document.createElement('div');
            progressDiv.id = 'restoreProgress';
            progressDiv.className = 'bg-blue-50 border border-blue-200 rounded-md p-4';
            progressDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="animate-spin h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>🔄 Restore in Progress:</strong> Restoring backup "${backupFilename}". Please wait while the database and files are being restored. This may take several minutes...
                        </p>
                    </div>
                </div>
            `;

            // Insert progress div after the first status message or at the top
            const firstStatusDiv = document.querySelector('.bg-green-50, .bg-red-50');
            if (firstStatusDiv) {
                firstStatusDiv.parentNode.insertBefore(progressDiv, firstStatusDiv.nextSibling);
            } else {
                const warningDiv = document.querySelector('.bg-yellow-50');
                if (warningDiv) {
                    warningDiv.parentNode.insertBefore(progressDiv, warningDiv.nextSibling);
                }
            }

            // Scroll to top to show progress
            window.scrollTo(0, 0);

            // Prepare form data
            const formData = new FormData();
            formData.append('backup_file', backupPath);
            formData.append('restore', '1');

            // Make AJAX request
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Remove progress div
                    if (progressDiv.parentNode) {
                        progressDiv.parentNode.removeChild(progressDiv);
                    }

                    // Show result with SweetAlert
                    if (data.type === 'success') {
                        Swal.fire({
                            title: 'Restore Successful!',
                            text: `Backup "${backupFilename}" has been restored successfully.`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Restore Failed',
                            html: `
                                <p>Failed to restore backup <strong>${backupFilename}</strong>.</p>
                                <details class="mt-3 text-left">
                                    <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium">View Error Details</summary>
                                    <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto text-left">${data.message}</pre>
                                </details>
                            `,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    // Remove progress div
                    if (progressDiv.parentNode) {
                        progressDiv.parentNode.removeChild(progressDiv);
                    }

                    // Show error with SweetAlert
                    Swal.fire({
                        title: 'Network Error',
                        html: `
                            <p>Failed to connect to the server while restoring <strong>${backupFilename}</strong>.</p>
                            <details class="mt-3 text-left">
                                <summary class="cursor-pointer text-red-600 hover:text-red-800 font-medium">View Error Details</summary>
                                <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto text-left">${error.message}</pre>
                            </details>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    // Reset button states
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalButtonText;

                    // Re-enable all other restore buttons
                    buttons.forEach(button => {
                        if (button !== clickedButton) {
                            button.disabled = false;
                        }
                    });
                });

            return false;
        }
    </script>
</body>

</html>
