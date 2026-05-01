@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Real-time overview of your platform')

@section('content')
{{-- Hero --}}
<div class="rounded-3xl glass-strong border border-ink-200/60 dark:border-ink-800/60 p-6 mb-6 relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-20" style="background: radial-gradient(closest-side, var(--accent), transparent 70%)"></div>
    <div class="relative z-10 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-widest text-ink-400 font-bold">Welcome back</p>
            <h2 class="text-2xl md:text-3xl font-extrabold mt-1">Hi, <span class="gradient-text">{{ auth()->user()->name }}</span> 👋</h2>
            <p class="text-sm text-ink-500 mt-2 max-w-xl">Your KeyAuth-style platform is running smoothly. Here's what's happening right now across all your applications and licenses.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('applications.create') }}" class="btn-primary"><i class="fas fa-plus"></i>New Application</a>
            <a href="{{ route('licenses.bulk-create') }}" class="btn-ghost"><i class="fas fa-layer-group"></i>Bulk Generate Keys</a>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Applications', 'icon' => 'fa-cubes', 'color' => 'from-indigo-500 to-violet-500', 'value' => $stats['total_applications'], 'sub' => $stats['active_applications'].' active'],
            ['label' => 'App Users', 'icon' => 'fa-users', 'color' => 'from-blue-500 to-cyan-500', 'value' => $stats['total_app_users'], 'sub' => $stats['banned_app_users'].' banned'],
            ['label' => 'Licenses', 'icon' => 'fa-key', 'color' => 'from-fuchsia-500 to-pink-500', 'value' => $stats['total_licenses'], 'sub' => $stats['active_licenses'].' active'],
            ['label' => 'Online Now', 'icon' => 'fa-bolt', 'color' => 'from-emerald-500 to-teal-500', 'value' => $stats['online_now'], 'sub' => $stats['sessions_24h'].' in 24h'],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="stat-card glass rounded-2xl p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $c['color'] }} flex items-center justify-center shadow-lg">
                    <i class="fas {{ $c['icon'] }} text-white"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-ink-400">{{ $c['label'] }}</span>
            </div>
            <p class="text-3xl font-extrabold">{{ number_format($c['value']) }}</p>
            <p class="text-xs text-ink-500 mt-1">{{ $c['sub'] }}</p>
        </div>
    @endforeach
</div>

{{-- Secondary metrics --}}
<div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
    @php
        $minor = [
            ['v' => $stats['expired_licenses'], 'l' => 'Expired', 'i' => 'fa-calendar-xmark', 'c' => 'text-red-500'],
            ['v' => $stats['suspended_licenses'], 'l' => 'Suspended', 'i' => 'fa-pause-circle', 'c' => 'text-amber-500'],
            ['v' => $stats['revoked_licenses'], 'l' => 'Revoked', 'i' => 'fa-ban', 'c' => 'text-ink-400'],
            ['v' => $stats['total_files'], 'l' => 'Files', 'i' => 'fa-folder', 'c' => 'text-blue-500'],
            ['v' => $stats['total_webhooks'], 'l' => 'Webhooks', 'i' => 'fa-bolt', 'c' => 'text-violet-500'],
            ['v' => $stats['total_users'], 'l' => 'Staff', 'i' => 'fa-user-shield', 'c' => 'text-fuchsia-500'],
        ];
    @endphp
    @foreach ($minor as $m)
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-2xl font-bold {{ $m['c'] }}">{{ number_format($m['v']) }}</p>
            <p class="text-xs text-ink-500 mt-1"><i class="fas {{ $m['i'] }} mr-1"></i>{{ $m['l'] }}</p>
        </div>
    @endforeach
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="glass rounded-2xl p-5 lg:col-span-2">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold"><i class="fas fa-chart-area text-primary-500 mr-2"></i>Sessions over time</h3>
            <span class="text-xs text-ink-400">last 14 days</span>
        </div>
        <div id="sessions-chart"></div>
    </div>
    <div class="glass rounded-2xl p-5">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-bold"><i class="fas fa-chart-pie text-accent-500 mr-2"></i>Events</h3>
            <span class="text-xs text-ink-400">last 24h</span>
        </div>
        <div id="events-chart"></div>
    </div>
