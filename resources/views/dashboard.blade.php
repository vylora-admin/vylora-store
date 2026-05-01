@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Overview of your license management system')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card glass rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                <i class="fas fa-cube text-white"></i>
            </div>
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-lg">Products</span>
        </div>
        <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['total_products']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Products</p>
    </div>
    <div class="stat-card glass rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
                <i class="fas fa-key text-white"></i>
            </div>
            <span class="text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded-lg">Keys</span>
        </div>
        <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['total_licenses']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Licenses</p>
    </div>
    <div class="stat-card glass rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                <i class="fas fa-circle-check text-white"></i>
            </div>
            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg">Active</span>
        </div>
        <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['active_licenses']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Active Licenses</p>
    </div>
    <div class="stat-card glass rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/25">
                <i class="fas fa-bolt text-white"></i>
            </div>
            <span class="text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-lg">Devices</span>
        </div>
        <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($stats['total_activations']) }}</p>
        <p class="text-xs text-gray-500 mt-1">Active Activations</p>
    </div>
</div>

{{-- Secondary Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-red-500">{{ $stats['expired_licenses'] }}</p>
        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-calendar-xmark mr-1"></i>Expired</p>
    </div>
    <div class="glass rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-yellow-500">{{ $stats['suspended_licenses'] }}</p>
        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-pause-circle mr-1"></i>Suspended</p>
    </div>
    <div class="glass rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-gray-500">{{ $stats['revoked_licenses'] }}</p>
        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-ban mr-1"></i>Revoked</p>
    </div>
    <div class="glass rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-purple-500">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-users mr-1"></i>Users</p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Licenses Per Product --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-chart-bar text-primary-500 mr-2"></i>Licenses Per Product</h3>
        <div class="space-y-3">
            @foreach($licensesPerProduct as $product)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $product->name }}</span>
                    <span class="text-gray-500">{{ $product->licenses_count }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full gradient-bg transition-all duration-500" style="width: {{ $stats['total_licenses'] > 0 ? ($product->licenses_count / $stats['total_licenses']) * 100 : 0 }}%"></div>
                </div>
            </div>
            @endforeach
            @if($licensesPerProduct->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">No products yet</p>
            @endif
        </div>
    </div>

    {{-- Recent Activity / Audit Log --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-clock-rotate-left text-rose-500 mr-2"></i>Recent Activity</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($recentAuditLogs as $log)
            <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-{{ str_contains($log->action, 'created') ? 'plus' : (str_contains($log->action, 'deleted') ? 'trash' : 'pen') }} text-xs text-primary-600 dark:text-primary-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $log->description ?? $log->action }}</p>
                    <p class="text-[10px] text-gray-400">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No activity yet</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Licenses Table --}}
<div class="glass rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white"><i class="fas fa-key text-emerald-500 mr-2"></i>Recent Licenses</h3>
        <a href="{{ route('licenses.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View All <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200/50 dark:border-gray-700/50">
                    <th class="pb-3 pr-4">Key</th>
                    <th class="pb-3 pr-4">Product</th>
                    <th class="pb-3 pr-4">Customer</th>
                    <th class="pb-3 pr-4">Type</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                @foreach($recentLicenses as $license)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition">
                    <td class="py-3 pr-4">
                        <a href="{{ route('licenses.show', $license) }}" class="font-mono text-xs text-primary-600 dark:text-primary-400 hover:underline">{{ Str::limit($license->license_key, 24) }}</a>
                    </td>
                    <td class="py-3 pr-4 text-xs text-gray-700 dark:text-gray-300">{{ $license->product->name ?? '-' }}</td>
                    <td class="py-3 pr-4 text-xs text-gray-600 dark:text-gray-400">{{ $license->customer_name ?? '-' }}</td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold
                            {{ $license->type === 'standard' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $license->type === 'trial' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                            {{ $license->type === 'extended' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                            {{ $license->type === 'lifetime' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                        ">{{ ucfirst($license->type) }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $license->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                            {{ $license->status === 'expired' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                            {{ $license->status === 'revoked' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}
                        ">
                            <span class="w-1.5 h-1.5 rounded-full {{ $license->status === 'active' ? 'bg-emerald-500' : ($license->status === 'expired' ? 'bg-red-500' : ($license->status === 'suspended' ? 'bg-yellow-500' : 'bg-gray-500')) }}"></span>
                            {{ ucfirst($license->status) }}
                        </span>
                    </td>
                    <td class="py-3 text-xs text-gray-500">{{ $license->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
