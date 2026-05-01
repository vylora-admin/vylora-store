<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\LicenseKeyGenerator;
use App\Services\LicenseService;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'PhotoEditor Pro', 'description' => 'Professional photo editing software', 'version' => '3.2.1'],
            ['name' => 'CodeStudio IDE', 'description' => 'Integrated development environment', 'version' => '2.0.0'],
            ['name' => 'DataSync Cloud', 'description' => 'Cloud data synchronization tool', 'version' => '1.5.0'],
        ];

        $licenseService = new LicenseService(new LicenseKeyGenerator());

        foreach ($products as $productData) {
            $product = Product::create($productData);

            $licenseService->createBulkLicenses([
                'product_id' => $product->id,
                'type' => 'standard',
                'max_activations' => 3,
                'customer_name' => 'Demo Customer',
                'customer_email' => 'demo@example.com',
                'expires_at' => now()->addYear(),
            ], 5, 'standard');

            $licenseService->createBulkLicenses([
                'product_id' => $product->id,
                'type' => 'trial',
                'max_activations' => 1,
                'expires_at' => now()->addDays(14),
            ], 3, 'short');
        }
    }
}
