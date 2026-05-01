@extends('layouts.app')

@section('title', 'Licenses')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Licenses</h1>
        <p class="text-gray-600 mt-1">Manage all license keys</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('licenses.bulk-create') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-layer-group mr-1"></i>Bulk Generate
        </a>
        <a href="{{ route('licenses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i>New License
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="{{ route('licenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search key, name, email..."
                   class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border text-sm">
        </div>
        <div>
            <select name="product_id" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border text-sm">
                <option value="">All Statuses</option>
                @foreach(['active', 'inactive', 'expired', 'suspended', 'revoked'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border text-sm">
                <option value="">All Types</option>
                @foreach(['trial', 'standard', 'extended', 'lifetime'] as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            <a href="{{ route('licenses.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">License Key</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activations</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($licenses as $license)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('licenses.show', $license) }}" class="font-mono text-sm text-indigo-600 hover:underline">{{ Str::limit($license->license_key, 25) }}</a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $license->product->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $license->customer_name ?? $license->customer_email ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ ucfirst($license->type) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $license->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $license->status === 'revoked' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $license->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                        ">{{ ucfirst($license->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $license->current_activations }}/{{ $license->max_activations }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $license->expires_at ? $license->expires_at->format('M d, Y') : 'Never' }}</td>
                    <td class="px-6 py-4 text-right space-x-1">
                        <a href="{{ route('licenses.show', $license) }}" class="text-gray-600 hover:text-indigo-600"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('licenses.edit', $license) }}" class="text-gray-600 hover:text-indigo-600"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('licenses.destroy', $license) }}" method="POST" class="inline" onsubmit="return confirm('Delete this license?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-600 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-key text-4xl mb-2"></i>
                        <p>No licenses found. <a href="{{ route('licenses.create') }}" class="text-indigo-600 hover:underline">Create one</a>.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $licenses->links() }}
</div>
@endsection
