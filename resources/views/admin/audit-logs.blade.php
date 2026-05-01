@extends('layouts.app')
@section('title', 'Audit Logs')
@section('subtitle', 'System activity and change history')

@section('content')
{{-- Filters --}}
<div class="glass rounded-2xl p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="w-48">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Action</label>
            <select name="action" class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($action)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">User</label>
            <select name="user_id" class="w-full px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
                <option value="">All Users</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition"><i class="fas fa-filter mr-1"></i>Filter</button>
        <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 rounded-xl text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">Clear</a>
    </form>
</div>

{{-- Logs Timeline --}}
<div class="space-y-3">
    @forelse($logs as $log)
    <div class="glass rounded-xl p-4 hover:shadow-md transition">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ str_contains($log->action, 'created') ? 'bg-emerald-100 dark:bg-emerald-900/30' : '' }}
                {{ str_contains($log->action, 'updated') ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                {{ str_contains($log->action, 'deleted') ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                {{ str_contains($log->action, 'suspended') || str_contains($log->action, 'revoked') ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}
                {{ str_contains($log->action, 'exported') ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}
                {{ !str_contains($log->action, 'created') && !str_contains($log->action, 'updated') && !str_contains($log->action, 'deleted') && !str_contains($log->action, 'suspended') && !str_contains($log->action, 'revoked') && !str_contains($log->action, 'exported') ? 'bg-gray-100 dark:bg-gray-800/30' : '' }}
            ">
                <i class="fas fa-{{ str_contains($log->action, 'created') ? 'plus text-emerald-600' : (str_contains($log->action, 'deleted') ? 'trash text-red-600' : (str_contains($log->action, 'exported') ? 'download text-purple-600' : 'pen text-blue-600')) }} text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">{{ str_replace('_', ' ', $log->action) }}</span>
                    @if($log->model_type)
                    <span class="text-[10px] text-gray-400">{{ $log->model_name }} #{{ $log->model_id }}</span>
                    @endif
                </div>
                <p class="text-sm text-gray-800 dark:text-gray-200">{{ $log->description ?? $log->action }}</p>
                <div class="flex items-center gap-3 mt-1 text-[10px] text-gray-400">
                    <span><i class="fas fa-user mr-1"></i>{{ $log->user?->name ?? 'System' }}</span>
                    <span><i class="fas fa-clock mr-1"></i>{{ $log->created_at->diffForHumans() }}</span>
                    @if($log->ip_address)
                    <span><i class="fas fa-globe mr-1"></i>{{ $log->ip_address }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="glass rounded-2xl p-12 text-center">
        <i class="fas fa-clock-rotate-left text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-400">No audit logs yet</p>
    </div>
    @endforelse
</div>

@if($logs->hasPages())
<div class="mt-6">{{ $logs->links() }}</div>
@endif
@endsection
