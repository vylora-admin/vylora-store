<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationVariable;
use Illuminate\Http\Request;

class VariableController extends Controller
{
    public function index(Application $application, Request $request)
    {
        $query = $application->variables()->where('scope', $request->input('scope', 'global'));
        if ($request->filled('search')) {
            $query->where('key', 'like', "%{$request->search}%");
        }
        $variables = $query->latest()->paginate(50)->withQueryString();

        return view('variables.index', compact('application', 'variables'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:120',
            'value' => 'required|string|max:65535',
            'scope' => 'required|in:global,subscription',
            'required_level' => 'nullable|integer|min:0|max:99',
            'is_secret' => 'sometimes|boolean',
        ]);
        ApplicationVariable::create([
            'application_id' => $application->id,
            'scope' => $validated['scope'],
            'key' => $validated['key'],
            'value' => $validated['value'],
            'required_level' => $validated['required_level'] ?? 0,
            'is_secret' => $request->boolean('is_secret'),
        ]);

        return back()->with('success', 'Variable created.');
    }

    public function update(Application $application, ApplicationVariable $variable, Request $request)
    {
        abort_if($variable->application_id !== $application->id, 404);
        $validated = $request->validate([
            'value' => 'required|string|max:65535',
            'required_level' => 'nullable|integer|min:0|max:99',
        ]);
        $variable->update($validated + ['is_secret' => $request->boolean('is_secret')]);

        return back()->with('success', 'Variable updated.');
    }

    public function destroy(Application $application, ApplicationVariable $variable)
    {
        abort_if($variable->application_id !== $application->id, 404);
        $variable->delete();

        return back()->with('success', 'Variable deleted.');
    }
}
