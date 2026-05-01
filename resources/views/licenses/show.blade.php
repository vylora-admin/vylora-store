@extends('layouts.app')
@section('title', 'License Details')
@section('subtitle', $license->license_key)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="glass rounded-2xl p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $license->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                            {{ $license->status === 'expired' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                            {{ $license->status === 'revoked' ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' : '' }}
                        ">
                            <span class="w-2 h-2 rounded-full {{ $license->status === 'active' ? 'bg-emerald-500' : ($license->status === 'expired' ? 'bg-red-500' : ($license->status === 'suspended' ? 'bg-yellow-500' : 'bg-gray-400')) }}"></span>
                            {{ ucfirst($license->status) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">{{ ucfirst($license->type) }}</span>
                    </div>
                    <p class="font-mono text-lg font-bold text-gray-900 dark:text-white tracking-wide" x-data="{ copied: false }">
                        {{ $license->license_key }}
                        <button @click="navigator.clipboard.writeText('{{ $license->license_key }}'); copied = true; setTimeout(() => copied = false, 2000)" class="ml-2 text-sm text-gray-400 hover:text-primary-500 transition">
                            <i :class="copied ? 'fas fa-check text-emerald-500' : 'fas fa-copy'"></i>
                        </button>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('licenses.edit', $license) }}" class="w-9 h-9 rounded-xl glass flex items-center justify-center text-gray-500 hover:text-primary-500 transition"><i class="fas fa-pen text-sm"></i></a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Product</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $license->product->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Activations</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $license->current_activations }} / {{ $license->max_activations }}</p>
                </div>
                <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Created</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $license->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Expires</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $license->expires_at ? $license->expires_at->format('M d, Y') : 'Never' }}</p>
                </div>
            </div>
        </div>

        {{-- Activations Table --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-laptop text-primary-500 mr-2"></i>Device Activations</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase text-gray-500 border-b border-gray-200/50 dark:border-gray-700/50">
                            <th class="pb-2 text-left">Hardware ID</th>
                            <th class="pb-2 text-left">Machine</th>
                            <th class="pb-2 text-left">IP</th>
                            <th class="pb-2 text-left">Status</th>
                            <th class="pb-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                        @forelse($license->activations as $activation)
                        <tr>
                            <td class="py-2.5 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $activation->hardware_id }}</td>
                            <td class="py-2.5 text-xs text-gray-600 dark:text-gray-400">{{ $activation->machine_name ?? '-' }}</td>
                            <td class="py-2.5 text-xs text-gray-600 dark:text-gray-400">{{ $activation->ip_address ?? '-' }}</td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $activation->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $activation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-2.5 text-xs text-gray-500">{{ $activation->activated_at?->diffForHumans() ?? $activation->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400 text-xs">No activations yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Customer Info --}}
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3"><i class="fas fa-user text-blue-500 mr-2"></i>Customer</h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="text-gray-400">Name:</span> {{ $license->customer_name ?? 'N/A' }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="text-gray-400">Email:</span> {{ $license->customer_email ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3"><i class="fas fa-cog text-gray-500 mr-2"></i>Actions</h3>
            <div class="space-y-2">
                @if($license->status === 'active')
                <form method="POST" action="{{ route('licenses.suspend', $license) }}">
                    @csrf @method('PATCH')
                    <button class="w-full px-4 py-2 rounded-xl text-sm font-medium text-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-300 hover:bg-yellow-100 transition text-left"><i class="fas fa-pause mr-2"></i>Suspend</button>
                </form>
                <form method="POST" action="{{ route('licenses.revoke', $license) }}" onsubmit="return confirm('Revoke this license? All activations will be deactivated.')">
                    @csrf @method('PATCH')
                    <button class="w-full px-4 py-2 rounded-xl text-sm font-medium text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-300 hover:bg-red-100 transition text-left"><i class="fas fa-ban mr-2"></i>Revoke</button>
                </form>
                @elseif($license->status === 'suspended')
                <form method="POST" action="{{ route('licenses.reactivate', $license) }}">
                    @csrf @method('PATCH')
                    <button class="w-full px-4 py-2 rounded-xl text-sm font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-300 hover:bg-emerald-100 transition text-left"><i class="fas fa-play mr-2"></i>Reactivate</button>
                </form>
                @endif
                <form method="POST" action="{{ route('licenses.destroy', $license) }}" onsubmit="return confirm('Delete this license permanently?')">
                    @csrf @method('DELETE')
                    <button class="w-full px-4 py-2 rounded-xl text-sm font-medium text-gray-500 bg-gray-50 dark:bg-gray-800/30 dark:text-gray-400 hover:bg-gray-100 transition text-left"><i class="fas fa-trash mr-2"></i>Delete</button>
                </form>
            </div>
        </div>

        {{-- Notes --}}
        @if($license->notes)
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-2"><i class="fas fa-sticky-note text-amber-500 mr-2"></i>Notes</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $license->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
