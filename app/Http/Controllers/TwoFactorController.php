<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorSecret;
use App\Services\TotpService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $tfa = $user->twoFactorSecret;
        $needsConfirm = $tfa && ! $tfa->confirmed_at;
        $provisioning = null;
        if ($needsConfirm) {
            $provisioning = TotpService::getProvisioningUri($tfa->secret, $user->email, config('app.name', 'KeyVault'));
        }

        return view('profile.two-factor', compact('user', 'tfa', 'provisioning', 'needsConfirm'));
    }

    public function enable(Request $request)
    {
        $user = $request->user();
        $secret = TotpService::generateSecret();
        $codes = TotpService::generateRecoveryCodes();
        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->id],
            ['secret' => $secret, 'recovery_codes' => $codes, 'confirmed_at' => null]
        );

        return redirect()->route('profile.2fa')->with('success', 'Scan the QR code, then enter a code to confirm.');
    }

    public function confirm(Request $request)
    {
        $user = $request->user();
        $tfa = $user->twoFactorSecret;
        if (! $tfa) {
            return back()->withErrors(['code' => '2FA not initialized']);
        }
        $code = (string) $request->input('code', '');
        if (! TotpService::verify($tfa->secret, $code)) {
            return back()->withErrors(['code' => 'Invalid code']);
        }
        $tfa->update(['confirmed_at' => now()]);

        return redirect()->route('profile.2fa')->with('success', '2FA enabled.');
    }

    public function disable(Request $request)
    {
        $request->user()->twoFactorSecret?->delete();

        return redirect()->route('profile.2fa')->with('success', '2FA disabled.');
    }
}
