<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Obsługuje żądanie resetu hasła.
     */
    public function store(Request $request)
    {
        // Walidacja danych z formularza
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Próbujemy zresetować hasło
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            // Hasło zresetowane poprawnie
            return redirect()->route('login')->with('status', __($status));
        }

        // Błąd resetu hasła
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
