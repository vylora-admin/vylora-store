@extends('layouts.app')
@section('title', '#' . $channel->name)
@section('subtitle', $application->name . ' · L' . $channel->required_level)

@section('content')
<div class="glass rounded-2xl flex flex-col h-[70vh]">
    <div class="px-5 py-3 border-b border-ink-200/60 dark:border-ink-800/60 flex items-center justify-between">
        <div>
            <h2 class="font-bold">#{{ $channel->name }}</h2>
            <p class="text-xs text-ink-500">{{ $channel->description ?: 'No description' }}</p>
        </div>
        <form method="POST" action="{{ route('applications.chat.destroy', [$application, $channel]) }}" onsubmit="return confirm('Delete this channel?')">@csrf @method('DELETE')
            <button class="btn-danger text-xs"><i class="fas fa-trash"></i>Delete channel</button>
        </form>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-2">
        @forelse ($messages as $m)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">{{ Str::upper(Str::substr($m->username_snapshot, 0, 1)) }}</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="font-bold">{{ $m->username_snapshot }}</span>
                        <span class="text-ink-400">{{ $m->created_at->format('M j H:i') }}</span>
                        <form method="POST" action="{{ route('applications.chat.delete-message', [$application, $channel, $m]) }}" class="ml-auto opacity-0 hover:opacity-100">@csrf @method('DELETE')<button class="text-red-500 text-[10px]"><i class="fas fa-trash"></i></button></form>
                    </div>
                    <p class="text-sm">{{ $m->message }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-center text-ink-500 py-8">No messages yet. Clients can post via <code class="text-primary-500">type=chatSend</code>.</p>
        @endforelse
    </div>
</div>
@endsection
