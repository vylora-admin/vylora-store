<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class InstallerController extends Controller
{
    public function show()
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('installer.show', [
            'php_version_ok' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'pdo_ok' => extension_loaded('pdo'),
            'mbstring_ok' => extension_loaded('mbstring'),
            'storage_writable' => is_writable(storage_path()),
        ]);
    }

    public function run(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'admin_name' => 'required|string|max:120',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
            'site_name' => 'nullable|string|max:120',
        ]);
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
        }
        $admin = User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'admin',
        ]);
        if (! empty($validated['site_name'])) {
            \App\Models\Setting::set('site_name', $validated['site_name'], 'string', 'general');
        }
        auth()->login($admin);

        return redirect()->route('dashboard')->with('success', 'Welcome to KeyVault Pro!');
    }
}
