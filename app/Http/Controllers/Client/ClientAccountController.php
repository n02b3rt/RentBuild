<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ClientAccountController extends Controller
{
    public function handleTopUp(Request $request)
    {
        if ($request->isMethod('get')) {
            // Wyświetlamy formularz
            return view('client.topup');
        }

        // POST: walidacja pola amount oraz przykładowych danych karty
        $data = $request->validate([
            'amount'      => ['required', 'numeric', 'min:1'],
            'card_number' => ['required', 'digits_between:13,19'],
            'card_expiry' => ['required', 'regex:/^\d{2}\/\d{2}$/'], // format MM/YY
            'card_cvc'    => ['required', 'digits:3'],
        ]);

        // Generujemy unikalny token transakcji (20 losowych znaków)
        $token = Str::upper(Str::random(20));
        $cacheKey = "topup_{$token}";

        // Zapisujemy do Cache: klucz=> ['user_id', 'amount'], ważność 60 minut
        Cache::put($cacheKey, [
            'user_id' => $request->user()->id,
            'amount'  => $data['amount'],
        ], now()->addMinutes(60));

        // Generujemy URL potwierdzający (np. https://twojadomena.pl/client/topup/confirm/ABCDEF123…)
        $confirmUrl = route('client.topup.confirm', ['token' => $token]);

        // Logujemy w pliku laravel.log
        Log::info("Użytkownik {$request->user()->id} wygenerował prośbę doładowania (PLN {$data['amount']}). Link potwierdzenia: {$confirmUrl}");

        // Wracamy do tego samego widoku z komunikatem
        return back()->with('status', 'Proces doładowania rozpoczęty – sprawdź logi serwera, aby znaleźć link potwierdzający.');
    }

    /**
     * GET /client/topup/confirm/{token}
     *   → pobiera dane z cache i finalizuje doładowanie konta
     */
    public function confirmTopUp($token)
    {
        $cacheKey = "topup_{$token}";
        $payload  = Cache::get($cacheKey);

        if (! $payload) {
            return redirect()->route('client.topup.form')
                ->withErrors(['token' => 'Token jest nieprawidłowy lub wygasł.']);
        }

        $user = User::find($payload['user_id']);
        if (! $user) {
            // Jeżeli nie ma użytkownika, usuwamy cache i zgłaszamy błąd
            Cache::forget($cacheKey);
            return redirect()->route('client.topup.form')
                ->withErrors(['user' => 'Nie udało się znaleźć użytkownika.']);
        }

        // Zwiększamy saldo konta
        $user->account_balance += $payload['amount'];
        $user->save();

        // Kasujemy cache, żeby token nie działał ponownie
        Cache::forget($cacheKey);

        // Przekierowujemy z komunikatem o sukcesie
        return redirect()->route('client.topup.form')
            ->with('status', "Saldo zostało doładowane o {$payload['amount']} PLN. Transakcja potwierdzona.");
    }
}
