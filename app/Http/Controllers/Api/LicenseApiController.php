<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseApiController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $result = $this->licenseService->validateLicense($request->license_key);

        return response()->json($result, $result['valid'] ? 200 : 422);
    }

    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'hardware_id' => 'required|string',
            'machine_name' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        $result = $this->licenseService->activateLicense($request->license_key, [
            'hardware_id' => $request->hardware_id,
            'machine_name' => $request->machine_name,
            'ip_address' => $request->ip(),
            'domain' => $request->domain,
        ]);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function deactivate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'hardware_id' => 'required|string',
        ]);

        $result = $this->licenseService->deactivateLicense($request->license_key, $request->hardware_id);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
