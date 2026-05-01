@extends('layouts.app')
@section('title', 'Edit ' . $application->name)
@section('subtitle', 'Configure application behavior')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('applications.update', $application) }}" class="glass-strong rounded-2xl p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1">Name</label>
                <input name="name" value="{{ old('name', $application->name) }}" required class="input">
            </div>
            <div><label class="block text-xs font-semibold mb-1">Version</label>
                <input name="version" value="{{ old('version', $application->version) }}" class="input"></div>
            <div><label class="block text-xs font-semibold mb-1">Default subscription days</label>
                <input type="number" name="default_subscription_days" value="{{ old('default_subscription_days', $application->default_subscription_days) }}" class="input"></div>
            <div class="md:col-span-2"><label class="block text-xs font-semibold mb-1">Description</label>
                <textarea name="description" rows="3" class="input">{{ old('description', $application->description) }}</textarea></div>
            <div><label class="block text-xs font-semibold mb-1">Download URL</label>
                <input name="download_url" value="{{ old('download_url', $application->download_url) }}" class="input"></div>
            <div><label class="block text-xs font-semibold mb-1">Icon URL</label>
                <input name="icon_url" value="{{ old('icon_url', $application->icon_url) }}" class="input"></div>
            <div class="md:col-span-2"><label class="block text-xs font-semibold mb-1">Discord webhook</label>
                <input name="discord_webhook_url" value="{{ old('discord_webhook_url', $application->discord_webhook_url) }}" class="input"></div>
            <div class="md:col-span-2"><label class="block text-xs font-semibold mb-1">Pause reason</label>
                <input name="pause_reason" value="{{ old('pause_reason', $application->pause_reason) }}" class="input"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            @foreach ([
                ['is_paused', 'Paused (clients will be blocked)'],
                ['allow_register', 'Allow registrations'],
                ['allow_login', 'Allow login'],
                ['allow_extend', 'Allow extension'],
                ['hwid_check_enabled', 'Enforce HWID lock'],
                ['integrity_check_enabled', 'Enforce integrity check'],
                ['disable_user_panel', 'Disable user panel'],
            ] as [$k, $l])
                <label class="flex items-center gap-3 p-3 rounded-xl bg-ink-100/60 dark:bg-ink-800/40 cursor-pointer hover:bg-ink-100 dark:hover:bg-ink-800/70">
                    <input type="checkbox" name="{{ $k }}" value="1" @checked(old($k, $application->{$k})) class="w-4 h-4 accent-indigo-500">
                    <span class="text-sm">{{ $l }}</span>
                </label>
            @endforeach
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
            <button form="delete-form" class="btn-danger" type="submit"><i class="fas fa-trash"></i>Delete</button>
            <div class="flex gap-2">
                <a href="{{ route('applications.show', $application) }}" class="btn-ghost"><i class="fas fa-arrow-left"></i>Cancel</a>
                <button class="btn-primary"><i class="fas fa-save"></i>Save Changes</button>
            </div>
        </div>
    </form>
    <form id="delete-form" method="POST" action="{{ route('applications.destroy', $application) }}" onsubmit="return confirm('Delete this application and ALL its data?')">@csrf @method('DELETE')</form>
</div>
@endsection
