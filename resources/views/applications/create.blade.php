@extends('layouts.app')
@section('title', 'New Application')
@section('subtitle', 'Set up authentication for a new product')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('applications.store') }}" class="glass-strong rounded-2xl p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1">Name <span class="text-red-500">*</span></label>
                <input name="name" value="{{ old('name') }}" required class="input" placeholder="e.g. MyAwesomeApp">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Version</label>
                <input name="version" value="{{ old('version', '1.0') }}" class="input">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Default subscription days</label>
                <input type="number" name="default_subscription_days" value="{{ old('default_subscription_days', 30) }}" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1">Description</label>
                <textarea name="description" rows="3" class="input">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Download URL</label>
                <input name="download_url" value="{{ old('download_url') }}" class="input" placeholder="https://...">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Icon URL</label>
                <input name="icon_url" value="{{ old('icon_url') }}" class="input" placeholder="https://...">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1">Discord webhook URL</label>
                <input name="discord_webhook_url" value="{{ old('discord_webhook_url') }}" class="input" placeholder="https://discord.com/api/webhooks/...">
                <p class="text-[11px] text-ink-500 mt-1">Receive Discord embeds for events like logins, registrations, and bans.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            @foreach ([
                ['allow_register', 'Allow new registrations', true],
                ['allow_login', 'Allow login', true],
                ['allow_extend', 'Allow subscription extension', true],
                ['hwid_check_enabled', 'Enforce HWID lock', true],
                ['integrity_check_enabled', 'Enforce integrity hash check', false],
            ] as [$k, $l, $d])
                <label class="flex items-center gap-3 p-3 rounded-xl bg-ink-100/60 dark:bg-ink-800/40 cursor-pointer hover:bg-ink-100 dark:hover:bg-ink-800/70">
                    <input type="checkbox" name="{{ $k }}" value="1" @checked(old($k, $d)) class="w-4 h-4 accent-indigo-500">
                    <span class="text-sm">{{ $l }}</span>
                </label>
            @endforeach
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            <a href="{{ route('applications.index') }}" class="btn-ghost"><i class="fas fa-arrow-left"></i>Back</a>
            <button class="btn-primary"><i class="fas fa-rocket"></i>Create Application</button>
        </div>
    </form>
</div>
@endsection
