<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationChatChannel;
use App\Models\ApplicationSubscription;
use App\Models\ApplicationUser;
use App\Models\ApplicationVariable;
use App\Models\Setting;
use App\Models\User;
use App\Services\AddonManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@keyvault.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        Setting::set('site_name', 'KeyVault Pro', 'string', 'general');
        Setting::set('accent_color', '#6366f1', 'string', 'theme');
        Setting::set('theme_mode', 'dark', 'string', 'theme');
        Setting::set('enable_glassmorphism', '1', 'bool', 'theme');
        Setting::set('enable_animations', '1', 'bool', 'theme');

        if (Application::count() === 0) {
            $app = Application::create([
                'owner_id' => $admin->id,
                'name' => 'DemoApp',
                'version' => '1.0.0',
                'description' => 'A demo application showcasing KeyVault Pro features.',
                'allow_register' => true,
                'allow_login' => true,
                'allow_extend' => true,
                'hwid_check_enabled' => true,
                'integrity_check_enabled' => false,
                'default_subscription_days' => 30,
            ]);

            ApplicationSubscription::create([
                'application_id' => $app->id,
                'name' => 'Free',
                'level' => 1,
                'price' => 0,
                'default_days' => 7,
                'description' => 'Free 7-day trial tier',
                'is_active' => true,
            ]);
            ApplicationSubscription::create([
                'application_id' => $app->id,
                'name' => 'Pro',
                'level' => 5,
                'price' => 9.99,
                'default_days' => 30,
                'description' => 'Monthly Pro subscription',
                'is_active' => true,
            ]);
            ApplicationSubscription::create([
                'application_id' => $app->id,
                'name' => 'Lifetime',
                'level' => 10,
                'price' => 99.99,
                'default_days' => 36500,
                'description' => 'One-time payment, lifetime access',
                'is_active' => true,
            ]);

            ApplicationUser::create([
                'application_id' => $app->id,
                'username' => 'demo_user',
                'email' => 'demo@example.com',
                'password_hash' => Hash::make('demopass'),
                'level' => 5,
                'expires_at' => now()->addMonth(),
                'last_login_at' => now()->subHour(),
                'last_ip' => '203.0.113.42',
                'hwid' => 'demo-hwid-7331',
            ]);

            ApplicationVariable::create([
                'application_id' => $app->id,
                'scope' => 'global',
                'key' => 'welcome_message',
                'value' => 'Welcome to DemoApp! Your auth is powered by KeyVault Pro.',
                'required_level' => 0,
            ]);
            ApplicationVariable::create([
                'application_id' => $app->id,
                'scope' => 'global',
                'key' => 'pro_only_secret',
                'value' => 'This variable is only visible to L5+ subscribers.',
                'required_level' => 5,
            ]);

            ApplicationChatChannel::create([
                'application_id' => $app->id,
                'name' => 'general',
                'description' => 'General chat for all users',
                'required_level' => 0,
                'is_active' => true,
            ]);
            ApplicationChatChannel::create([
                'application_id' => $app->id,
                'name' => 'pro-lounge',
                'description' => 'Exclusive channel for Pro subscribers',
                'required_level' => 5,
                'is_active' => true,
            ]);
        }

        AddonManager::sync();
    }
}
