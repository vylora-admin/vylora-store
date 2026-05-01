@extends('layouts.app')
@section('title', 'Create License')
@section('subtitle', 'Generate a new license key')

@section('content')
<div class="max-w-2xl">
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('licenses.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Product *</label>
                    <select name="product_id" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Customer Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">License Type *</label>
                    <select name="type" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        @foreach(['standard' => 'Standard', 'trial' => 'Trial', 'extended' => 'Extended', 'lifetime' => 'Lifetime'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', 'standard') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Key Format *</label>
                    <select name="key_format" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        <option value="standard" {{ old('key_format', 'standard') === 'standard' ? 'selected' : '' }}>Standard (XXXX-XXXX-XXXX-XXXX)</option>
                        <option value="extended" {{ old('key_format') === 'extended' ? 'selected' : '' }}>Extended (XXXX-XXXX-XXXX-XXXX-XXXX)</option>
                        <option value="short" {{ old('key_format') === 'short' ? 'selected' : '' }}>Short (XXXX-XXXX-XXXX)</option>
                        <option value="uuid" {{ old('key_format') === 'uuid' ? 'selected' : '' }}>UUID</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Max Activations *</label>
                    <input type="number" name="max_activations" value="{{ old('max_activations', 1) }}" min="1" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Expiration Date</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-primary-500/25 hover:shadow-xl transition-all">
                    <i class="fas fa-key"></i> Generate License
                </button>
                <a href="{{ route('licenses.index') }}" class="px-5 py-2.5 rounded-xl glass text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
