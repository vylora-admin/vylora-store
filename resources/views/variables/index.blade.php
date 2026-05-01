@extends('layouts.app')
@section('title', 'Variables — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <form method="GET" class="flex gap-2 mb-3">
            <select name="scope" class="input w-40" onchange="this.form.submit()">
                <option value="global" @selected(request('scope', 'global')==='global')>Global vars</option>
                <option value="user" @selected(request('scope')==='user')>User-bound</option>
                <option value="subscription" @selected(request('scope')==='subscription')>Subscription</option>
            </select>
            <input name="search" value="{{ request('search') }}" placeholder="Search keys..." class="input flex-1">
        </form>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase text-ink-500"><tr>
                    <th class="text-left px-4 py-3">Key</th>
                    <th class="text-left px-4 py-3">Value</th>
                    <th class="text-left px-4 py-3">Level</th>
                    <th class="text-right px-4 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
                    @forelse ($variables as $v)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $v->key }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-ink-500">@if ($v->is_secret)<span class="badge badge-yellow">secret</span>@else{{ Str::limit($v->value, 60) }}@endif</td>
                            <td class="px-4 py-3"><span class="badge badge-violet">L{{ $v->required_level }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('applications.variables.destroy', [$application, $v]) }}" class="inline" onsubmit="return confirm('Delete variable?')">@csrf @method('DELETE')<button class="text-red-500 text-xs"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-12 text-ink-500">No variables yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $variables->links() }}</div>
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">Add Variable</h3>
        <form method="POST" action="{{ route('applications.variables.store', $application) }}" class="space-y-3">@csrf
            <input name="key" placeholder="Key" required class="input">
            <textarea name="value" placeholder="Value" required rows="3" class="input"></textarea>
            <select name="scope" class="input">
                <option value="global">Global</option>
                <option value="subscription">Subscription</option>
            </select>
            <input type="number" name="required_level" value="0" min="0" max="99" placeholder="Min level" class="input">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_secret" value="1" class="accent-indigo-500">Mark as secret</label>
            <button class="btn-primary w-full justify-center"><i class="fas fa-plus"></i>Add Variable</button>
        </form>
        <div class="text-[11px] text-ink-500 mt-3 p-3 rounded-lg bg-ink-100/60 dark:bg-ink-800/40">
            <p><strong>Tip:</strong> Variables are exposed to clients via the <code>var</code> API endpoint, scoped by required level.</p>
        </div>
    </div>
</div>
@endsection
