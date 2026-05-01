@extends('layouts.app')
@section('title', 'Edit Product')
@section('subtitle', $product->name)

@section('content')
<div class="max-w-xl">
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white font-mono">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white resize-none">{{ old('description', $product->description) }}</textarea>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Version</label>
                <input type="text" name="version" value="{{ old('version', $product->version) }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
            </div>
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-primary-500 focus:ring-primary-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-primary-500/25 hover:shadow-xl transition-all">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl glass text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
