<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="text-xs font-semibold text-gray-600 block mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="text-xs font-semibold text-gray-600 block mb-1">Password</label>
            <input id="password" type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                <span class="text-xs text-gray-600">Remember me</span>
            </label>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Forgot password?</a>
            @endif
        </div>
        <button type="submit" class="w-full py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg transition" style="background: linear-gradient(135deg, #667eea, #764ba2);">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>
        <p class="text-center text-xs text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:text-indigo-800">Register</a>
        </p>
    </form>
</x-guest-layout>
