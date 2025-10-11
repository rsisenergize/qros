<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintJobController;
use App\Http\Middleware\DesktopUniqueKeyMiddleware;
use App\Http\Middleware\CorsMiddleware;

// called by Electron every X seconds
Route::middleware(DesktopUniqueKeyMiddleware::class)->group(function () {
    Route::get('/test-connection', [PrintJobController::class, 'testConnection']);

    //Single job pull
    Route::get('/print-jobs/pull', [PrintJobController::class, 'pull']);

    //Multiple job pull
    Route::get('/print-jobs/pull-multiple', [PrintJobController::class, 'pullMultiple']);

    Route::get('/printer-details', [PrintJobController::class, 'printerDetails']);
    // mark a job done/failed
    Route::patch('/print-jobs/{printJob}', [PrintJobController::class, 'update']);
    Route::get('/print-jobs/printer/{printerId}/jobs', [PrintJobController::class, 'getPrinterJobs']);

    // Mark print job as completed
    Route::post('/print-jobs/{printJobId}/complete', [PrintJobController::class, 'complete']);
    Route::post('/print-jobs/{printJobId}/failed', [PrintJobController::class, 'failed']);
    Route::get('/print-jobs/pending/{printId}', [PrintJobController::class, 'pending']);
});

// Pusher connection logging (optional - for monitoring usage)
Route::post('/log-pusher-connection', function (Request $request) {
    // Simple logging - you can enhance this to store in database
    \Illuminate\Support\Facades\Log::info('Pusher connection', [
        'socket_id' => $request->socket_id,
        'connection_id' => $request->connection_id,
        'component' => $request->component,
        'timestamp' => $request->timestamp,
        'ip' => $request->ip()
    ]);

    return response()->json(['status' => 'logged']);
});

// Temporarily disable Pusher due to quota issues
Route::post('/disable-pusher-temporarily', function (Request $request) {
    $pusherSetting = \App\Models\PusherSetting::first();
    if ($pusherSetting) {
        $pusherSetting->update(['is_enabled_pusher_broadcast' => false]);
        \Illuminate\Support\Facades\Log::info('Pusher temporarily disabled due to quota issues');
    }

    return response()->json(['status' => 'disabled']);
});

// Force disconnect all Pusher connections (for quota cleanup)
Route::post('/force-disconnect-pusher', function (Request $request) {
    // Log the disconnect request
    \Illuminate\Support\Facades\Log::info('Force disconnect Pusher connections requested', [
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    return response()->json([
        'status' => 'disconnected',
        'message' => 'All connections should be disconnected. Reload pages to reconnect.'
    ]);
});
