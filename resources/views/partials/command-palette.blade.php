@php
    $cmd = collect([
        ['name' => 'Dashboard', 'icon' => 'fa-chart-line', 'url' => route('dashboard')],
        ['name' => 'Applications', 'icon' => 'fa-cubes', 'url' => route('applications.index')],
        ['name' => 'New Application', 'icon' => 'fa-plus', 'url' => route('applications.create')],
        ['name' => 'Licenses', 'icon' => 'fa-key', 'url' => route('licenses.index')],
        ['name' => 'New License', 'icon' => 'fa-plus', 'url' => route('licenses.create')],
        ['name' => 'Bulk License', 'icon' => 'fa-layer-group', 'url' => route('licenses.bulk-create')],
        ['name' => 'Products', 'icon' => 'fa-cube', 'url' => route('products.index')],
        ['name' => 'Profile', 'icon' => 'fa-user', 'url' => route('profile.edit')],
        ['name' => '2FA', 'icon' => 'fa-shield-halved', 'url' => route('profile.2fa')],
    ]);
    if (auth()->user()?->isAdmin()) {
        $cmd = $cmd->concat([
            ['name' => 'Sellers', 'icon' => 'fa-handshake', 'url' => route('sellers.index')],
            ['name' => 'Announcements', 'icon' => 'fa-bullhorn', 'url' => route('announcements.index')],
            ['name' => 'Addons', 'icon' => 'fa-puzzle-piece', 'url' => route('addons.index')],
            ['name' => 'Staff', 'icon' => 'fa-user-shield', 'url' => route('admin.users')],
            ['name' => 'Audit Log', 'icon' => 'fa-clipboard-list', 'url' => route('admin.audit-logs')],
            ['name' => 'Settings', 'icon' => 'fa-sliders', 'url' => route('settings.index')],
        ]);
    }
@endphp

<div x-cloak x-show="paletteOpen" @click.self="paletteOpen=false"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-start justify-center pt-24"
     x-transition.opacity>
    <div x-show="paletteOpen" x-transition x-data="{ q: '' }"
         class="w-full max-w-xl mx-4 glass-strong rounded-2xl shadow-2xl border border-ink-200/60 dark:border-ink-700/60 overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 border-b border-ink-200/60 dark:border-ink-700/60">
            <i class="fas fa-magnifying-glass text-ink-400"></i>
            <input x-model="q" x-init="$watch('paletteOpen', v => v && setTimeout(() => $el.focus(), 50))" type="text" placeholder="Type a command…" class="flex-1 bg-transparent outline-none text-sm">
            <kbd class="text-[10px] px-1.5 py-0.5 bg-ink-100 dark:bg-ink-800 rounded">ESC</kbd>
        </div>
        <ul class="max-h-80 overflow-y-auto py-2">
            @foreach ($cmd as $c)
                <li x-show="q==='' || '{{ Str::lower($c['name']) }}'.includes(q.toLowerCase())">
                    <a href="{{ $c['url'] }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-ink-100/70 dark:hover:bg-ink-800/60 text-sm">
                        <i class="fas {{ $c['icon'] }} text-primary-500 w-4"></i>
                        <span class="flex-1">{{ $c['name'] }}</span>
                        <i class="fas fa-arrow-right text-ink-300 text-xs"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
