@extends('layouts.app')
@section('title', $product->name)
@section('subtitle', 'Product details')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="glass rounded-2xl p-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-primary-600 flex items-center justify-center shadow-lg"><i class="fas fa-cube text-white text-lg"></i></div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h2>
                        <p class="text-xs text-gray-500 font-mono">{{ $product->slug }} · v{{ $product->version ?? '1.0' }}</p>
                    </div>
                </div>
                @if($product->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">{{ $product->description }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                <a href="{{ route('products.edit', $product) }}" class="w-9 h-9 rounded-xl glass flex items-center justify-center text-gray-500 hover:text-primary-500 transition"><i class="fas fa-pen text-sm"></i></a>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 mt-5">
            <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-primary-600">{{ $product->licenses()->count() }}</p>
                <p class="text-[10px] font-bold uppercase text-gray-400">Licenses</p>
            </div>
            <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $product->licenses()->where('status', 'active')->count() }}</p>
                <p class="text-[10px] font-bold uppercase text-gray-400">Active</p>
            </div>
            <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-gray-500">{{ $product->created_at->format('M Y') }}</p>
                <p class="text-[10px] font-bold uppercase text-gray-400">Created</p>
            </div>
        </div>
    </div>
</div>
@endsection
