@extends('layouts.app')
@section('title', 'Addons')
@section('subtitle', 'Drop-in modules that extend KeyVault Pro')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-ink-500">Place addon directories in <code class="text-primary-500">/addons/&lt;slug&gt;/</code> with an <code class="text-primary-500">addon.json</code> manifest.</p>
    <form method="POST" action="{{ route('addons.rescan') }}">@csrf<button class="btn-ghost"><i class="fas fa-sync"></i>Rescan</button></form>
</div>

@if ($addons->isEmpty())
    <div class="glass rounded-2xl p-12 text-center">
        <i class="fas fa-puzzle-piece text-4xl text-ink-300 mb-3"></i>
        <h3 class="text-lg font-bold mb-1">No addons installed</h3>
        <p class="text-sm text-ink-500">Install addons by placing them in <code>addons/</code> and clicking Rescan.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($addons as $addon)
            <div class="glass rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white text-base font-bold flex items-center justify-center"><i class="fas fa-{{ $addon->icon ?: 'puzzle-piece' }}"></i></div>
                        <div>
                            <h3 class="font-bold leading-tight">{{ $addon->name }}</h3>
                            <p class="text-[11px] text-ink-500 mt-0.5">v{{ $addon->version }} by {{ $addon->author ?? 'unknown' }}</p>
                        </div>
                    </div>
                    @if ($addon->is_enabled)<span class="badge badge-green">Enabled</span>@else<span class="badge badge-red">Disabled</span>@endif
                </div>
                <p class="text-xs text-ink-500 line-clamp-3 min-h-[42px]">{{ $addon->description }}</p>
                <div class="flex justify-end gap-2 mt-3">
                    <form method="POST" action="{{ route('addons.toggle', $addon) }}">@csrf
                        <button class="btn-ghost text-xs"><i class="fas fa-power-off"></i>{{ $addon->is_enabled ? 'Disable' : 'Enable' }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
