<?php

namespace App\Services;

use App\Models\Addon;
use Illuminate\Support\Facades\File;

class AddonManager
{
    protected static array $hooks = [];

    protected static bool $loaded = false;

    public static function path(): string
    {
        return base_path('addons');
    }

    public static function discover(): array
    {
        $path = self::path();
        if (! is_dir($path)) {
            return [];
        }
        $found = [];
        foreach (scandir($path) as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }
            $manifestPath = $path.'/'.$entry.'/addon.json';
            if (file_exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                if (is_array($manifest)) {
                    $manifest['__path'] = $path.'/'.$entry;
                    $manifest['__slug'] = $manifest['slug'] ?? $entry;
                    $found[$manifest['__slug']] = $manifest;
                }
            }
        }

        return $found;
    }

    public static function sync(): void
    {
        $found = self::discover();
        foreach ($found as $slug => $manifest) {
            Addon::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'] ?? $slug,
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $manifest['author'] ?? null,
                    'description' => $manifest['description'] ?? null,
                    'icon' => $manifest['icon'] ?? null,
                    'manifest' => $manifest,
                ]
            );
        }
    }

    public static function loadEnabled(): void
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;

        $enabled = Addon::where('is_enabled', true)->get();
        foreach ($enabled as $addon) {
            $entry = ($addon->manifest['__path'] ?? self::path().'/'.$addon->slug).'/'.($addon->manifest['entry'] ?? 'index.php');
            if (file_exists($entry)) {
                try {
                    require_once $entry;
                } catch (\Throwable $e) {
                    \Log::error('Addon load failed', ['slug' => $addon->slug, 'error' => $e->getMessage()]);
                }
            }
        }
    }

    public static function on(string $event, callable $callback, int $priority = 10): void
    {
        self::$hooks[$event][$priority][] = $callback;
    }

    public static function fire(string $event, array $args = []): array
    {
        $results = [];
        if (! isset(self::$hooks[$event])) {
            return $results;
        }
        ksort(self::$hooks[$event]);
        foreach (self::$hooks[$event] as $priority => $callbacks) {
            foreach ($callbacks as $cb) {
                try {
                    $results[] = $cb(...$args);
                } catch (\Throwable $e) {
                    \Log::error('Addon hook failed', ['event' => $event, 'error' => $e->getMessage()]);
                }
            }
        }

        return $results;
    }
}
