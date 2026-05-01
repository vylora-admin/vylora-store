@extends('layouts.app')
@section('title', 'Webhooks — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-3">
        @forelse ($webhooks as $w)
            <div class="glass rounded-2xl p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold">{{ $w->name }}</h3>
                            @if ($w->is_active)<span class="badge badge-green">active</span>@else<span class="badge badge-red">disabled</span>@endif
                        </div>
                        <code class="text-xs font-mono text-ink-500 break-all">{{ $w->url }}</code>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach ($w->events as $ev)
                                <span class="badge badge-blue">{{ $ev }}</span>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-ink-400 mt-2">{{ $w->deliveries_count }} deliveries · timeout {{ $w->timeout_seconds }}s</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <form method="POST" action="{{ route('applications.webhooks.test', [$application, $w]) }}">@csrf
                            <button class="btn-ghost text-xs"><i class="fas fa-paper-plane"></i>Test</button>
                        </form>
                        <form method="POST" action="{{ route('applications.webhooks.destroy', [$application, $w]) }}" onsubmit="return confirm('Delete webhook?')">@csrf @method('DELETE')
                            <button class="btn-danger text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass rounded-2xl p-12 text-center">
                <i class="fas fa-bolt text-4xl text-ink-300 mb-3"></i>
                <p class="text-sm text-ink-500">No webhooks configured.</p>
            </div>
        @endforelse
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">New Webhook</h3>
        <form method="POST" action="{{ route('applications.webhooks.store', $application) }}" class="space-y-3">@csrf
            <input name="name" placeholder="Name" required class="input">
            <input name="url" type="url" placeholder="https://..." required class="input">
            <input name="secret" placeholder="Secret (HMAC, optional)" class="input">
            <p class="text-[11px] font-semibold mt-2">Events</p>
            <div class="grid grid-cols-2 gap-1.5 max-h-40 overflow-y-auto">
                @foreach (\App\Http\Controllers\WebhookController::EVENTS as $ev)
                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="events[]" value="{{ $ev }}" class="accent-indigo-500">{{ $ev }}</label>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input name="retry_count" type="number" value="3" min="0" max="10" class="input" placeholder="Retries">
                <input name="timeout_seconds" type="number" value="10" min="1" max="60" class="input" placeholder="Timeout (s)">
            </div>
            <button class="btn-primary w-full justify-center"><i class="fas fa-plus"></i>Add Webhook</button>
        </form>
    </div>
</div>
@endsection
