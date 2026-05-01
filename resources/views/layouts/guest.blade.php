<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KeyVault') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        .glass { background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white">KeyVault</h1>
            <p class="text-white/70 text-sm mt-1">License Management System</p>
        </div>
        <div class="glass rounded-2xl p-8 shadow-2xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
