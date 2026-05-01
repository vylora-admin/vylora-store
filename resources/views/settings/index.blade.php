@extends('layouts.app')
@section('title', 'Settings')
@section('subtitle', 'Configure your platform-wide preferences')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <aside class="glass rounded-2xl p-3 h-fit">
        @foreach (array_keys($fields) as $g)
            <a href="{{ route('settings.index', ['tab' => $g]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ $tab === $g ? 'bg-primary-500/10 text-primary-500 font-semibold' : 'hover:bg-ink-100/60 dark:hover:bg-ink-800/60 text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-{{ ['general' => 'sliders', 'theme' => 'palette', 'mail' => 'envelope', 'security' => 'shield-halved', 'integrations' => 'plug'][$g] ?? 'cog' }} w-5"></i>
                {{ Str::title($g) }}
            </a>
        @endforeach
    </aside>

    <div class="lg:col-span-3">
        <form method="POST" action="{{ route('settings.update') }}" class="glass-strong rounded-2xl p-6 space-y-4">@csrf @method('PATCH')
            <input type="hidden" name="tab" value="{{ $tab }}">
            @foreach ($fields[$tab] ?? [] as $key => $meta)
                <div>
                    <label class="block text-xs font-semibold mb-1">{{ $meta['label'] }}</label>
                    @if ($meta['type'] === 'bool')
                        <label class="flex items-center gap-2"><input type="checkbox" name="{{ $key }}" value="1" @checked($values[$key]) class="w-4 h-4 accent-indigo-500"><span class="text-xs text-ink-500">Enabled</span></label>
                    @elseif ($meta['type'] === 'int')
                        <input type="number" name="{{ $key }}" value="{{ $values[$key] }}" class="input">
                    @elseif (str_contains($key, 'pass'))
                        <input type="password" name="{{ $key }}" value="{{ $values[$key] }}" class="input">
                    @else
                        <input type="text" name="{{ $key }}" value="{{ $values[$key] }}" class="input">
                    @endif
                </div>
            @endforeach
            <div class="flex justify-end pt-3 border-t border-ink-200/60 dark:border-ink-700/60">
                <button class="btn-primary"><i class="fas fa-save"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
