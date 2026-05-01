@php
    $app = isset($application) ? $application : null;
    $isAdmin = auth()->user()?->isAdmin();
@endphp

<aside :class="sidebarOpen ? 'w-64' : 'w-20'"
       class="fixed inset-y-0 left-0 z-30 glass border-r border-ink-200/60 dark:border-ink-800/60 transition-all duration-200 flex flex-col hidden lg:flex">
    <div class="flex items-center h-16 px-4 border-b border-ink-200/60 dark:border-ink-800/60">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-soft" style="background: linear-gradient(135deg, var(--accent), #8b5cf6)">
                <i class="fas fa-shield-halved text-white text-sm"></i>
            </div>
            <span x-show="sidebarOpen" x-transition class="font-extrabold text-lg gradient-text leading-none">{{ \App\Models\Setting::get('site_name', 'KeyVault Pro') }}</span>
        </a>
    </div>

    <nav class="flex-1 py-3 px-2 space-y-0.5 overflow-y-auto">
        <p x-show="sidebarOpen" class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest text-ink-400">Overview</p>
        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>
        <a href="{{ route('applications.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('applications.*') && !request()->routeIs('applications.users.*','applications.subscriptions.*','applications.files.*','applications.variables.*','applications.webhooks.*','applications.blacklist.*','applications.chat.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
            <i class="fas fa-cubes w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition>Applications</span>
        </a>

        @if ($app)
            <div class="mt-3 mx-1 p-3 rounded-xl card-grad border border-ink-200/40 dark:border-ink-700/40" x-show="sidebarOpen" x-transition>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ink-400 mb-1">Active App</p>
                <p class="text-sm font-bold gradient-text truncate">{{ $app->name }}</p>
                <p class="text-[11px] text-ink-500 mt-0.5">v{{ $app->version }} · {{ $app->users()->count() }} users</p>
            </div>
            <p x-show="sidebarOpen" class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-ink-400">Manage App</p>

            @php
                $appNav = [
                    ['route' => 'applications.users.index', 'icon' => 'fa-users', 'label' => 'Users'],
                    ['route' => 'applications.subscriptions.index', 'icon' => 'fa-layer-group', 'label' => 'Subscriptions'],
                    ['route' => 'applications.files.index', 'icon' => 'fa-folder-open', 'label' => 'Files'],
                    ['route' => 'applications.variables.index', 'icon' => 'fa-code', 'label' => 'Variables'],
                    ['route' => 'applications.webhooks.index', 'icon' => 'fa-bolt', 'label' => 'Webhooks'],
                    ['route' => 'applications.blacklist.index', 'icon' => 'fa-ban', 'label' => 'Blacklist'],
                    ['route' => 'applications.chat.index', 'icon' => 'fa-comments', 'label' => 'Chat'],
                ];
            @endphp
            @foreach ($appNav as $item)
                <a href="{{ route($item['route'], $app) }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->routeIs($item['route']) ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                    <span x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        <p x-show="sidebarOpen" class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-ink-400">Catalog</p>
        <a href="{{ route('products.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('products.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
            <i class="fas fa-cube w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Products</span>
        </a>
        <a href="{{ route('licenses.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('licenses.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
            <i class="fas fa-key w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Licenses</span>
        </a>

        @if ($isAdmin)
            <p x-show="sidebarOpen" class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-ink-400">Administration</p>
            <a href="{{ route('sellers.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('sellers.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-handshake w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Sellers</span>
            </a>
            <a href="{{ route('announcements.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('announcements.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-bullhorn w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Announcements</span>
            </a>
            <a href="{{ route('addons.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('addons.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-puzzle-piece w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Addons</span>
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.users*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-user-shield w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Staff</span>
            </a>
            <a href="{{ route('admin.audit-logs') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.audit-logs') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-clipboard-list w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Audit Log</span>
            </a>
            <a href="{{ route('settings.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('settings.*') ? 'active' : 'text-ink-600 dark:text-ink-300' }}">
                <i class="fas fa-sliders w-5 text-center"></i><span x-show="sidebarOpen" x-transition>Settings</span>
            </a>
        @endif
    </nav>

    <div class="px-3 py-3 border-t border-ink-200/60 dark:border-ink-800/60 space-y-1">
        <button @click="paletteOpen = true" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm bg-ink-100/60 dark:bg-ink-800/60 hover:bg-ink-200/60 dark:hover:bg-ink-700/60 text-ink-600 dark:text-ink-300">
            <i class="fas fa-magnifying-glass w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition class="flex-1 text-left">Quick search</span>
            <kbd x-show="sidebarOpen" class="hidden sm:inline text-[10px] px-1.5 py-0.5 bg-ink-200 dark:bg-ink-700 rounded">Ctrl+K</kbd>
        </button>
        <button @click="darkMode = !darkMode" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm hover:bg-ink-100 dark:hover:bg-ink-800/60 text-ink-600 dark:text-ink-300">
            <i :class="darkMode ? 'fa-sun' : 'fa-moon'" class="fas w-5 text-center"></i>
            <span x-show="sidebarOpen" x-transition x-text="darkMode ? 'Light mode' : 'Dark mode'"></span>
        </button>
    </div>
</aside>
