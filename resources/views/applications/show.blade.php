@extends('layouts.app')
@section('title', $application->name)
@section('subtitle', 'Application overview')

@section('content')
<div class="rounded-3xl glass-strong p-6 mb-6 relative overflow-hidden">
    <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full opacity-20" style="background: radial-gradient(closest-side, var(--accent), transparent 70%)"></div>
    <div class="relative z-10 flex flex-wrap gap-4 items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 text-white text-2xl font-extrabold flex items-center justify-center shadow-soft">
                {{ Str::upper(Str::substr($application->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-2xl font-extrabold">{{ $application->name }}</h2>
                    @if ($application->is_paused)
                        <span class="badge badge-yellow">Paused</span>
                    @else
                        <span class="badge badge-green flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full pulse-dot"></span>Live</span>
                    @endif
                </div>
                <p class="text-sm text-ink-500">v{{ $application->version }} · created {{ $application->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('applications.users.index', $application) }}" class="btn-ghost"><i class="fas fa-users"></i>Users</a>
            <a href="{{ route('applications.edit', $application) }}" class="btn-ghost"><i class="fas fa-pen"></i>Edit</a>
            <form method="POST" action="{{ route('applications.pause', $application) }}">@csrf @method('PATCH')
                <button class="btn-ghost"><i class="fas fa-{{ $application->is_paused ? 'play' : 'pause' }}"></i>{{ $application->is_paused ? 'Resume' : 'Pause' }}</button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
    @foreach ([
        ['Users', 'fa-users', $stats['users'], 'text-blue-500'],
        ['Banned', 'fa-ban', $stats['banned'], 'text-red-500'],
        ['Online', 'fa-bolt', $stats['online'], 'text-emerald-500'],
        ['Logs today', 'fa-clipboard', $stats['logs_today'], 'text-violet-500'],
        ['Subs', 'fa-layer-group', $stats['subscriptions'], 'text-amber-500'],
        ['Files', 'fa-folder', $stats['files'], 'text-fuchsia-500'],
    ] as [$l, $i, $v, $c])
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-2xl font-bold {{ $c }}">{{ number_format($v) }}</p>
            <p class="text-xs text-ink-500 mt-1"><i class="fas {{ $i }} mr-1"></i>{{ $l }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="glass rounded-2xl p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold"><i class="fas fa-key text-primary-500 mr-2"></i>API Credentials</h3>
            <form method="POST" action="{{ route('applications.reset-secret', $application) }}" onsubmit="return confirm('Rotate the secret? Existing clients will break.')">
                @csrf @method('PATCH')
                <button class="text-xs text-amber-500 font-semibold hover:underline">Rotate secret</button>
            </form>
        </div>
        <div class="space-y-2" x-data>
            @foreach ([
                ['Name', $application->name],
                ['Owner ID', $application->owner_uid],
                ['Secret', $application->secret],
                ['Version', $application->version],
                ['API Endpoint', url('/api/1.3/')],
            ] as [$l, $v])
                <div class="flex items-center gap-2 p-2 rounded-lg bg-ink-100/60 dark:bg-ink-800/40">
                    <span class="text-xs font-semibold text-ink-400 w-28 flex-shrink-0">{{ $l }}</span>
                    <code class="flex-1 text-xs font-mono truncate">{{ $v }}</code>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $v }}'); $event.target.classList.add('text-emerald-500')" class="text-ink-400 hover:text-primary-500"><i class="fas fa-copy text-xs"></i></button>
                </div>
            @endforeach
        </div>

        <div class="mt-5 p-4 rounded-xl bg-ink-900 text-ink-100 text-xs font-mono overflow-x-auto">
<pre class="whitespace-pre">// .NET / C# example
KeyAuthApp.app_init(
    name:    "{{ $application->name }}",
    ownerid: "{{ $application->owner_uid }}",
    secret:  "{{ Str::limit($application->secret, 16, '...') }}",
    version: "{{ $application->version }}"
);</pre>
        </div>
    </div>

    <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-bold mb-3"><i class="fas fa-clock-rotate-left text-accent-500 mr-2"></i>Recent Sessions</h3>
        <div class="space-y-2 max-h-[420px] overflow-y-auto">
            @forelse ($recentSessions as $s)
                <div class="p-3 rounded-lg bg-ink-100/60 dark:bg-ink-800/40">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold">{{ $s->user?->username ?? 'guest' }}</p>
                        @if ($s->is_validated)
                            <span class="badge badge-green">validated</span>
                        @else
                            <span class="badge badge-yellow">init</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-ink-500 mt-1 font-mono truncate">{{ $s->ip ?? '—' }} · {{ Str::limit($s->user_agent ?? 'unknown', 30) }}</p>
                    <p class="text-[10px] text-ink-400 mt-1">{{ $s->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-sm text-ink-500 py-4 text-center">No sessions yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-bold mb-3"><i class="fas fa-users text-blue-500 mr-2"></i>Recent users</h3>
        <div class="space-y-1.5">
            @forelse ($recentUsers as $u)
                <a href="{{ route('applications.users.show', [$application, $u]) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-ink-100/60 dark:hover:bg-ink-800/40">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-ink-200 dark:bg-ink-700 flex items-center justify-center text-xs font-bold">{{ Str::upper(Str::substr($u->username,0,1)) }}</div>
                        <div>
                            <p class="text-sm font-semibold leading-none">{{ $u->username }}</p>
                            <p class="text-[10px] text-ink-500 mt-1">{{ $u->last_ip ?? '—' }}</p>
                        </div>
                    </div>
                    @if ($u->is_banned)
                        <span class="badge badge-red">banned</span>
                    @elseif ($u->expires_at && $u->expires_at->isPast())
                        <span class="badge badge-yellow">expired</span>
                    @else
                        <span class="badge badge-green">active</span>
                    @endif
                </a>
            @empty
                <p class="text-sm text-ink-500 py-4 text-center">No users yet. They'll appear after their first registration.</p>
            @endforelse
        </div>
    </div>

    <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-bold mb-3"><i class="fas fa-clipboard-list text-amber-500 mr-2"></i>Recent logs</h3>
        <div class="space-y-1 max-h-[400px] overflow-y-auto">
            @forelse ($recentLogs as $log)
                <div class="flex items-start gap-2 p-2 rounded-lg hover:bg-ink-100/60 dark:hover:bg-ink-800/40 text-xs">
                    <span class="badge badge-{{ ['debug' => 'blue', 'info' => 'blue', 'warning' => 'yellow', 'error' => 'red', 'critical' => 'red'][$log->level] ?? 'blue' }} flex-shrink-0">{{ $log->event_type }}</span>
                    <span class="flex-1 text-ink-500 truncate">{{ Str::limit($log->message, 80) }}</span>
                    <span class="text-[10px] text-ink-400 flex-shrink-0">{{ $log->created_at->diffForHumans(null, true) }}</span>
                </div>
            @empty
                <p class="text-sm text-ink-500 py-4 text-center">No logs yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
