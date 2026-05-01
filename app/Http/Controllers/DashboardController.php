<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_licenses' => License::count(),
            'active_licenses' => License::where('status', 'active')->count(),
            'expired_licenses' => License::where('status', 'expired')->count(),
            'suspended_licenses' => License::where('status', 'suspended')->count(),
            'total_activations' => LicenseActivation::where('is_active', true)->count(),
            'total_users' => User::count(),
            'revoked_licenses' => License::where('status', 'revoked')->count(),
        ];

        $recentLicenses = License::with('product')->latest()->limit(8)->get();
        $recentActivations = LicenseActivation::with('license.product')->latest()->limit(5)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->limit(10)->get();

        $licensesPerProduct = Product::withCount('licenses')
            ->orderByDesc('licenses_count')
            ->limit(6)
            ->get();

        $licenseTrend = License::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentLicenses',
            'recentActivations',
            'recentAuditLogs',
            'licensesPerProduct',
            'licenseTrend'
        ));
    }
}