</div>

{{-- Activity grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-bold mb-3"><i class="fas fa-rocket text-primary-500 mr-2"></i>Top applications</h3>
        <div class="space-y-2">
            @forelse ($topApplications as $a)
                <a href="{{ route('applications.show', $a) }}" class="flex items-center justify-between p-3 rounded-xl bg-ink-50/60 dark:bg-ink-800/40 hover:bg-ink-100/80 dark:hover:bg-ink-800/70">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg gradient-text-bg bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center text-sm font-bold">{{ Str::upper(Str::substr($a->name,0,2)) }}</div>
                        <div>
                            <p class="text-sm font-semibold leading-none">{{ $a->name }}</p>
                            <p class="text-[11px] text-ink-500 mt-1">v{{ $a->version }} · {{ $a->users_count }} users</p>
                        </div>
                    </div>
                    @if ($a->is_paused)
                        <span class="badge badge-yellow">Paused</span>
                    @else
                        <span class="badge badge-green flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full pulse-dot"></span>Live</span>
                    @endif
                </a>
            @empty
                <p class="text-sm text-ink-500 py-6 text-center">No applications yet. <a href="{{ route('applications.create') }}" class="text-primary-500 font-semibold">Create one →</a></p>
            @endforelse
        </div>
    </div>

    <div class="glass rounded-2xl p-5">
        <h3 class="text-sm font-bold mb-3"><i class="fas fa-clock-rotate-left text-accent-500 mr-2"></i>Recent activity</h3>
        <div class="space-y-2 max-h-[400px] overflow-y-auto">
            @forelse ($recentLogs as $log)
                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-ink-100/60 dark:hover:bg-ink-800/40">
                    <div class="w-8 h-8 rounded-lg bg-{{ ['debug' => 'gray', 'info' => 'blue', 'warning' => 'amber', 'error' => 'red', 'critical' => 'red'][$log->level] ?? 'gray' }}-500/15 text-{{ ['debug' => 'gray', 'info' => 'blue', 'warning' => 'amber', 'error' => 'red', 'critical' => 'red'][$log->level] ?? 'gray' }}-600 dark:text-{{ ['debug' => 'gray', 'info' => 'blue', 'warning' => 'amber', 'error' => 'red', 'critical' => 'red'][$log->level] ?? 'gray' }}-300 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bolt text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold">{{ $log->event_type }} <span class="text-ink-400 font-normal">· {{ $log->created_at->diffForHumans(null, true) }} ago</span></p>
                        <p class="text-xs text-ink-500 truncate">{{ Str::limit($log->message, 80) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-500 py-6 text-center">No activity yet.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {});
    (function() {
        const isDark = document.documentElement.classList.contains('dark');
        const text = isDark ? '#cbd5e1' : '#475569';
        const sessionsData = @json($sessionsTrend);
        const labels = sessionsData.map(d => d.label);
        const values = sessionsData.map(d => d.count);

        new ApexCharts(document.querySelector('#sessions-chart'), {
            chart: { type: 'area', height: 240, toolbar: { show: false }, foreColor: text, sparkline: { enabled: false } },
            series: [{ name: 'Sessions', data: values }],
            xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
            yaxis: { labels: { style: { fontSize: '10px' } } },
            colors: ['#6366f1'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.05, shadeIntensity: 1 } },
            grid: { borderColor: isDark ? '#1e293b' : '#e2e8f0' },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        }).render();

        const events = @json($eventBreakdown);
        new ApexCharts(document.querySelector('#events-chart'), {
            chart: { type: 'donut', height: 240, foreColor: text },
            series: events.map(e => e.count),
            labels: events.map(e => e.event_type),
            colors: ['#6366f1', '#d946ef', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6'],
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { enabled: false },
            stroke: { width: 0 },
        }).render();
    })();
</script>
@endpush
@endsection
