<?php

use App\Http\Controllers\Api\KeyAuthApiController;
use App\Http\Controllers\Api\LicenseApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/licenses')->group(function () {
    Route::post('/validate', [LicenseApiController::class, 'check'])->name('api.licenses.validate');
    Route::post('/activate', [LicenseApiController::class, 'activate'])->name('api.licenses.activate');
    Route::post('/deactivate', [LicenseApiController::class, 'deactivate'])->name('api.licenses.deactivate');
});

Route::any('/1.3/', [KeyAuthApiController::class, 'handle'])->name('api.keyauth');
Route::any('/1.3', [KeyAuthApiController::class, 'handle']);
Route::any('/v2/auth', [KeyAuthApiController::class, 'handle']);
