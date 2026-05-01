@extends('layouts.app')

@section('title', 'License Details')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">License Details</h1>
        <p class="text-gray-400 mt-1 font-mono text-lg">{{ $license->license_key }}</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('licenses.edit', $license) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
        @if($license->status === 'active')
            <form action="{{ route('licenses.suspend', $license) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition" onclick="return confirm('Suspend this license?')">
                    <i class="fas fa-pause mr-1"></i>Suspend
                </button>
            </form>
            <form action="{{ route('licenses.revoke', $license) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" onclick="return confirm('Revoke this license? All activations will be deactivated.')">
                    <i class="fas fa-ban mr-1"></i>Revoke
                </button>
            </form>
        @elseif($license->status === 'suspended' || $license->status === 'inactive')
            <form action="{{ route('licenses.reactivate', $license) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-play mr-1"></i>Reactivate
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">License Information</h2>
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-sm text-gray-500">Product</dt>
                <dd class="font-medium">
                    <a href="{{ route('products.show', $license->product) }}" class="text-indigo-600 hover:underline">{{ $license->product->name }}</a>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Type</dt>
                <dd><span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ ucfirst($license->type) }}</span></dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Status</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $license->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $license->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $license->status === 'revoked' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $license->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                    ">{{ ucfirst($license->status) }}</span>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Activations</dt>
                <dd class="font-medium">{{ $license->current_activations }} / {{ $license->max_activations }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Customer</dt>
                <dd class="font-medium">{{ $license->customer_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Email</dt>
                <dd class="font-medium">{{ $license->customer_email ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Issued At</dt>
                <dd class="font-medium">{{ $license->issued_at?->format('M d, Y H:i') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Expires At</dt>
                <dd class="font-medium {{ $license->isExpired() ? 'text-red-600' : '' }}">
                    {{ $license->expires_at ? $license->expires_at->format('M d, Y H:i') : 'Never' }}
                    @if($license->isExpired()) <span class="text-xs">(Expired)</span> @endif
                </dd>
            </div>
        </dl>
        @if($license->notes)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Notes</dt>
                <dd class="mt-1 text-gray-700">{{ $license->notes }}</dd>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Copy</h2>
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="text-xs text-gray-500 mb-1">License Key</p>
            <div class="flex items-center">
                <code class="text-sm font-mono text-gray-900 break-all flex-1">{{ $license->license_key }}</code>
                <button onclick="navigator.clipboard.writeText('{{ $license->license_key }}')" class="ml-2 text-indigo-600 hover:text-indigo-800" title="Copy to clipboard">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-gray-700 mb-2">API Validation</h3>
        <div class="bg-gray-900 rounded-lg p-3 text-xs text-green-400 font-mono overflow-x-auto">
            <pre>curl -X POST /api/v1/licenses/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key": "{{ $license->license_key }}"}'</pre>
        </div>
    </div>
</div>

<!-- Activations -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Activations</h2>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Machine</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hardware ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activated At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($license->activations as $activation)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $activation->machine_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ Str::limit($activation->hardware_id ?? '-', 20) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $activation->ip_address ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $activation->domain ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $activation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $activation->is_active ? 'Active' : 'Deactivated' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $activation->activated_at?->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No activations yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
