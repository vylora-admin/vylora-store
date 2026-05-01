@extends('layouts.app')
@section('title', $user->username)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-1 space-y-4">
        <div class="glass-strong rounded-2xl p-6 text-center">
            <div class="w-20 h-20 rounded-2xl mx-auto bg-gradient-to-br from-primary-500 to-accent-500 text-white text-2xl font-extrabold flex items-center justify-center shadow-soft">
                {{ Str::upper(Str::substr($user->username, 0, 2)) }}
            </div>
            <h2 class="text-xl font-bold mt-4">{{ $user->username }}</h2>
            <p class="text-xs text-ink-500">{{ $user->email ?? 'no email' }}</p>
            <div class="flex justify-center gap-2 mt-3 flex-wrap">
                @if ($user->is_banned)<span class="badge badge-red">banned</span>
                @elseif ($user->isExpired())<span class="badge badge-yellow">expired</span>
                @else<span class="badge badge-green">active</span>@endif
                <span class="badge badge-violet">L{{ $user->level }}</span>
            </div>
        </div>

        <div class="glass rounded-2xl p-5 space-y-3">
            <h3 class="text-sm font-bold mb-2">Actions</h3>
            @if ($user->is_banned)
                <form method="POST" action="{{ route('applications.users.unban', [$application, $user]) }}">@csrf @method('PATCH')
                    <button class="btn-ghost w-full justify-center"><i class="fas fa-unlock"></i>Unban</button>
                </form>
            @else
                <form method="POST" action="{{ route('applications.users.ban', [$application, $user]) }}" class="space-y-2">@csrf @method('PATCH')
                    <input name="reason" placeholder="Reason..." class="input">
                    <button class="btn-danger w-full justify-center"><i class="fas fa-ban"></i>Ban User</button>
                </form>
            @endif
            <form method="POST" action="{{ route('applications.users.reset-hwid', [$application, $user]) }}">@csrf @method('PATCH')
                <button class="btn-ghost w-full justify-center"><i class="fas fa-microchip"></i>Reset HWID</button>
            </form>
            <form method="POST" action="{{ route('applications.users.extend', [$application, $user]) }}" class="flex gap-2">@csrf @method('PATCH')
                <input name="days" type="number" value="30" class="input w-24">
                <button class="btn-ghost flex-1 justify-center"><i class="fas fa-calendar-plus"></i>Extend</button>
            </form>
            <form method="POST" action="{{ route('applications.users.destroy', [$application, $user]) }}" onsubmit="return confirm('Delete this user permanently?')">@csrf @method('DELETE')
                <button class="btn-danger w-full justify-center"><i class="fas fa-trash"></i>Delete User</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-4">
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">Profile</h3>
            <dl class="grid grid-cols-2 gap-y-2 text-sm">
                @foreach ([
                    'Username' => $user->username,
                    'Email' => $user->email ?: '—',
                    'Level' => 'L' . $user->level,
                    'Last IP' => $user->last_ip ?: '—',
                    'HWID' => $user->hwid ? Str::limit($user->hwid, 32) : '—',
                    'Last login' => $user->last_login_at?->diffForHumans() ?? 'never',
                    'Expires' => $user->expires_at?->format('Y-m-d H:i') ?? 'never',
                    'Created' => $user->created_at->format('Y-m-d H:i'),
                ] as $l => $v)
                    <dt class="text-ink-500">{{ $l }}</dt>
                    <dd class="font-mono text-xs">{{ $v }}</dd>
                @endforeach
            </dl>
        </div>

        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">Subscriptions</h3>
            @if ($user->userSubscriptions->isEmpty())
                <p class="text-xs text-ink-500">No subscriptions assigned.</p>
            @else
                <div class="space-y-2">
                    @foreach ($user->userSubscriptions as $us)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-ink-100/60 dark:bg-ink-800/40">
                            <div>
                                <p class="text-sm font-semibold">{{ $us->subscription?->name }}</p>
                                <p class="text-xs text-ink-500">Expires {{ $us->expires_at?->diffForHumans() ?? 'never' }}</p>
                            </div>
                            <span class="badge {{ $us->is_active ? 'badge-green' : 'badge-red' }}">{{ $us->is_active ? 'active' : 'inactive' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">Recent Sessions</h3>
            <div class="space-y-2">
                @forelse ($user->sessions->take(10) as $s)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-ink-100/60 dark:bg-ink-800/40 text-xs">
                        <span class="font-mono">{{ $s->ip ?? '—' }}</span>
                        <span class="text-ink-500">{{ Str::limit($s->user_agent ?? 'unknown', 40) }}</span>
                        <span class="text-ink-400">{{ $s->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-xs text-ink-500">No sessions.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
