@extends('layouts.app')
@section('title', 'Edit License')
@section('subtitle', $license->license_key)

@section('content')
<div class="max-w-2xl">
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('licenses.update', $license) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">License Key</label>
                    <input type="text" value="{{ $license->license_key }}" disabled class="w-full px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-mono text-gray-500 dark:text-gray-400">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Product *</label>
                    <select name="product_id" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $license->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Status *</label>
                    <select name="status" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        @foreach(['active','inactive','expired','suspended','revoked'] as $s)
                        <option value="{{ $s }}" {{ $license->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $license->customer_name) }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Customer Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $license->customer_email) }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Type *</label>
                    <select name="type" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                        @foreach(['trial','standard','extended','lifetime'] as $t)
                        <option value="{{ $t }}" {{ $license->type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Max Activations *</label>
                    <input type="number" name="max_activations" value="{{ old('max_activations', $license->max_activations) }}" min="1" required class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Expiration Date</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $license->expires_at?->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white resize-none">{{ old('notes', $license->notes) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold shadow-lg shadow-primary-500/25 hover:shadow-xl transition-all">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('licenses.show', $license) }}" class="px-5 py-2.5 rounded-xl glass text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
