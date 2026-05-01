<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_licenses' => License::count(),
            'active_licenses' => License::where('status', 'active')->count(),
            'expired_licenses' => License::where('status', 'expired')
                ->orWhere(fn ($q) => $q->where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<', now()))
                ->count(),
            'total_activations' => LicenseActivation::where('is_active', true)->count(),
            'suspended_licenses' => License::where('status', 'suspended')->count(),
        ];

        $recentLicenses = License::with('product')
            ->latest()
            ->take(10)
            ->get();

        $recentActivations = LicenseActivation::with('license.product')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentLicenses', 'recentActivations'));
    }
}
