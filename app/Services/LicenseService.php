<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\DB;

class LicenseService
{
    public function __construct(
        protected LicenseKeyGenerator $keyGenerator
    ) {}

    public function createLicense(array $data): License
    {
        $data['license_key'] = $data['license_key'] ?? $this->keyGenerator->generate($data['key_format'] ?? 'standard');
        unset($data['key_format']);

        return License::create($data);
    }

    public function createBulkLicenses(array $baseData, int $count, string $format = 'standard'): array
    {
        $licenses = [];

        DB::transaction(function () use ($baseData, $count, $format, &$licenses) {
            $keys = $this->keyGenerator->generateBulk($count, $format);

            foreach ($keys as $key) {
                $data = array_merge($baseData, ['license_key' => $key]);
                unset($data['key_format']);
                $licenses[] = License::create($data);
            }
        });

        return $licenses;
    }

    public function activateLicense(string $licenseKey, array $activationData): array
    {
        $license = License::where('license_key', $licenseKey)->first();

        if (! $license) {
            return ['success' => false, 'message' => 'License key not found.'];
        }

        if ($license->isExpired()) {
            return ['success' => false, 'message' => 'License has expired.'];
        }

        if ($license->status !== 'active') {
            return ['success' => false, 'message' => 'License is not active. Status: '.$license->status];
        }

        if (! $license->canActivate()) {
            return ['success' => false, 'message' => 'Maximum activations reached.'];
        }

        $existingActivation = $license->activations()
            ->where('hardware_id', $activationData['hardware_id'] ?? null)
            ->where('is_active', true)
            ->first();

        if ($existingActivation) {
            return [
                'success' => true,
                'message' => 'Device already activated.',
                'activation' => $existingActivation,
                'license' => $license,
            ];
        }

        $activation = DB::transaction(function () use ($license, $activationData) {
            $activation = $license->activations()->create(array_merge($activationData, [
                'is_active' => true,
                'activated_at' => now(),
            ]));

            $license->increment('current_activations');

            return $activation;
        });

        return [
            'success' => true,
            'message' => 'License activated successfully.',
            'activation' => $activation,
            'license' => $license->fresh(),
        ];
    }

    public function deactivateLicense(string $licenseKey, string $hardwareId): array
    {
        $license = License::where('license_key', $licenseKey)->first();

        if (! $license) {
            return ['success' => false, 'message' => 'License key not found.'];
        }

        $activation = $license->activations()
            ->where('hardware_id', $hardwareId)
            ->where('is_active', true)
            ->first();

        if (! $activation) {
            return ['success' => false, 'message' => 'No active activation found for this device.'];
        }

        DB::transaction(function () use ($license, $activation) {
            $activation->update([
                'is_active' => false,
                'deactivated_at' => now(),
            ]);

            $license->decrement('current_activations');
        });

        return [
            'success' => true,
            'message' => 'License deactivated successfully.',
            'license' => $license->fresh(),
        ];
    }

    public function validateLicense(string $licenseKey): array
    {
        $license = License::with('product')->where('license_key', $licenseKey)->first();

        if (! $license) {
            return ['valid' => false, 'message' => 'License key not found.'];
        }

        if ($license->isExpired()) {
            return ['valid' => false, 'message' => 'License has expired.', 'license' => $this->formatLicenseInfo($license)];
        }

        if ($license->status !== 'active') {
            return ['valid' => false, 'message' => 'License is not active.', 'license' => $this->formatLicenseInfo($license)];
        }

        return [
            'valid' => true,
            'message' => 'License is valid.',
            'license' => $this->formatLicenseInfo($license),
        ];
    }

    private function formatLicenseInfo(License $license): array
    {
        return [
            'key' => $license->license_key,
            'product' => $license->product->name ?? null,
            'type' => $license->type,
            'status' => $license->status,
            'max_activations' => $license->max_activations,
            'current_activations' => $license->current_activations,
            'issued_at' => $license->issued_at?->toIso8601String(),
            'expires_at' => $license->expires_at?->toIso8601String(),
        ];
    }
}
