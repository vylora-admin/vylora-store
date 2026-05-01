<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationWebhook;
use App\Models\ApplicationWebhookDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public static function dispatch(Application $app, string $event, array $payload): void
    {
        $webhooks = ApplicationWebhook::where('application_id', $app->id)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($w) => in_array($event, $w->events ?? []));

        foreach ($webhooks as $webhook) {
            self::deliver($webhook, $event, $payload);
        }

        if (! empty($app->discord_webhook_url) && in_array($event, $app->webhook_events ?? [])) {
            self::deliverDiscord($app, $event, $payload);
        }
    }

    public static function deliver(ApplicationWebhook $webhook, string $event, array $payload): void
    {
        $body = [
            'event' => $event,
            'application_id' => $webhook->application_id,
            'timestamp' => now()->toIso8601String(),
            'data' => $payload,
        ];
        $headers = $webhook->headers ?? [];
        if (! empty($webhook->secret)) {
            $signature = hash_hmac('sha256', json_encode($body), $webhook->secret);
            $headers['X-KeyVault-Signature'] = 'sha256='.$signature;
        }
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = 'KeyVault-Webhook/1.0';

        $delivery = ApplicationWebhookDelivery::create([
            'application_webhook_id' => $webhook->id,
            'event' => $event,
            'payload' => $body,
        ]);

        try {
            $response = Http::timeout($webhook->timeout_seconds ?? 10)
                ->withHeaders($headers)
                ->retry($webhook->retry_count ?? 1, 200)
                ->post($webhook->url, $body);

            $delivery->update([
                'status_code' => $response->status(),
                'response' => substr($response->body(), 0, 4000),
                'is_success' => $response->successful(),
                'attempts' => 1,
                'delivered_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Webhook delivery failed', ['url' => $webhook->url, 'error' => $e->getMessage()]);
            $delivery->update([
                'response' => substr($e->getMessage(), 0, 4000),
                'is_success' => false,
                'attempts' => 1,
                'delivered_at' => now(),
            ]);
        }
    }

    public static function deliverDiscord(Application $app, string $event, array $payload): void
    {
        $color = match ($event) {
            'user_login' => 0x4CAF50,
            'user_register' => 0x2196F3,
            'user_banned' => 0xF44336,
            'license_used' => 0x9C27B0,
            default => 0x607D8B,
        };
        $fields = [];
        foreach ($payload as $k => $v) {
            if (is_scalar($v) && strlen((string) $v) <= 1024) {
                $fields[] = ['name' => $k, 'value' => (string) $v, 'inline' => true];
            }
        }
        $embed = [
            'title' => $app->name.' — '.$event,
            'color' => $color,
            'fields' => $fields,
            'timestamp' => now()->toIso8601String(),
            'footer' => ['text' => 'KeyVault Pro'],
        ];
        try {
            Http::timeout(5)->post($app->discord_webhook_url, [
                'embeds' => [$embed],
                'username' => 'KeyVault',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Discord webhook failed', ['error' => $e->getMessage()]);
        }
    }
}
