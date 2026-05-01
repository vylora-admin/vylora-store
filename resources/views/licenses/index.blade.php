@extends('layouts.app')
@section('title', 'Licenses')
@section('subtitle', 'Manage all license keys')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div class="flex gap-2">
        <a href="{{ route('licenses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 transition-all">
            <i class="fas fa-plus"></i> New License
        </a>
        <a href="{{ route('licenses.bulk-create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl glass text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
            <i class="fas fa-layer-group"></i> Bulk Generate
        </a>
        <a href="{{ route('licenses.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl glass text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
            <i class="fas fa-file-csv text-emerald-500"></i> Export CSV
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="glass rounded-2xl p-4 mb-6">
    <form method="GET" action="{{ route('licenses.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Key, name, email..." class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition dark:text-white">
        </div>
        <div class="w-40">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Product</label>
            <select name="product_id" class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
                <option value="">All Products</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
                <option value="">All</option>
                @foreach(['active','inactive','expired','suspended','revoked'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Type</label>
            <select name="type" class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
                <option value="">All</option>
                @foreach(['trial','standard','extended','lifetime'] as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition"><i class="fas fa-search mr-1"></i> Filter</button>
        <a href="{{ route('licenses.index') }}" class="px-4 py-2 rounded-xl text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50/50 dark:bg-gray-800/30">
                    <th class="px-5 py-3">License Key</th>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Activations</th>
                    <th class="px-5 py-3">Expires</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                @forelse($licenses as $license)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition group">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('licenses.show', $license) }}" class="font-mono text-xs text-primary-600 dark:text-primary-400 hover:underline font-medium">{{ $license->license_key }}</a>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-700 dark:text-gray-300">{{ $license->product->name ?? '-' }}</td>
                    <td class="px-5 py-3.5">
                        <div>
                            <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $license->customer_name ?? '-' }}</p>
                            @if($license->customer_email)
                            <p class="text-[10px] text-gray-400">{{ $license->customer_email }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold
                            {{ $license->type === 'standard' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $license->type === 'trial' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                            {{ $license->type === 'extended' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                            {{ $license->type === 'lifetime' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                        ">{{ ucfirst($license->type) }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $license->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                            {{ $license->status === 'expired' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                            {{ $license->status === 'revoked' ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : '' }}
                            {{ $license->status === 'inactive' ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' : '' }}
                        ">
                            <span class="w-1.5 h-1.5 rounded-full {{ $license->status === 'active' ? 'bg-emerald-500' : ($license->status === 'expired' ? 'bg-red-500' : ($license->status === 'suspended' ? 'bg-yellow-500' : 'bg-gray-400')) }}"></span>
                            {{ ucfirst($license->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-600 dark:text-gray-400">{{ $license->current_activations }}/{{ $license->max_activations }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $license->expires_at ? $license->expires_at->format('M d, Y') : 'Never' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('licenses.show', $license) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition" title="View"><i class="fas fa-eye text-xs"></i></a>
                            <a href="{{ route('licenses.edit', $license) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <form method="POST" action="{{ route('licenses.destroy', $license) }}" onsubmit="return confirm('Delete this license?')">
                                @csrf @method('DELETE')
                                <button class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400"><i class="fas fa-key text-3xl mb-2 block opacity-30"></i>No licenses found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($licenses->hasPages())
    <div class="px-5 py-3 border-t border-gray-200/50 dark:border-gray-700/50">
        {{ $licenses->links() }}
    </div>
    @endif
</div>
@endsection
