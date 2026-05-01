<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Product;
use App\Models\User;
use App\Services\LicenseKeyGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@keyvault.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create manager user
        User::updateOrCreate(
            ['email' => 'manager@keyvault.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        // Create regular user
        User::updateOrCreate(
            ['email' => 'user@keyvault.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $products = [
            [
                'name' => 'ProDesign Studio',
                'slug' => 'prodesign-studio',
                'description' => 'Professional graphic design and illustration software',
                'version' => '3.5.0',
                'is_active' => true,
            ],
            [
                'name' => 'CodeForge IDE',
                'slug' => 'codeforge-ide',
                'description' => 'Advanced integrated development environment for modern developers',
                'version' => '2.1.0',
                'is_active' => true,
            ],
            [
                'name' => 'DataVault Analytics',
                'slug' => 'datavault-analytics',
                'description' => 'Enterprise data analytics and visualization platform',
                'version' => '1.8.0',
                'is_active' => true,
            ],
        ];

        $generator = new LicenseKeyGenerator;

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );

            $types = ['standard', 'trial', 'extended', 'lifetime'];
            $formats = ['standard', 'extended', 'short', 'uuid'];
            $statuses = ['active', 'active', 'active', 'active', 'expired', 'suspended', 'revoked', 'inactive'];

            for ($i = 0; $i < 8; $i++) {
                $format = $formats[array_rand($formats)];
                License::create([
                    'license_key' => $generator->generate($format),
                    'product_id' => $product->id,
                    'customer_name' => fake()->name(),
                    'customer_email' => fake()->safeEmail(),
                    'type' => $types[array_rand($types)],
                    'status' => $statuses[$i],
                    'max_activations' => rand(1, 10),
                    'current_activations' => 0,
                    'expires_at' => $i < 5 ? now()->addMonths(rand(1, 12)) : ($i === 4 ? now()->subDays(rand(1, 30)) : null),
                ]);
            }
        }
    }
}
