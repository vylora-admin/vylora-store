<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Str;

class LicenseKeyGenerator
{
    /**
     * Generate a single unique license key.
     *
     * Formats:
     *  - standard:  XXXX-XXXX-XXXX-XXXX
     *  - extended:  XXXX-XXXX-XXXX-XXXX-XXXX
     *  - short:     XXXX-XXXX-XXXX
     *  - uuid:      UUID v4
     */
    public function generate(string $format = 'standard'): string
    {
        do {
            $key = match ($format) {
                'extended' => $this->buildSegmented(5),
                'short' => $this->buildSegmented(3),
                'uuid' => (string) Str::uuid(),
                default => $this->buildSegmented(4),
            };
        } while (License::where('license_key', $key)->exists());

        return $key;
    }

    /**
     * Generate multiple unique keys at once.
     */
    public function generateBulk(int $count, string $format = 'standard'): array
    {
        $keys = [];
        for ($i = 0; $i < $count; $i++) {
            $keys[] = $this->generate($format);
        }

        return $keys;
    }

    /**
     * Generate a key with a custom prefix (e.g. PRO-XXXX-XXXX).
     */
    public function generateWithPrefix(string $prefix, string $format = 'standard'): string
    {
        $key = $this->generate($format);

        return strtoupper($prefix).'-'.$key;
    }

    private function buildSegmented(int $segments, int $length = 4): string
    {
        $parts = [];
        for ($i = 0; $i < $segments; $i++) {
            $parts[] = strtoupper(Str::random($length));
        }

        return implode('-', $parts);
    }
}
