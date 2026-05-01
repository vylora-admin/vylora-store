<header class="sticky top-0 z-20 glass-strong border-b border-ink-200/60 dark:border-ink-800/60">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:inline-flex items-center justify-center w-10 h-10 rounded-xl hover:bg-ink-100 dark:hover:bg-ink-800/60 text-ink-500">
                <i class="fas fa-bars-staggered"></i>
            </button>
            <div class="hidden sm:flex flex-col">
                <h1 class="text-base font-bold leading-tight">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-ink-500">@yield('subtitle', \App\Models\Setting::get('site_name', config('app.name', 'KeyVault Pro')))</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button @click="paletteOpen = true" class="hidden md:flex items-center gap-2 pl-3 pr-2 py-2 rounded-xl bg-ink-100/70 dark:bg-ink-800/60 hover:bg-ink-200/70 dark:hover:bg-ink-700/60 text-ink-500 text-sm border border-ink-200/60 dark:border-ink-700/60 min-w-[260px]">
                <i class="fas fa-magnifying-glass text-xs"></i>
                <span class="flex-1 text-left text-xs">Search apps, users, licenses…</span>
                <kbd class="text-[10px] px-1.5 py-0.5 bg-white dark:bg-ink-900 rounded border border-ink-200 dark:border-ink-700">Ctrl K</kbd>
            </button>
            <button @click="darkMode = !darkMode" class="w-10 h-10 rounded-xl hover:bg-ink-100 dark:hover:bg-ink-800/60 text-ink-500 inline-flex items-center justify-center">
                <i :class="darkMode ? 'fa-sun' : 'fa-moon'" class="fas"></i>
            </button>
            @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-ink-100 dark:hover:bg-ink-800/60">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 text-white text-xs font-bold flex items-center justify-center">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="hidden sm:flex flex-col text-left leading-tight">
                        <span class="text-xs font-semibold">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-ink-400">{{ ucfirst(auth()->user()->role ?? 'user') }}</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-ink-400"></i>
                </button>
                <div x-show="open" @click.outside="open=false" x-cloak x-transition class="absolute right-0 mt-2 w-56 glass-strong rounded-2xl shadow-xl border border-ink-200/60 dark:border-ink-700/60 overflow-hidden">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-ink-100/60 dark:hover:bg-ink-800/60"><i class="fas fa-user text-ink-400 w-4"></i>Profile</a>
                    <a href="{{ route('profile.2fa') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-ink-100/60 dark:hover:bg-ink-800/60"><i class="fas fa-shield-halved text-ink-400 w-4"></i>Two-factor auth</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-ink-100/60 dark:hover:bg-ink-800/60"><i class="fas fa-sliders text-ink-400 w-4"></i>Settings</a>
                    @endif
                    <div class="border-t border-ink-200/60 dark:border-ink-700/60"></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-500/10"><i class="fas fa-arrow-right-from-bracket w-4"></i>Sign out</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>
</header>
