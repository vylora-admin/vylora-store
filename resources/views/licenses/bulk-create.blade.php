@extends('layouts.app')

@section('title', 'Bulk Generate Licenses')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Bulk Generate Licenses</h1>
    <p class="text-gray-600 mt-1">Generate multiple license keys at once</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('licenses.bulk-store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
            <select name="product_id" id="product_id" required
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 10) }}" min="1" max="500" required
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                <p class="text-xs text-gray-500 mt-1">Max 500 at once</p>
            </div>
            <div>
                <label for="key_format" class="block text-sm font-medium text-gray-700 mb-1">Key Format *</label>
                <select name="key_format" id="key_format" required
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                    <option value="standard">Standard (XXXX-XXXX-XXXX-XXXX)</option>
                    <option value="extended">Extended (XXXX-XXXX-XXXX-XXXX-XXXX)</option>
                    <option value="short">Short (XXXX-XXXX-XXXX)</option>
                    <option value="uuid">UUID</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">License Type *</label>
                <select name="type" id="type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                    <option value="trial">Trial</option>
                    <option value="standard" selected>Standard</option>
                    <option value="extended">Extended</option>
                    <option value="lifetime">Lifetime</option>
                </select>
            </div>
            <div>
                <label for="max_activations" class="block text-sm font-medium text-gray-700 mb-1">Max Activations *</label>
                <input type="number" name="max_activations" id="max_activations" value="{{ old('max_activations', 1) }}" min="1" required
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
            <div>
                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
        </div>

        <div class="mb-4">
            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="2"
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">{{ old('notes') }}</textarea>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-layer-group mr-2"></i>Generate Licenses
            </button>
            <a href="{{ route('licenses.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
