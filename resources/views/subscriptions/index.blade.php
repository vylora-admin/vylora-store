@extends('layouts.app')
@section('title', 'Subscriptions — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase text-ink-500">
                    <tr>
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Level</th>
                        <th class="text-left px-4 py-3">Days</th>
                        <th class="text-left px-4 py-3">Price</th>
                        <th class="text-left px-4 py-3">Users</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
                    @forelse ($subscriptions as $s)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $s->name }}</td>
                            <td class="px-4 py-3"><span class="badge badge-violet">L{{ $s->level }}</span></td>
                            <td class="px-4 py-3">{{ $s->default_days }}d</td>
                            <td class="px-4 py-3">${{ number_format($s->price, 2) }}</td>
                            <td class="px-4 py-3">{{ $s->user_subscriptions_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('applications.subscriptions.destroy', [$application, $s]) }}" class="inline" onsubmit="return confirm('Delete subscription?')">
                                    @csrf @method('DELETE')<button class="text-red-500 text-xs"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-12 text-ink-500">No subscriptions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">Add Subscription</h3>
        <form method="POST" action="{{ route('applications.subscriptions.store', $application) }}" class="space-y-3">@csrf
            <input name="name" placeholder="Tier name (e.g. Premium)" required class="input">
            <div class="grid grid-cols-2 gap-2">
                <input name="level" type="number" value="1" placeholder="Level" min="0" max="99" class="input">
                <input name="default_days" type="number" value="30" placeholder="Days" min="1" class="input">
            </div>
            <input name="price" type="number" step="0.01" value="0" placeholder="Price" class="input">
            <textarea name="description" placeholder="Description (optional)" rows="2" class="input"></textarea>
            <button class="btn-primary w-full justify-center"><i class="fas fa-plus"></i>Add Tier</button>
        </form>
    </div>
</div>
@endsection
