@extends('layouts.app')
@section('title', 'Create User')
@section('subtitle', 'Add a new system user')

@section('content')
<div class="max-w-xl">
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Role *</label>
                <select name="role" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                    <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Password *</label>
                <input type="password" name="password" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-purple-500/25 hover:shadow-xl transition-all">
                    <i class="fas fa-user-plus"></i> Create User
                </button>
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 rounded-xl glass text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
