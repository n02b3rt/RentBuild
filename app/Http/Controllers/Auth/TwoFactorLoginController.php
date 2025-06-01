<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\TwoFactorService;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;

class TwoFactorLoginController extends Controller
{
    /**
     * Formularz do wpisania kodu 2FA
     */
    public function showLoginForm(Request $request)
    {
        if (! $request->session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa');
    }

    /**
     * Weryfikacja kodu 2FA lub kodu zapasowego
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'code'          => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $userId = $request->session()->get('2fa:user:id');
        $user = User::findOrFail($userId);

        $valid = false;

        try {
            if ($request->filled('recovery_code')) {
                $valid = $user->validateRecoveryCode($request->recovery_code);
            } elseif ($request->filled('code')) {
                $twoFactorService = app(TwoFactorService::class);
                $secret = $twoFactorService->decryptSecret($user->two_factor_secret);
                $valid = $twoFactorService->verifyCode($secret, $request->code);

                if ($valid) {
                    $user->resetFailedAttempts();
                } else {
                    $user->incrementFailedAttempts();
                }
            }
        } catch (InvalidCharactersException $e) {
            Log::error('Nieprawidłowy sekret 2FA', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->withErrors(['code' => 'Wystąpił problem z 2FA. Spróbuj ponownie.']);
        } catch (\Exception $e) {
            Log::error('Błąd 2FA', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->withErrors(['code' => 'Błąd weryfikacji kodu.']);
        }

        if (! $valid) {
            if ($user->two_factor_failed_attempts >= 20) {
                Log::warning('Zablokowano logowanie 2FA z powodu zbyt wielu prób', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                return back()->withErrors('Za dużo prób. Spróbuj później.');
            }

            return back()->withErrors(['code' => 'Niepoprawny kod.']);
        }

        $request->session()->put('2fa_passed', true);
        $request->session()->forget('2fa:user:id');

        Auth::login($user);

        return redirect()->intended('/');
    }
}
