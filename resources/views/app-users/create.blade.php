@extends('layouts.app')
@section('title', 'Add User — ' . $application->name)

@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('applications.users.store', $application) }}" class="glass-strong rounded-2xl p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2"><label class="block text-xs font-semibold mb-1">Username *</label><input name="username" required class="input"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold mb-1">Email</label><input name="email" type="email" class="input"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold mb-1">Password *</label><input name="password" type="password" required minlength="4" class="input"></div>
            <div><label class="block text-xs font-semibold mb-1">Level</label><input name="level" type="number" value="1" min="0" max="99" class="input"></div>
            <div><label class="block text-xs font-semibold mb-1">Expires At</label><input name="expires_at" type="datetime-local" class="input"></div>
            <div class="col-span-2"><label class="block text-xs font-semibold mb-1">Subscription</label>
                <select name="subscription_id" class="input"><option value="">— None —</option>
                    @foreach ($subs as $s)<option value="{{ $s->id }}">{{ $s->name }} (L{{ $s->level }} · {{ $s->default_days }}d)</option>@endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            <a href="{{ route('applications.users.index', $application) }}" class="btn-ghost">Cancel</a>
            <button class="btn-primary"><i class="fas fa-save"></i>Create User</button>
        </div>
    </form>
</div>
@endsection
