@extends('layouts.app')
@section('title', 'App Users — ' . $application->name)
@section('subtitle', 'End-users authenticating into your application')

@section('content')
<div class="flex flex-wrap gap-3 items-center justify-between mb-5">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by username, email, IP, HWID..." class="input pl-9 w-80">
        </div>
        <select name="status" class="input w-32">
            <option value="">All</option>
            <option value="active" @selected(request('status')==='active')>Active</option>
            <option value="banned" @selected(request('status')==='banned')>Banned</option>
            <option value="expired" @selected(request('status')==='expired')>Expired</option>
        </select>
        <button class="btn-ghost"><i class="fas fa-filter"></i>Apply</button>
    </form>
    <a href="{{ route('applications.users.create', $application) }}" class="btn-primary"><i class="fas fa-user-plus"></i>Add User</a>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase tracking-wider text-ink-500">
            <tr>
                <th class="text-left px-4 py-3">User</th>
                <th class="text-left px-4 py-3">Last IP</th>
                <th class="text-left px-4 py-3">HWID</th>
                <th class="text-left px-4 py-3">Level</th>
                <th class="text-left px-4 py-3">Expires</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-right px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
            @forelse ($users as $u)
                <tr class="hover:bg-ink-100/40 dark:hover:bg-ink-800/30">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 text-white text-xs font-bold flex items-center justify-center">{{ Str::upper(Str::substr($u->username, 0, 2)) }}</div>
                            <div>
                                <p class="font-semibold leading-none">{{ $u->username }}</p>
                                <p class="text-[10px] text-ink-500 mt-1">{{ $u->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $u->last_ip ?? '—' }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $u->hwid ? Str::limit($u->hwid, 16) : '—' }}</td>
                    <td class="px-4 py-3"><span class="badge badge-violet">L{{ $u->level }}</span></td>
                    <td class="px-4 py-3 text-xs text-ink-500">{{ $u->expires_at?->diffForHumans() ?? 'never' }}</td>
                    <td class="px-4 py-3">
                        @if ($u->is_banned)
                            <span class="badge badge-red">banned</span>
                        @elseif ($u->expires_at && $u->expires_at->isPast())
                            <span class="badge badge-yellow">expired</span>
                        @else
                            <span class="badge badge-green">active</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('applications.users.show', [$application, $u]) }}" class="text-primary-500 hover:text-primary-600 text-xs font-semibold">Manage →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-12 text-ink-500">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
