<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Seller;
use App\Models\SellerTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::with(['user', 'application'])->latest()->paginate(20);

        return view('sellers.index', compact('sellers'));
    }

    public function create()
    {
        $applications = Application::orderBy('name')->get();

        return view('sellers.create', compact('applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'application_id' => 'nullable|exists:applications,id',
            'balance' => 'nullable|numeric|min:0',
            'display_name' => 'nullable|string|max:120',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'seller',
        ]);
        Seller::create([
            'user_id' => $user->id,
            'application_id' => $validated['application_id'] ?? null,
            'display_name' => $validated['display_name'] ?? $validated['name'],
            'balance' => $validated['balance'] ?? 0,
            'permissions' => [],
            'is_active' => true,
        ]);

        return redirect()->route('sellers.index')->with('success', 'Seller created.');
    }

    public function show(Seller $seller)
    {
        $seller->load(['user', 'application']);
        $transactions = $seller->transactions()->latest()->paginate(20);

        return view('sellers.show', compact('seller', 'transactions'));
    }

    public function adjustBalance(Seller $seller, Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
        ]);
        $newBalance = $seller->balance + $validated['amount'];
        if ($newBalance < 0) {
            return back()->withErrors(['amount' => 'Balance cannot be negative']);
        }
        $seller->update(['balance' => $newBalance]);
        SellerTransaction::create([
            'seller_id' => $seller->id,
            'type' => $validated['amount'] >= 0 ? 'credit' : 'debit',
            'amount' => $validated['amount'],
            'balance_after' => $newBalance,
            'notes' => $validated['notes'] ?? 'Manual adjustment',
        ]);

        return back()->with('success', 'Balance adjusted.');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();

        return back()->with('success', 'Seller removed.');
    }
}
