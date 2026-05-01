<?php

use App\Http\Controllers\Api\LicenseApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/licenses')->group(function () {
    Route::post('/validate', [LicenseApiController::class, 'check'])->name('api.licenses.validate');
    Route::post('/activate', [LicenseApiController::class, 'activate'])->name('api.licenses.activate');
    Route::post('/deactivate', [LicenseApiController::class, 'deactivate'])->name('api.licenses.deactivate');
});
