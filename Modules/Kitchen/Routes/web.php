<?php

use Illuminate\Support\Facades\Route;
use Modules\Kitchen\Http\Controllers\KitchenController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Specific routes first to avoid conflicts with resource routes
    Route::get('kitchens/all-kot', [KitchenController::class, 'allKot'])->name('kitchen.all-kot.index');
    Route::get('kitchens/kot/{id}', [KitchenController::class, 'showKot'])->name('kitchen.kot.show');

    // Resource route last
    Route::resource('kitchens', KitchenController::class)->names('kitchen.kitchen-places');
});
