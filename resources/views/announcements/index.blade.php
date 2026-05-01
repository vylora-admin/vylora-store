@extends('layouts.app')
@section('title', 'Announcements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-3">
        @forelse ($announcements as $a)
            <div class="glass rounded-2xl p-5">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold">{{ $a->title }}</h3>
                            <span class="badge badge-{{ ['info'=>'blue','success'=>'green','warning'=>'yellow','critical'=>'red'][$a->level] }}">{{ $a->level }}</span>
                        </div>
                        <p class="text-xs text-ink-500 mt-2">{{ $a->body }}</p>
                        <p class="text-[11px] text-ink-400 mt-2">{{ $a->application?->name ?? 'All applications' }} · {{ $a->published_at?->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('announcements.destroy', $a) }}">@csrf @method('DELETE')<button class="text-red-500"><i class="fas fa-trash text-xs"></i></button></form>
                </div>
            </div>
        @empty
            <div class="glass rounded-2xl p-12 text-center">
                <i class="fas fa-bullhorn text-4xl text-ink-300 mb-3"></i>
                <p class="text-sm text-ink-500">No announcements yet.</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $announcements->links() }}</div>
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">New Announcement</h3>
        <form method="POST" action="{{ route('announcements.store') }}" class="space-y-3">@csrf
            <select name="application_id" class="input">
                <option value="">All applications</option>
                @foreach ($applications as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
            </select>
            <input name="title" placeholder="Title" required class="input">
            <textarea name="body" placeholder="Body (markdown allowed)" rows="4" required class="input"></textarea>
            <select name="level" class="input">
                @foreach (['info','success','warning','critical'] as $l)<option value="{{ $l }}">{{ $l }}</option>@endforeach
            </select>
            <input name="expires_at" type="datetime-local" placeholder="Expires" class="input">
            <button class="btn-primary w-full justify-center"><i class="fas fa-bullhorn"></i>Publish</button>
        </form>
    </div>
</div>
@endsection
