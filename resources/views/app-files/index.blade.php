@extends('layouts.app')
@section('title', 'Files — ' . $application->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-ink-100/60 dark:bg-ink-800/60 text-xs uppercase text-ink-500"><tr>
                    <th class="text-left px-4 py-3">Name</th><th class="text-left px-4 py-3">Size</th>
                    <th class="text-left px-4 py-3">Level</th><th class="text-left px-4 py-3">Downloads</th>
                    <th class="text-left px-4 py-3">Status</th><th class="text-right px-4 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-ink-200/40 dark:divide-ink-800/40">
                    @forelse ($files as $f)
                        <tr>
                            <td class="px-4 py-3"><span class="font-semibold">{{ $f->name }}</span><br><span class="text-xs text-ink-500">{{ $f->original_filename }}</span></td>
                            <td class="px-4 py-3">{{ number_format($f->size / 1024, 1) }} KB</td>
                            <td class="px-4 py-3"><span class="badge badge-violet">L{{ $f->required_level }}</span></td>
                            <td class="px-4 py-3">{{ number_format($f->download_count) }}</td>
                            <td class="px-4 py-3">@if ($f->is_active)<span class="badge badge-green">active</span>@else<span class="badge badge-red">disabled</span>@endif</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('applications.files.toggle', [$application, $f]) }}" class="inline">@csrf @method('PATCH')<button class="text-amber-500 text-xs mr-2"><i class="fas fa-power-off"></i></button></form>
                                <form method="POST" action="{{ route('applications.files.destroy', [$application, $f]) }}" class="inline" onsubmit="return confirm('Delete file?')">@csrf @method('DELETE')<button class="text-red-500 text-xs"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-12 text-ink-500">No files yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $files->links() }}</div>
    </div>

    <div class="glass-strong rounded-2xl p-5 h-fit">
        <h3 class="text-sm font-bold mb-3">Upload File</h3>
        <form method="POST" action="{{ route('applications.files.store', $application) }}" enctype="multipart/form-data" class="space-y-3">@csrf
            <input name="name" placeholder="Display name" required class="input">
            <input type="file" name="file" required class="input">
            <input type="number" name="required_level" value="0" min="0" max="99" placeholder="Required level" class="input">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_encrypted" value="1" class="accent-indigo-500">Mark as encrypted</label>
            <button class="btn-primary w-full justify-center"><i class="fas fa-cloud-arrow-up"></i>Upload</button>
        </form>
    </div>
</div>
@endsection
