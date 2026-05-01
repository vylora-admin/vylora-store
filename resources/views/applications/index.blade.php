@extends('layouts.app')
@section('title', 'Applications')
@section('subtitle', 'Manage all the apps protected by KeyVault Pro')

@section('content')
<div class="flex flex-wrap gap-3 items-center justify-between mb-5">
    <form method="GET" class="flex items-center gap-2">
        <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search applications..." class="input pl-9 w-72">
        </div>
        <button class="btn-ghost"><i class="fas fa-filter"></i>Filter</button>
    </form>
    <a href="{{ route('applications.create') }}" class="btn-primary"><i class="fas fa-plus"></i>New Application</a>
</div>

@if ($applications->isEmpty())
    <div class="glass rounded-2xl p-12 text-center">
        <i class="fas fa-cubes text-4xl text-ink-300 mb-3"></i>
        <h3 class="text-lg font-bold mb-1">No applications yet</h3>
        <p class="text-sm text-ink-500 mb-4">Create your first application to start authenticating users.</p>
        <a href="{{ route('applications.create') }}" class="btn-primary mx-auto inline-flex"><i class="fas fa-plus"></i>Create Application</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($applications as $app)
            <a href="{{ route('applications.show', $app) }}" class="stat-card glass rounded-2xl p-5 block">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 text-white text-base font-bold flex items-center justify-center shadow-lg">{{ Str::upper(Str::substr($app->name, 0, 2)) }}</div>
                        <div>
                            <h3 class="font-bold leading-tight">{{ $app->name }}</h3>
                            <p class="text-[11px] text-ink-500 mt-0.5">v{{ $app->version }}</p>
                        </div>
                    </div>
                    @if ($app->is_paused)
                        <span class="badge badge-yellow">Paused</span>
                    @else
                        <span class="badge badge-green flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full pulse-dot"></span>Live</span>
                    @endif
                </div>
                <p class="text-xs text-ink-500 line-clamp-2 mb-4 min-h-[32px]">{{ $app->description ?: 'No description provided.' }}</p>
                <div class="grid grid-cols-3 gap-2 text-center text-[11px]">
                    <div class="rounded-lg bg-ink-100/60 dark:bg-ink-800/50 py-2"><p class="font-bold text-base">{{ $app->users_count }}</p><p class="text-ink-500">Users</p></div>
                    <div class="rounded-lg bg-ink-100/60 dark:bg-ink-800/50 py-2"><p class="font-bold text-base">{{ $app->sessions_count }}</p><p class="text-ink-500">Sessions</p></div>
                    <div class="rounded-lg bg-ink-100/60 dark:bg-ink-800/50 py-2"><p class="font-bold text-base">{{ $app->created_at->diffForHumans(null, true) }}</p><p class="text-ink-500">Old</p></div>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $applications->links() }}</div>
@endif
@endsection
