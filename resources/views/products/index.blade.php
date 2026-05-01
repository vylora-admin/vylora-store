@extends('layouts.app')
@section('title', 'Products')
@section('subtitle', 'Manage your software products')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($products as $product)
    <div class="stat-card glass rounded-2xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-primary-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-cube text-white"></i>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-500' }}">
                {{ $product->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <h3 class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</h3>
        <p class="text-xs text-gray-500 font-mono mb-3">{{ $product->slug }} · v{{ $product->version ?? '1.0' }}</p>
        @if($product->description)
        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $product->description }}</p>
        @endif
        <div class="flex items-center justify-between pt-3 border-t border-gray-200/50 dark:border-gray-700/50">
            <span class="text-xs text-gray-500"><i class="fas fa-key mr-1 text-primary-400"></i>{{ $product->licenses_count ?? $product->licenses()->count() }} licenses</span>
            <div class="flex gap-1">
                <a href="{{ route('products.show', $product) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition"><i class="fas fa-eye text-xs"></i></a>
                <a href="{{ route('products.edit', $product) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition"><i class="fas fa-pen text-xs"></i></a>
                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full glass rounded-2xl p-12 text-center">
        <i class="fas fa-cube text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-400">No products yet. Create your first product!</p>
    </div>
    @endforelse
</div>
@endsection
