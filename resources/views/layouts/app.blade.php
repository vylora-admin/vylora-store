<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KeyVault') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' },
                        accent: { 50:'#fdf4ff',100:'#fae8ff',200:'#f5d0fe',300:'#f0abfc',400:'#e879f9',500:'#d946ef',600:'#c026d3',700:'#a21caf',800:'#86198f',900:'#701a75' },
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); }
        .dark .glass { background: rgba(30,30,50,0.7); border-color: rgba(255,255,255,0.08); }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-mesh { background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 50%, #f0e6ff 100%); }
        .dark .gradient-mesh { background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #151530 100%); }
        .stat-card { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(99,102,241,0.25); }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover, .sidebar-link.active { background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); }
        .sidebar-link.active { border-right: 3px solid #6366f1; }
        .dark .sidebar-link:hover, .dark .sidebar-link.active { background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(168,85,247,0.2)); }
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .badge { @apply px-2.5 py-0.5 rounded-full text-xs font-semibold; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 3px; }
    </style>
</head>
<body class="font-sans antialiased gradient-mesh min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed inset-y-0 left-0 z-30 glass border-r border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 flex flex-col">
            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl gradient-bg flex items-center justify-center shadow-lg shadow-primary-500/25">
                        <i class="fas fa-key text-white text-sm"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="font-bold text-lg bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">KeyVault</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 text-center text-primary-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Dashboard</span>
                </a>
                <a href="{{ route('products.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-cube w-5 text-center text-blue-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Products</span>
                </a>
                <a href="{{ route('licenses.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('licenses.*') ? 'active' : '' }}">
                    <i class="fas fa-key w-5 text-center text-emerald-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Licenses</span>
                </a>
                <a href="{{ route('licenses.bulk-create') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('licenses.bulk-create') ? 'active' : '' }}">
                    <i class="fas fa-layer-group w-5 text-center text-amber-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Bulk Generate</span>
                </a>

                @if(auth()->user()->isManager())
                <div x-show="sidebarOpen" class="pt-4 pb-2 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Administration</span>
                </div>
                <a href="{{ route('admin.users') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users-gear w-5 text-center text-purple-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Users</span>
                </a>
                <a href="{{ route('admin.audit-logs') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-300 {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                    <i class="fas fa-clock-rotate-left w-5 text-center text-rose-500"></i>
                    <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Audit Logs</span>
                </a>
                @endif
            </nav>

            {{-- Sidebar Footer --}}
            <div class="p-3 border-t border-gray-200/50 dark:border-gray-700/50">
                <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center py-2 rounded-xl text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
                    <i :class="sidebarOpen ? 'fa-angles-left' : 'fa-angles-right'" class="fas"></i>
                </button>
            </div>
        </aside>

        {{-- Main Content --}}
        <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-20 glass border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center justify-between h-16 px-6">
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">@yield('subtitle', '')</p>
                    </div>
                    <div class="flex items-center gap-4">
                        {{-- Dark Mode Toggle --}}
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="w-10 h-10 rounded-xl glass flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">
                            <i x-show="!darkMode" class="fas fa-moon"></i>
                            <i x-show="darkMode" x-cloak class="fas fa-sun text-yellow-400"></i>
                        </button>

                        {{-- User Menu --}}
                        <div x-data="{ userMenu: false }" class="relative">
                            <button @click="userMenu = !userMenu" class="flex items-center gap-3 px-3 py-2 rounded-xl glass hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
                                <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center text-white text-xs font-bold shadow">
                                    {{ Auth::user()->initials }}
                                </div>
                                <div x-show="sidebarOpen" class="text-left hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            <div x-show="userMenu" @click.away="userMenu = false" x-cloak x-transition class="absolute right-0 mt-2 w-56 glass rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 py-2 z-50">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
                                    <i class="fas fa-user-circle w-4 text-gray-400"></i> Profile Settings
                                </a>
                                <hr class="my-1 border-gray-200/50 dark:border-gray-700/50">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="mx-6 mt-4">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mx-6 mt-4">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            {{-- Page Content --}}
            <main class="p-6 animate-fade-in">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
