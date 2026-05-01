<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkMode: localStorage.getItem('darkMode') === null ? true : localStorage.getItem('darkMode') === 'true',
          sidebarOpen: window.innerWidth >= 1024,
          paletteOpen: false,
          init() { this.$watch('darkMode', v => localStorage.setItem('darkMode', v)); }
      }"
      :class="{ 'dark': darkMode }"
      @keydown.window.ctrl.k.prevent="paletteOpen = true"
      @keydown.window.escape="paletteOpen = false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::get('site_name', config('app.name', 'KeyVault Pro')) }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    @php $accent = \App\Models\Setting::get('accent_color', '#6366f1'); @endphp
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                        accent: { 50:'#fdf4ff',100:'#fae8ff',200:'#f5d0fe',300:'#f0abfc',400:'#e879f9',500:'#d946ef',600:'#c026d3',700:'#a21caf',800:'#86198f',900:'#701a75' },
                        ink: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#030712' }
                    },
                    boxShadow: {
                        'soft': '0 2px 30px -10px rgba(99,102,241,0.18)',
                        'glow': '0 0 0 4px rgba(99,102,241,0.18)'
                    },
                    backdropBlur: { xs: '2px' }
                }
            }
        }
    </script>
    <style>
        :root { --accent: {{ $accent }}; }
        * { -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        body { background: radial-gradient(60% 50% at 0% 0%, rgba(99,102,241,0.10) 0%, transparent 60%),
                          radial-gradient(50% 40% at 100% 0%, rgba(217,70,239,0.10) 0%, transparent 60%),
                          linear-gradient(180deg, #f6f8fb 0%, #eef1f7 100%); }
        .dark body { background: radial-gradient(60% 50% at 0% 0%, rgba(99,102,241,0.18) 0%, transparent 60%),
                                  radial-gradient(50% 40% at 100% 0%, rgba(168,85,247,0.16) 0%, transparent 60%),
                                  linear-gradient(180deg, #0b0d18 0%, #0f1224 60%, #0a0a1f 100%); }

        .glass { background: rgba(255,255,255,0.72); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border: 1px solid rgba(255,255,255,0.55); }
        .dark .glass { background: rgba(20,24,38,0.62); border-color: rgba(255,255,255,0.06); }

        .glass-strong { background: rgba(255,255,255,0.92); backdrop-filter: blur(20px); border: 1px solid rgba(15,23,42,0.06); }
        .dark .glass-strong { background: rgba(15,18,30,0.85); border-color: rgba(255,255,255,0.06); }

        .card-grad { background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(217,70,239,0.06) 100%); }
        .dark .card-grad { background: linear-gradient(135deg, rgba(99,102,241,0.18) 0%, rgba(217,70,239,0.10) 100%); }

        .gradient-text { background: linear-gradient(135deg, #6366f1 0%, #d946ef 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }

        .stat-card { transition: transform .25s, box-shadow .25s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 22px 40px -18px rgba(99,102,241,0.35); }

        .sidebar-link { transition: all .15s ease; }
        .sidebar-link:hover { background: linear-gradient(90deg, rgba(99,102,241,0.10), rgba(217,70,239,0.07)); color: var(--accent); }
        .sidebar-link.active { background: linear-gradient(90deg, rgba(99,102,241,0.15), rgba(217,70,239,0.10)); color: var(--accent); border-right: 3px solid var(--accent); }
        .dark .sidebar-link:hover, .dark .sidebar-link.active { background: linear-gradient(90deg, rgba(99,102,241,0.22), rgba(217,70,239,0.12)); }

        .badge { padding: 0.15rem 0.55rem; border-radius: 999px; font-size: .68rem; font-weight: 700; letter-spacing: .02em; }
        .badge-green { background: rgba(16,185,129,.12); color: #10b981; }
        .badge-red { background: rgba(239,68,68,.12); color: #ef4444; }
        .badge-yellow { background: rgba(245,158,11,.14); color: #f59e0b; }
        .badge-blue { background: rgba(59,130,246,.14); color: #3b82f6; }
        .badge-violet { background: rgba(139,92,246,.14); color: #8b5cf6; }
        .badge-pink { background: rgba(236,72,153,.14); color: #ec4899; }

        .input { @apply w-full px-3 py-2.5 rounded-xl bg-white/70 dark:bg-ink-900/40 border border-ink-200/70 dark:border-ink-700/60 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 text-sm; }
        .btn { @apply inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition; }
        .btn-primary { @apply btn text-white shadow-soft; background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .btn-primary:hover { filter: brightness(1.08); }
        .btn-ghost { @apply btn bg-white/60 dark:bg-ink-800/40 hover:bg-white dark:hover:bg-ink-800 border border-ink-200/60 dark:border-ink-700/60 text-ink-700 dark:text-ink-200; }
        .btn-danger { @apply btn bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-300 border border-red-500/20; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.25); border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.4); }

        @keyframes fadeIn { from { opacity:0; transform: translateY(6px) } to { opacity:1; transform:none } }
        .animate-fade-in { animation: fadeIn .35s ease-out; }
        @keyframes pulseDot { 0%,100% { opacity:1 } 50% { opacity:.35 } }
        .pulse-dot { animation: pulseDot 1.6s ease-in-out infinite; }
    </style>
    @stack('head')
</head>
<body class="font-sans text-ink-800 dark:text-ink-100 min-h-screen">

<div class="flex min-h-screen">
    @include('partials.sidebar')

    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'" class="flex-1 flex flex-col min-w-0 transition-all duration-200">
        @include('partials.topbar')

        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 max-w-screen-2xl w-full mx-auto animate-fade-in">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 flex items-center justify-between gap-3 rounded-2xl glass-strong px-4 py-3 border border-emerald-500/30">
                    <span class="text-emerald-700 dark:text-emerald-300 text-sm font-medium">
                        <i class="fas fa-circle-check mr-2"></i>{{ session('success') }}
                    </span>
                    <button @click="show=false" class="text-ink-400 hover:text-ink-600 dark:hover:text-white"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl glass-strong px-4 py-3 border border-red-500/30">
                    <p class="text-red-700 dark:text-red-300 text-sm font-semibold mb-1"><i class="fas fa-triangle-exclamation mr-2"></i>Please fix these errors:</p>
                    <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-300 space-y-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-6 py-4 text-xs text-ink-400 dark:text-ink-500 flex justify-between border-t border-ink-200/60 dark:border-ink-800/60">
            <span>© {{ date('Y') }} {{ \App\Models\Setting::get('site_name', config('app.name', 'KeyVault Pro')) }}</span>
            <span>v2.0 · KeyAuth-compatible API at <code class="text-primary-500">/api/1.3</code></span>
        </footer>
    </div>
</div>

@include('partials.command-palette')

@stack('scripts')
</body>
</html>
