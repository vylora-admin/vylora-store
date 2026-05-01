<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public const FIELDS = [
        'general' => [
            'site_name' => ['label' => 'Site Name', 'type' => 'string', 'default' => 'KeyVault Pro'],
            'site_url' => ['label' => 'Site URL', 'type' => 'string', 'default' => ''],
            'logo_url' => ['label' => 'Logo URL', 'type' => 'string', 'default' => ''],
            'support_email' => ['label' => 'Support Email', 'type' => 'string', 'default' => ''],
            'allow_signup' => ['label' => 'Allow admin signup', 'type' => 'bool', 'default' => false],
        ],
        'theme' => [
            'theme_mode' => ['label' => 'Default theme (light/dark/system)', 'type' => 'string', 'default' => 'system'],
            'accent_color' => ['label' => 'Accent color (hex)', 'type' => 'string', 'default' => '#6366f1'],
            'primary_color' => ['label' => 'Primary color (hex)', 'type' => 'string', 'default' => '#6366f1'],
            'enable_glassmorphism' => ['label' => 'Enable glassmorphism', 'type' => 'bool', 'default' => true],
            'enable_animations' => ['label' => 'Enable UI animations', 'type' => 'bool', 'default' => true],
        ],
        'mail' => [
            'smtp_host' => ['label' => 'SMTP Host', 'type' => 'string', 'default' => ''],
            'smtp_port' => ['label' => 'SMTP Port', 'type' => 'int', 'default' => 587],
            'smtp_user' => ['label' => 'SMTP Username', 'type' => 'string', 'default' => ''],
            'smtp_pass' => ['label' => 'SMTP Password', 'type' => 'string', 'default' => ''],
            'smtp_from' => ['label' => 'From address', 'type' => 'string', 'default' => 'noreply@keyvault.local'],
            'smtp_encryption' => ['label' => 'Encryption (tls/ssl/none)', 'type' => 'string', 'default' => 'tls'],
        ],
        'security' => [
            'require_2fa_admin' => ['label' => 'Require 2FA for admin', 'type' => 'bool', 'default' => false],
            'rate_limit_api_min' => ['label' => 'Rate limit per minute (API)', 'type' => 'int', 'default' => 120],
            'rate_limit_login_min' => ['label' => 'Login attempts per minute', 'type' => 'int', 'default' => 10],
            'enable_captcha' => ['label' => 'Enable CAPTCHA on login', 'type' => 'bool', 'default' => false],
            'recaptcha_site_key' => ['label' => 'reCAPTCHA site key', 'type' => 'string', 'default' => ''],
            'recaptcha_secret' => ['label' => 'reCAPTCHA secret key', 'type' => 'string', 'default' => ''],
            'session_lifetime_hours' => ['label' => 'API session lifetime (hours)', 'type' => 'int', 'default' => 24],
        ],
        'integrations' => [
            'discord_webhook_default' => ['label' => 'Default Discord webhook URL', 'type' => 'string', 'default' => ''],
            'enable_geoip' => ['label' => 'Enable GeoIP enrichment', 'type' => 'bool', 'default' => false],
        ],
    ];

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'general');
        $values = [];
        foreach (self::FIELDS as $group => $fields) {
            foreach ($fields as $key => $meta) {
                $values[$key] = Setting::get($key, $meta['default']);
            }
        }

        return view('settings.index', [
            'tab' => $tab,
            'fields' => self::FIELDS,
            'values' => $values,
        ]);
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');
        $fields = self::FIELDS[$tab] ?? [];
        foreach ($fields as $key => $meta) {
            $value = $request->input($key);
            if ($meta['type'] === 'bool') {
                $value = $request->boolean($key) ? '1' : '0';
            }
            Setting::set($key, (string) ($value ?? ''), $meta['type'], $tab);
        }

        return redirect()->route('settings.index', ['tab' => $tab])->with('success', 'Settings saved.');
    }
}
