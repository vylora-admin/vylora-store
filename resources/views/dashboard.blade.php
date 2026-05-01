@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-1">License management overview</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 rounded-full">
                <i class="fas fa-box text-indigo-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Products</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-id-card text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Licenses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_licenses'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Active Licenses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['active_licenses'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 rounded-full">
                <i class="fas fa-clock text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Expired Licenses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['expired_licenses'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-ban text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Suspended Licenses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['suspended_licenses'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-desktop text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Active Activations</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_activations'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Licenses</h2>
            <a href="{{ route('licenses.index') }}" class="text-indigo-600 text-sm hover:underline">View All</a>
        </div>
        <div class="divide-y">
            @forelse($recentLicenses as $license)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <a href="{{ route('licenses.show', $license) }}" class="text-sm font-mono text-indigo-600 hover:underline">
                            {{ $license->license_key }}
                        </a>
                        <p class="text-xs text-gray-500">{{ $license->product->name ?? 'N/A' }} &middot; {{ $license->customer_email ?? 'No email' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $license->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $license->status === 'revoked' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $license->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                    ">
                        {{ ucfirst($license->status) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-key text-4xl mb-2"></i>
                    <p>No licenses yet. <a href="{{ route('licenses.create') }}" class="text-indigo-600 hover:underline">Create one</a>.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activations</h2>
        </div>
        <div class="divide-y">
            @forelse($recentActivations as $activation)
                <div class="px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activation->machine_name ?? 'Unknown Device' }}</p>
                            <p class="text-xs text-gray-500">{{ $activation->license->license_key ?? 'N/A' }} &middot; {{ $activation->ip_address ?? '' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $activation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $activation->is_active ? 'Active' : 'Deactivated' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-desktop text-4xl mb-2"></i>
                    <p>No activations yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
