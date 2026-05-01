<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'License Manager') - KeyVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight">
                        <i class="fas fa-key mr-2"></i>KeyVault
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : 'hover:bg-indigo-600' }}">
                            <i class="fas fa-chart-bar mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-indigo-800' : 'hover:bg-indigo-600' }}">
                            <i class="fas fa-box mr-1"></i> Products
                        </a>
                        <a href="{{ route('licenses.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('licenses.*') ? 'bg-indigo-800' : 'hover:bg-indigo-600' }}">
                            <i class="fas fa-id-card mr-1"></i> Licenses
                        </a>
                        <a href="{{ route('licenses.bulk-create') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('licenses.bulk-create') ? 'bg-indigo-800' : 'hover:bg-indigo-600' }}">
                            <i class="fas fa-layer-group mr-1"></i> Bulk Generate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-sm">
            KeyVault License Management System &copy; {{ date('Y') }}
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
