<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Module Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-lg w-full mx-4">
        <!-- Warning Icon -->
        <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-4">Backup Module Not Found</h1>

        <!-- Description -->
        <p class="text-gray-600 text-center mb-6 leading-relaxed">
            The backup and restore functionality requires the Backup Module to be installed. This module provides essential backup and restore capabilities for your application.
        </p>

        <!-- Missing File Info -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Missing File:</p>
            <code class="text-sm text-gray-800 font-mono">Modules/Backup/Restore/restore-backup-simple.php</code>
        </div>

        <!-- Installation Steps -->
        <div class="mb-6">
            <p class="font-semibold text-gray-800 mb-3">To install the Backup Module:</p>
            <ol class="text-sm text-gray-600 space-y-2 list-decimal list-inside">
                <li>Ensure you have purchased the Backup Module</li>
                <li>Download the module files from your purchase</li>
                <li>Follow the installation instructions in the module documentation</li>

            </ol>
        </div>

        <!-- Action Button -->
        <div class="text-center mb-6">
            <a href="/" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                Return to Homepage
            </a>
        </div>

        <!-- Help Text -->
        <p class="text-xs text-gray-500 text-center">
            If you need assistance, please contact your system administrator or the module developer.
        </p>
    </div>
</body>

</html>
