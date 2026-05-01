@extends('layouts.app')
@section('title', 'Blacklist — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <form method="GET" class="flex gap-2 mb-3">
            <select name="type" class="input w-40">
                <option value="">All types</option>
                @foreach (['hwid','ip','username','country','email'] as $t)<option value="{{ $t }}" @selected(request('type')===$t)>{{ $t }}</option>@endforeach
            </select>
            <input name="search" value="{{ request('search') }}" placeholder="Search..." class="input flex-1">
            <button class="btn-ghost"><i class="fas fa-search"></i></button>
        </form>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase text-ink-500"><tr>
                    <th class="text-left px-4 py-3">Type</th><th class="text-left px-4 py-3">Value</th>
                    <th class="text-left px-4 py-3">Reason</th><th class="text-left px-4 py-3">Expires</th><th></th>
                </tr></thead>
                <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
                    @forelse ($items as $b)
                        <tr>
                            <td class="px-4 py-3"><span class="badge badge-red">{{ $b->type }}</span></td>
                            <td class="px-4 py-3 font-mono text-xs">{{ Str::limit($b->value, 30) }}</td>
                            <td class="px-4 py-3 text-xs text-ink-500">{{ $b->reason ?: '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $b->expires_at?->diffForHumans() ?? 'permanent' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('applications.blacklist.destroy', [$application, $b]) }}">@csrf @method('DELETE')<button class="text-red-500 text-xs"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-12 text-ink-500">No blacklist entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">Add to Blacklist</h3>
        <form method="POST" action="{{ route('applications.blacklist.store', $application) }}" class="space-y-3">@csrf
            <select name="type" required class="input">
                @foreach (['hwid' => 'HWID', 'ip' => 'IP Address', 'username' => 'Username', 'country' => 'Country code', 'email' => 'Email'] as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
            </select>
            <input name="value" placeholder="Value" required class="input">
            <input name="reason" placeholder="Reason" class="input">
            <input name="expires_at" type="datetime-local" placeholder="Expires" class="input">
            <button class="btn-primary w-full justify-center"><i class="fas fa-ban"></i>Block</button>
        </form>
    </div>
</div>
@endsection
