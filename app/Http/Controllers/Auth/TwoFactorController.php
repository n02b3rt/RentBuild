<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Wyświetla ekran setupu 2FA z QR, kluczem i formularzem.
     */
    public function showSetup(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return redirect()->route('profile.edit')->with('status', '2FA jest już aktywne.');
        }

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->twoFactorService->generateEncryptedSecret();
            $user->save();
        }

        $secret = $this->twoFactorService->decryptSecret($user->two_factor_secret);
        $qrUrl = $this->twoFactorService->getOtpAuthUrl(config('app.name'), $user->email, $secret);
        $qrSvg = $this->twoFactorService->getInlineQrCodeSvg($qrUrl);

        return view('profile.2fa.setup', [
            'qrSvg'  => $qrSvg,
            'secret' => $secret,
        ]);
    }

    /**
     * Potwierdzenie kodu z aplikacji (np. Google Authenticator).
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $secret = $this->twoFactorService->decryptSecret($user->two_factor_secret);

        if (!$this->twoFactorService->verifyCode($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Niepoprawny kod.']);
        }

        $user->two_factor_enabled = true;
        $backupCodes = $this->twoFactorService->generateBackupCodes(5);
        $user->two_factor_recovery_codes = encrypt(json_encode($backupCodes));

        $user->save();

        return redirect()->route('profile.edit')->with('status', '2FA aktywowane.');
    }

    /**
     * Wyłączenie 2FA.
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('profile.edit')->with('status', '2FA wyłączone.');
    }

    /**
     * Pokazuje aktualne kody zapasowe 2FA.
     */
    public function showRecoveryCodes(Request $request)
    {
        $codes = json_decode(decrypt($request->user()->two_factor_recovery_codes), true);

        return view('profile.2fa.recovery-codes', [
            'codes' => $codes,
        ]);
    }

    /**
     * Generuje nowe kody zapasowe.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $codes = $this->twoFactorService->generateBackupCodes(5);
        $request->user()->two_factor_recovery_codes = encrypt(json_encode($codes));
        $request->user()->save();

        return back()->with('status', 'Wygenerowano nowe kody zapasowe.');
    }

}
