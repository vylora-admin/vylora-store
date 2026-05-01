@extends('layouts.app')
@section('title', 'Chat — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-3">
        @forelse ($channels as $c)
            <a href="{{ route('applications.chat.show', [$application, $c]) }}" class="glass rounded-2xl p-5 flex items-center justify-between hover:scale-[1.01] transition-transform">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold">#{{ $c->name }}</h3>
                        <span class="badge badge-violet">L{{ $c->required_level }}</span>
                    </div>
                    <p class="text-xs text-ink-500 mt-1">{{ $c->description ?: 'No description' }}</p>
                    <p class="text-[11px] text-ink-400 mt-1">{{ $c->messages_count }} messages</p>
                </div>
                <i class="fas fa-arrow-right text-ink-400"></i>
            </a>
        @empty
            <div class="glass rounded-2xl p-12 text-center">
                <i class="fas fa-comments text-4xl text-ink-300 mb-3"></i>
                <p class="text-sm text-ink-500">No chat channels yet.</p>
            </div>
        @endforelse
    </div>
    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">New Channel</h3>
        <form method="POST" action="{{ route('applications.chat.store', $application) }}" class="space-y-3">@csrf
            <input name="name" placeholder="Channel name (e.g. general)" required class="input">
            <textarea name="description" placeholder="Description" rows="2" class="input"></textarea>
            <input name="required_level" type="number" value="0" min="0" max="99" placeholder="Required level" class="input">
            <button class="btn-primary w-full justify-center"><i class="fas fa-plus"></i>Create Channel</button>
        </form>
    </div>
</div>
@endsection
