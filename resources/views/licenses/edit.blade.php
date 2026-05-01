@extends('layouts.app')

@section('title', 'Edit License')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Edit License</h1>
    <p class="text-gray-600 mt-1 font-mono">{{ $license->license_key }}</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('licenses.update', $license) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-4">
            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
            <select name="product_id" id="product_id" required
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $license->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $license->customer_name) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
            <div>
                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $license->customer_email) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">License Type *</label>
                <select name="type" id="type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                    @foreach(['trial', 'standard', 'extended', 'lifetime'] as $type)
                        <option value="{{ $type }}" {{ old('type', $license->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
                    @foreach(['active', 'inactive', 'expired', 'suspended', 'revoked'] as $status)
                        <option value="{{ $status }}" {{ old('status', $license->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="max_activations" class="block text-sm font-medium text-gray-700 mb-1">Max Activations *</label>
                <input type="number" name="max_activations" id="max_activations" value="{{ old('max_activations', $license->max_activations) }}" min="1" required
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                <input type="datetime-local" name="expires_at" id="expires_at"
                       value="{{ old('expires_at', $license->expires_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">
            </div>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="2"
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 border">{{ old('notes', $license->notes) }}</textarea>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-save mr-2"></i>Update License
            </button>
            <a href="{{ route('licenses.show', $license) }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
