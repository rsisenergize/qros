<?php

use Illuminate\Support\Facades\Route;
use Modules\Kiosk\Http\Controllers\KioskController;
use App\Http\Middleware\LocaleMiddleware;

Route::middleware([LocaleMiddleware::class])->prefix('kiosk')->group(function () {
    Route::get('restaurant/{hash}', [KioskController::class, 'index'])->name('kiosk.restaurant');
    Route::get('order-confirmation/{uuid}', [KioskController::class, 'orderConfirmation'])->name('kiosk.order-confirmation');
});
