<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppFileController extends Controller
{
    public function index(Application $application)
    {
        $files = $application->files()->latest()->paginate(20);

        return view('app-files.index', compact('application', 'files'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'file' => 'required|file|max:51200',
            'required_level' => 'nullable|integer|min:0|max:99',
            'is_encrypted' => 'sometimes|boolean',
        ]);
        $upload = $request->file('file');
        $path = $upload->store("app-files/{$application->id}");
        $hash = hash_file('sha256', $upload->getRealPath());
        ApplicationFile::create([
            'application_id' => $application->id,
            'name' => $validated['name'],
            'file_path' => $path,
            'original_filename' => $upload->getClientOriginalName(),
            'size' => $upload->getSize(),
            'hash' => $hash,
            'required_level' => $validated['required_level'] ?? 0,
            'is_encrypted' => $request->boolean('is_encrypted'),
            'is_active' => true,
        ]);

        return back()->with('success', 'File uploaded.');
    }

    public function destroy(Application $application, ApplicationFile $file)
    {
        abort_if($file->application_id !== $application->id, 404);
        Storage::delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File deleted.');
    }

    public function toggle(Application $application, ApplicationFile $file)
    {
        abort_if($file->application_id !== $application->id, 404);
        $file->update(['is_active' => ! $file->is_active]);

        return back()->with('success', 'File toggled.');
    }
}
