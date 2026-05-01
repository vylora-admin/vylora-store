@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
        <p class="text-gray-600 mt-1">{{ $product->description ?? 'No description' }}</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('products.edit', $product) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
        <a href="{{ route('licenses.create') }}?product_id={{ $product->id }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-1"></i>New License
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Version</p>
        <p class="text-lg font-semibold">{{ $product->version ?? '-' }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Licenses</p>
        <p class="text-lg font-semibold">{{ $product->licenses_count }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Status</p>
        <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
            {{ $product->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Licenses</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">License Key</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activations</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($licenses as $license)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('licenses.show', $license) }}" class="font-mono text-sm text-indigo-600 hover:underline">{{ $license->license_key }}</a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $license->customer_name ?? $license->customer_email ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ ucfirst($license->type) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $license->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $license->status === 'revoked' ? 'bg-gray-100 text-gray-800' : '' }}
                        ">{{ ucfirst($license->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $license->current_activations }}/{{ $license->max_activations }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No licenses for this product.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $licenses->links() }}
</div>
@endsection
