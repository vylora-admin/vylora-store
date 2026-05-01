@extends('layouts.app')
@section('title', 'User Management')
@section('subtitle', 'Manage system users and roles')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white w-64">
        <select name="role" class="px-3 py-2 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none dark:text-white">
            <option value="">All Roles</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        </select>
        <button class="px-4 py-2 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition"><i class="fas fa-search"></i></button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-purple-500/25 hover:shadow-xl transition-all">
        <i class="fas fa-user-plus"></i> Add User
    </a>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50/50 dark:bg-gray-800/30">
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Joined</th>
                    <th class="px-5 py-3">Last Login</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center text-white text-xs font-bold">{{ $user->initials }}</div>
                            <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                            {{ $user->role === 'user' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        ">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.users.edit', $user) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition"><i class="fas fa-pen text-xs"></i></a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Delete user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400"><i class="fas fa-users text-3xl mb-2 block opacity-30"></i>No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-5 py-3 border-t border-gray-200/50 dark:border-gray-700/50">{{ $users->links() }}</div>
    @endif
</div>
@endsection
