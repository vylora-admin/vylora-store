@extends('layouts.app')
@section('title', $seller->display_name ?? $seller->user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-1 space-y-4">
        <div class="glass-strong rounded-2xl p-6 text-center">
            <div class="w-20 h-20 rounded-2xl mx-auto bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-2xl font-extrabold flex items-center justify-center">{{ Str::upper(Str::substr($seller->user->name, 0, 2)) }}</div>
            <h2 class="text-xl font-bold mt-3">{{ $seller->display_name ?? $seller->user->name }}</h2>
            <p class="text-xs text-ink-500">{{ $seller->user->email }}</p>
            <p class="text-3xl font-extrabold gradient-text mt-3">${{ number_format($seller->balance, 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">API Key</h3>
            <code class="block p-3 rounded-lg bg-ink-900 text-ink-100 text-xs font-mono break-all">{{ $seller->api_key }}</code>
        </div>
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">Adjust Balance</h3>
            <form method="POST" action="{{ route('sellers.balance', $seller) }}" class="space-y-2">@csrf
                <input name="amount" type="number" step="0.01" placeholder="+10 or -5" required class="input">
                <input name="notes" placeholder="Notes" class="input">
                <button class="btn-primary w-full justify-center"><i class="fas fa-coins"></i>Apply</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-3">Transactions</h3>
            <div class="space-y-2">
                @forelse ($transactions as $t)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-ink-100/60 dark:bg-ink-800/40">
                        <div>
                            <span class="badge badge-{{ $t->type === 'credit' || $t->amount >= 0 ? 'green' : 'red' }}">{{ $t->type }}</span>
                            <span class="text-xs text-ink-500 ml-2">{{ $t->notes ?? '—' }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold {{ $t->amount >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $t->amount >= 0 ? '+' : '' }}${{ number_format($t->amount, 2) }}</p>
                            <p class="text-[10px] text-ink-400">balance ${{ number_format($t->balance_after, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-500 py-4 text-center">No transactions yet.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</div>
@endsection
