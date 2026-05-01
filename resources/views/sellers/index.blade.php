@extends('layouts.app')
@section('title', 'Sellers / Resellers')

@section('content')
<div class="flex justify-between mb-5">
    <p class="text-sm text-ink-500">Sellers can issue keys & manage subscriptions for assigned applications using their own API key.</p>
    <a href="{{ route('sellers.create') }}" class="btn-primary"><i class="fas fa-user-plus"></i>New Seller</a>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase text-ink-500"><tr>
            <th class="text-left px-4 py-3">Name</th>
            <th class="text-left px-4 py-3">App</th>
            <th class="text-left px-4 py-3">Balance</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-right px-4 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
            @forelse ($sellers as $s)
                <tr>
                    <td class="px-4 py-3"><span class="font-semibold">{{ $s->display_name ?: $s->user->name }}</span><br><span class="text-xs text-ink-500">{{ $s->user->email }}</span></td>
                    <td class="px-4 py-3 text-xs">{{ $s->application?->name ?? 'All apps' }}</td>
                    <td class="px-4 py-3 font-bold">${{ number_format($s->balance, 2) }}</td>
                    <td class="px-4 py-3">@if ($s->is_active)<span class="badge badge-green">active</span>@else<span class="badge badge-red">inactive</span>@endif</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('sellers.show', $s) }}" class="text-primary-500 text-xs font-semibold">Manage →</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-12 text-ink-500">No sellers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sellers->links() }}</div>
@endsection
