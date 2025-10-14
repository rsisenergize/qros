<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->validateCsrfTokens(except: [
            '*-webhook/*',
            '*/webhook/*',
            '*_webhook/*',
            '*_webhook',
            '*-webhook',
            '*/billing-verify-webhook/*',
            'custom-modules/*',
            '*/save-paypal-webhook/*',
            '*/payfast-notification/*',
            '*/png',
            'receipt/png',
            'kot/png',
            'order/png'
        ]);

        // Add CORS middleware globally to handle all CORS requests
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
