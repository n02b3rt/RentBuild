<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Generuje nowy zaszyfrowany sekret (Base32).
     */
    public function generateEncryptedSecret(): string
    {
        $secret = $this->google2fa->generateSecretKey(); // zwraca base32
        return Crypt::encryptString($secret);
    }

    /**
     * Odszyfrowuje sekret do użycia.
     */
    public function decryptSecret(string $encryptedSecret): string
    {
        $secret = Crypt::decryptString($encryptedSecret);

        // 🛡️ Walidacja base32 (tylko znaki A-Z i 2-7)
        if (!preg_match('/^[A-Z2-7]+$/', $secret)) {
            throw new InvalidCharactersException("Odszyfrowany sekret zawiera nieprawidłowe znaki.");
        }

        return $secret;
    }

    /**
     * Generuje URI otpauth:// do użycia w aplikacji 2FA.
     */
    public function getOtpAuthUrl(string $companyName, string $userEmail, string $decryptedSecret): string
    {
        return $this->google2fa->getQRCodeUrl($companyName, $userEmail, $decryptedSecret);
    }

    /**
     * Zwraca inline SVG QR kod do wyświetlenia użytkownikowi.
     */
    public function getInlineQrCodeSvg(string $otpAuthUrl): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($otpAuthUrl);
    }

    /**
     * Weryfikuje 6-cyfrowy kod użytkownika.
     */
    public function verifyCode(string $decryptedSecret, string $code): bool
    {
        try {
            return $this->google2fa->verifyKey($decryptedSecret, $code);
        } catch (InvalidCharactersException $e) {
            return false;
        }
    }

    /**
     * Generuje kody zapasowe.
     */
    public function generateBackupCodes(int $count = 5): array
    {
        return collect(range(1, $count))
            ->map(fn () => bin2hex(random_bytes(4)))
            ->all();
    }

    /**
     * Pokazuje kody zapasowe.
     */
    public function showRecoveryCodes(Request $request)
    {
        $codes = json_decode(decrypt($request->user()->two_factor_recovery_codes), true);
        return view('profile.2fa.recovery-codes', ['codes' => $codes]);
    }

    /**
     * Regeneruje nowe kody zapasowe.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $codes = $this->twoFactorService->generateBackupCodes(5);
        $request->user()->two_factor_recovery_codes = encrypt(json_encode($codes));
        $request->user()->save();

        return back()->with('status', 'Wygenerowano nowe kody zapasowe.');
    }
}
