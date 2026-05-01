<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="text-xs font-semibold text-gray-600 block mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="text-xs font-semibold text-gray-600 block mb-1">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="text-xs font-semibold text-gray-600 block mb-1">Password</label>
            <input id="password" type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="text-xs font-semibold text-gray-600 block mb-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>
        <button type="submit" class="w-full py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg transition" style="background: linear-gradient(135deg, #667eea, #764ba2);">
            <i class="fas fa-user-plus mr-2"></i>Create Account
        </button>
        <p class="text-center text-xs text-gray-500">
            Already registered? <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-800">Sign in</a>
        </p>
    </form>
</x-guest-layout>
