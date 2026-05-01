<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::resource('products', ProductController::class);

// Licenses
Route::resource('licenses', LicenseController::class);
Route::get('licenses-bulk/create', [LicenseController::class, 'bulkCreate'])->name('licenses.bulk-create');
Route::post('licenses-bulk', [LicenseController::class, 'bulkStore'])->name('licenses.bulk-store');
Route::patch('licenses/{license}/suspend', [LicenseController::class, 'suspend'])->name('licenses.suspend');
Route::patch('licenses/{license}/revoke', [LicenseController::class, 'revoke'])->name('licenses.revoke');
Route::patch('licenses/{license}/reactivate', [LicenseController::class, 'reactivate'])->name('licenses.reactivate');
