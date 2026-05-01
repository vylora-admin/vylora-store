<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Services\AddonManager;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index()
    {
        AddonManager::sync();
        $addons = Addon::orderBy('name')->get();

        return view('addons.index', compact('addons'));
    }

    public function toggle(Addon $addon)
    {
        $addon->update(['is_enabled' => ! $addon->is_enabled]);

        return back()->with('success', $addon->is_enabled ? "{$addon->name} enabled." : "{$addon->name} disabled.");
    }

    public function configure(Addon $addon, Request $request)
    {
        $config = $request->input('config', []);
        $addon->update(['config' => $config]);

        return back()->with('success', "{$addon->name} configured.");
    }

    public function rescan()
    {
        AddonManager::sync();

        return back()->with('success', 'Addons rescanned.');
    }
}
