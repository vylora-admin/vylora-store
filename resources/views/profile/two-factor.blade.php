@extends('layouts.app')
@section('title', 'Two-Factor Authentication')

@section('content')
<div class="max-w-2xl">
    <div class="glass-strong rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white text-base font-bold flex items-center justify-center"><i class="fas fa-shield-halved"></i></div>
            <div>
                <h2 class="text-lg font-bold">Two-Factor Authentication</h2>
                <p class="text-xs text-ink-500">Add an extra layer of security to your admin login.</p>
            </div>
        </div>

        @if (!$tfa)
            <p class="text-sm text-ink-500 mb-4">2FA is currently disabled.</p>
            <form method="POST" action="{{ route('profile.2fa.enable') }}">@csrf
                <button class="btn-primary"><i class="fas fa-shield"></i>Enable 2FA</button>
            </form>
        @elseif ($needsConfirm)
            <div class="rounded-xl bg-ink-100/60 dark:bg-ink-800/40 p-4 mb-4">
                <p class="text-xs text-ink-500 mb-2">1. Scan this QR code with Google Authenticator or Authy:</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($provisioning) }}" class="mx-auto rounded-lg bg-white p-2" alt="QR">
                <p class="text-[11px] text-center text-ink-400 mt-2">Or enter this code manually: <code class="font-mono text-primary-500">{{ $tfa->secret }}</code></p>
            </div>
            <form method="POST" action="{{ route('profile.2fa.confirm') }}" class="space-y-3">@csrf
                <p class="text-xs font-semibold">2. Enter the 6-digit code from your app:</p>
                <input name="code" pattern="\d{6}" maxlength="6" required class="input text-center text-2xl font-mono tracking-widest" placeholder="000000">
                <button class="btn-primary w-full justify-center">Confirm & Enable 2FA</button>
            </form>
        @else
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 mb-4">
                <i class="fas fa-circle-check text-emerald-500"></i>
                <p class="text-sm text-emerald-700 dark:text-emerald-300">2FA is active. You'll be asked for a code on every login.</p>
            </div>
            @if ($tfa->recovery_codes)
                <div class="mb-4">
                    <p class="text-xs font-semibold mb-2">Recovery codes (keep them safe):</p>
                    <div class="grid grid-cols-2 gap-2 font-mono text-xs p-3 rounded-lg bg-ink-900 text-ink-100">
                        @foreach ($tfa->recovery_codes as $code)<span>{{ $code }}</span>@endforeach
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('profile.2fa.disable') }}" onsubmit="return confirm('Disable 2FA?')">@csrf @method('DELETE')
                <button class="btn-danger"><i class="fas fa-shield-slash"></i>Disable 2FA</button>
            </form>
        @endif
    </div>
</div>
@endsection
