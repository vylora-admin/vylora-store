<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Product;
use App\Services\AuditService;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function index(Request $request)
    {
        $query = License::with('product');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $licenses = $query->latest()->paginate(20)->withQueryString();
        $products = Product::where('is_active', true)->get();

        return view('licenses.index', compact('licenses', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();

        return view('licenses.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'type' => 'required|in:trial,standard,extended,lifetime',
            'max_activations' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
            'key_format' => 'required|in:standard,extended,short,uuid',
        ]);

        $license = $this->licenseService->createLicense($validated);

        AuditService::log('license_created', $license, null, $validated, "Created license: {$license->license_key}");

        return redirect()->route('licenses.index')->with('success', 'License created successfully.');
    }

    public function bulkCreate()
    {
        $products = Product::where('is_active', true)->get();

        return view('licenses.bulk-create', compact('products'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'type' => 'required|in:trial,standard,extended,lifetime',
            'max_activations' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
            'key_format' => 'required|in:standard,extended,short,uuid',
            'quantity' => 'required|integer|min:1|max:500',
        ]);

        $quantity = $validated['quantity'];
        $format = $validated['key_format'];
        unset($validated['quantity'], $validated['key_format']);

        $licenses = $this->licenseService->createBulkLicenses($validated, $quantity, $format);

        AuditService::log('licenses_bulk_created', null, null, ['quantity' => count($licenses)], count($licenses).' licenses bulk-created');

        return redirect()->route('licenses.index')
            ->with('success', count($licenses).' licenses created successfully.');
    }

    public function show(License $license)
    {
        $license->load(['product', 'activations']);

        return view('licenses.show', compact('license'));
    }

    public function edit(License $license)
    {
        $products = Product::where('is_active', true)->get();

        return view('licenses.edit', compact('license', 'products'));
    }

    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'type' => 'required|in:trial,standard,extended,lifetime',
            'status' => 'required|in:active,inactive,expired,suspended,revoked',
            'max_activations' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $license->update($validated);

        return redirect()->route('licenses.show', $license)->with('success', 'License updated successfully.');
    }

    public function destroy(License $license)
    {
        $license->delete();

        return redirect()->route('licenses.index')->with('success', 'License deleted successfully.');
    }

    public function suspend(License $license)
    {
        $license->update(['status' => 'suspended']);
        AuditService::log('license_suspended', $license, null, null, "Suspended license: {$license->license_key}");

        return back()->with('success', 'License suspended.');
    }

    public function revoke(License $license)
    {
        $license->update(['status' => 'revoked']);
        $license->activations()->update(['is_active' => false, 'deactivated_at' => now()]);
        $license->update(['current_activations' => 0]);
        AuditService::log('license_revoked', $license, null, null, "Revoked license: {$license->license_key}");

        return back()->with('success', 'License revoked and all activations deactivated.');
    }

    public function reactivate(License $license)
    {
        $license->update(['status' => 'active']);
        AuditService::log('license_reactivated', $license, null, null, "Reactivated license: {$license->license_key}");

        return back()->with('success', 'License reactivated.');
    }

    public function export(Request $request)
    {
        $query = License::with('product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $licenses = $query->latest()->get();

        $filename = 'licenses_export_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($licenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['License Key', 'Product', 'Customer Name', 'Customer Email', 'Type', 'Status', 'Activations', 'Max Activations', 'Expires At', 'Created At']);

            foreach ($licenses as $license) {
                fputcsv($file, [
                    $license->license_key,
                    $license->product->name ?? 'N/A',
                    $license->customer_name,
                    $license->customer_email,
                    $license->type,
                    $license->status,
                    $license->current_activations,
                    $license->max_activations,
                    $license->expires_at?->format('Y-m-d'),
                    $license->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        AuditService::log('licenses_exported', null, null, ['count' => $licenses->count()], "Exported {$licenses->count()} licenses");

        return response()->stream($callback, 200, $headers);
    }
}
