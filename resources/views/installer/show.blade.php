<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Install KeyVault Pro</title>
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body { font-family: Inter, sans-serif; background: linear-gradient(135deg, #0b0d18 0%, #1a1a3e 60%, #0f1224 100%); color: white; }
        .glass { background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xl glass rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center text-white text-2xl mb-3"><i class="fas fa-shield-halved"></i></div>
            <h1 class="text-2xl font-extrabold bg-gradient-to-r from-indigo-300 to-fuchsia-300 bg-clip-text text-transparent">Welcome to KeyVault Pro</h1>
            <p class="text-sm text-white/60 mt-2">Set up your platform in 30 seconds</p>
        </div>

        <div class="grid grid-cols-2 gap-2 mb-5 text-xs">
            @foreach ([
                'PHP 8.1+' => $php_version_ok,
                'PDO extension' => $pdo_ok,
                'Mbstring extension' => $mbstring_ok,
                'Storage writable' => $storage_writable,
            ] as $check => $ok)
                <div class="flex items-center gap-2 p-2 rounded-lg bg-white/5">
                    <i class="fas fa-{{ $ok ? 'check text-emerald-400' : 'xmark text-red-400' }}"></i>
                    <span>{{ $check }}</span>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('installer.run') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-semibold mb-1 text-white/70">Site name</label>
                <input name="site_name" value="KeyVault Pro" class="w-full px-3 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 text-sm"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold mb-1 text-white/70">Admin name</label>
                    <input name="admin_name" required class="w-full px-3 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:outline-none text-sm"></div>
                <div><label class="block text-xs font-semibold mb-1 text-white/70">Admin email</label>
                    <input name="admin_email" type="email" required class="w-full px-3 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:outline-none text-sm"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold mb-1 text-white/70">Password</label>
                    <input name="admin_password" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:outline-none text-sm"></div>
                <div><label class="block text-xs font-semibold mb-1 text-white/70">Confirm password</label>
                    <input name="admin_password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-xl bg-white/10 border border-white/20 focus:outline-none text-sm"></div>
            </div>
            @if ($errors->any())
                <div class="text-xs text-red-300 bg-red-500/10 border border-red-500/30 rounded-xl p-3">
                    @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            <button class="w-full mt-4 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 font-bold text-white shadow-lg hover:opacity-90 transition">Install & Continue</button>
        </form>
    </div>
</body>
</html>
