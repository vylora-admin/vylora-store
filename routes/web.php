<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AppFileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AppUserController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('install')->group(function () {
    Route::get('/', [InstallerController::class, 'show'])->name('installer.show');
    Route::post('/', [InstallerController::class, 'run'])->name('installer.run');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('profile/2fa')->group(function () {
        Route::get('/', [TwoFactorController::class, 'show'])->name('profile.2fa');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('profile.2fa.enable');
        Route::post('/confirm', [TwoFactorController::class, 'confirm'])->name('profile.2fa.confirm');
        Route::delete('/', [TwoFactorController::class, 'disable'])->name('profile.2fa.disable');
    });

    Route::resource('products', ProductController::class);

    Route::resource('licenses', LicenseController::class);
    Route::get('licenses-bulk/create', [LicenseController::class, 'bulkCreate'])->name('licenses.bulk-create');
    Route::post('licenses-bulk', [LicenseController::class, 'bulkStore'])->name('licenses.bulk-store');
    Route::patch('licenses/{license}/suspend', [LicenseController::class, 'suspend'])->name('licenses.suspend');
    Route::patch('licenses/{license}/revoke', [LicenseController::class, 'revoke'])->name('licenses.revoke');
    Route::patch('licenses/{license}/reactivate', [LicenseController::class, 'reactivate'])->name('licenses.reactivate');
    Route::get('licenses-export', [LicenseController::class, 'export'])->name('licenses.export');

    Route::resource('applications', ApplicationController::class);
    Route::patch('applications/{application}/reset-secret', [ApplicationController::class, 'resetSecret'])->name('applications.reset-secret');
    Route::patch('applications/{application}/pause', [ApplicationController::class, 'pause'])->name('applications.pause');

    Route::prefix('applications/{application}')->group(function () {
        Route::get('users', [AppUserController::class, 'index'])->name('applications.users.index');
        Route::get('users/create', [AppUserController::class, 'create'])->name('applications.users.create');
        Route::post('users', [AppUserController::class, 'store'])->name('applications.users.store');
        Route::get('users/{user}', [AppUserController::class, 'show'])->name('applications.users.show');
        Route::delete('users/{user}', [AppUserController::class, 'destroy'])->name('applications.users.destroy');
        Route::patch('users/{user}/ban', [AppUserController::class, 'ban'])->name('applications.users.ban');
        Route::patch('users/{user}/unban', [AppUserController::class, 'unban'])->name('applications.users.unban');
        Route::patch('users/{user}/reset-hwid', [AppUserController::class, 'resetHwid'])->name('applications.users.reset-hwid');
        Route::patch('users/{user}/extend', [AppUserController::class, 'extend'])->name('applications.users.extend');

        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('applications.subscriptions.index');
        Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('applications.subscriptions.store');
        Route::patch('subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('applications.subscriptions.update');
        Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('applications.subscriptions.destroy');

        Route::get('files', [AppFileController::class, 'index'])->name('applications.files.index');
        Route::post('files', [AppFileController::class, 'store'])->name('applications.files.store');
        Route::patch('files/{file}/toggle', [AppFileController::class, 'toggle'])->name('applications.files.toggle');
        Route::delete('files/{file}', [AppFileController::class, 'destroy'])->name('applications.files.destroy');

        Route::get('variables', [VariableController::class, 'index'])->name('applications.variables.index');
        Route::post('variables', [VariableController::class, 'store'])->name('applications.variables.store');
        Route::patch('variables/{variable}', [VariableController::class, 'update'])->name('applications.variables.update');
        Route::delete('variables/{variable}', [VariableController::class, 'destroy'])->name('applications.variables.destroy');

        Route::get('webhooks', [WebhookController::class, 'index'])->name('applications.webhooks.index');
        Route::post('webhooks', [WebhookController::class, 'store'])->name('applications.webhooks.store');
        Route::patch('webhooks/{webhook}', [WebhookController::class, 'update'])->name('applications.webhooks.update');
        Route::delete('webhooks/{webhook}', [WebhookController::class, 'destroy'])->name('applications.webhooks.destroy');
        Route::post('webhooks/{webhook}/test', [WebhookController::class, 'test'])->name('applications.webhooks.test');

        Route::get('blacklist', [BlacklistController::class, 'index'])->name('applications.blacklist.index');
        Route::post('blacklist', [BlacklistController::class, 'store'])->name('applications.blacklist.store');
        Route::delete('blacklist/{blacklist}', [BlacklistController::class, 'destroy'])->name('applications.blacklist.destroy');

        Route::get('chat', [ChatController::class, 'index'])->name('applications.chat.index');
        Route::post('chat', [ChatController::class, 'store'])->name('applications.chat.store');
        Route::get('chat/{channel}', [ChatController::class, 'show'])->name('applications.chat.show');
        Route::delete('chat/{channel}', [ChatController::class, 'destroy'])->name('applications.chat.destroy');
        Route::delete('chat/{channel}/messages/{message}', [ChatController::class, 'deleteMessage'])->name('applications.chat.delete-message');
    });

    Route::middleware('admin')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('addons', [AddonController::class, 'index'])->name('addons.index');
        Route::post('addons/{addon}/toggle', [AddonController::class, 'toggle'])->name('addons.toggle');
        Route::post('addons/{addon}/configure', [AddonController::class, 'configure'])->name('addons.configure');
        Route::post('addons/rescan', [AddonController::class, 'rescan'])->name('addons.rescan');

        Route::get('sellers', [SellerController::class, 'index'])->name('sellers.index');
        Route::get('sellers/create', [SellerController::class, 'create'])->name('sellers.create');
        Route::post('sellers', [SellerController::class, 'store'])->name('sellers.store');
        Route::get('sellers/{seller}', [SellerController::class, 'show'])->name('sellers.show');
        Route::post('sellers/{seller}/balance', [SellerController::class, 'adjustBalance'])->name('sellers.balance');
        Route::delete('sellers/{seller}', [SellerController::class, 'destroy'])->name('sellers.destroy');

        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
            Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
            Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
            Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
            Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
            Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit-logs');
        });
    });
});

require __DIR__.'/auth.php';
