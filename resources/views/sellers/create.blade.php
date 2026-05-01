@extends('layouts.app')
@section('title', 'New Seller')

@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('sellers.store') }}" class="glass-strong rounded-2xl p-6 space-y-4">@csrf
        <div><label class="block text-xs font-semibold mb-1">Full name</label><input name="name" required class="input"></div>
        <div><label class="block text-xs font-semibold mb-1">Display name (public)</label><input name="display_name" class="input"></div>
        <div><label class="block text-xs font-semibold mb-1">Email</label><input name="email" type="email" required class="input"></div>
        <div><label class="block text-xs font-semibold mb-1">Password</label><input name="password" type="password" required class="input"></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-xs font-semibold mb-1">Application</label>
                <select name="application_id" class="input"><option value="">All apps</option>
                    @foreach ($applications as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold mb-1">Starting balance ($)</label><input name="balance" type="number" step="0.01" value="0" class="input"></div>
        </div>
        <div class="flex justify-end gap-2 pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            <a href="{{ route('sellers.index') }}" class="btn-ghost">Cancel</a>
            <button class="btn-primary"><i class="fas fa-save"></i>Create Seller</button>
        </div>
    </form>
</div>
@endsection
