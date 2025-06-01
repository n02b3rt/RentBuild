<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorIsVerified
{
    /**
     * Jeśli użytkownik włączył 2FA, ale nie przeszedł weryfikacji w tej sesji,
     * przekieruj na stronę logowania 2FA.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // jeżeli ma włączone 2FA i nie przeszedł jej teraz
            if ($user->two_factor_enabled && ! $request->session()->has('2fa_passed')) {
                // zapisz id użytkownika, jeśli nie było już ustawione
                if (! $request->session()->has('2fa:user:id')) {
                    $request->session()->put('2fa:user:id', $user->id);
                }
                return redirect()->route('2fa.login');
            }
        }

        return $next($request);
    }
}
