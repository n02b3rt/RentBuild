<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Rental;
use App\Models\Equipment;
use Carbon\Carbon;

class ClientRentalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $days  = $start->diffInDays($end) + 1;

        // ceny jednostkowe
        $dailyEq      = $equipment->finalPrice();
        $operatorRate = $equipment->operator_daily_rate;

        // koszty
        $equipmentCost = round($days * $dailyEq, 2);
        $operatorCost  = $request->has('with_operator')
            ? round($days * $operatorRate, 2)
            : 0;

        $total = round($equipmentCost + $operatorCost, 2);

        $notes = $request->input('notes', '');

        // zapisz wszystko w sesji
        session([
            'rental_data' => [
                'equipment_id'         => $equipment->id,
                'start_date'           => $start->toDateString(),
                'end_date'             => $end->toDateString(),
                'with_operator'        => $request->has('with_operator'),
                'days'                 => $days,
                'equipment_daily_rate' => $dailyEq,
                'equipment_cost'       => $equipmentCost,
                'operator_daily_rate'  => $operatorRate,
                'operator_cost'        => $operatorCost,
                'total_price'          => $total,
                'notes'                => $notes,
            ]
        ]);

        return redirect()->route('client.rentals.payment');
    }

    public function index()
    {
        $rentals = Rental::with('equipment')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('rentals.index', compact('rentals'));
    }

    public function summary(Equipment $equipment)
    {
        return view('rentals.summary', compact('equipment'));
    }

    public function payment()
    {
        $data = session('rental_data');

        if (! $data) {
            return redirect()
                ->route('client.rentals.index')
                ->withErrors('Brak danych do wypożyczenia.');
        }

        $equipment = Equipment::findOrFail($data['equipment_id']);

        $rental = new Rental([
            'equipment_id'  => $equipment->id,
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'with_operator' => $data['with_operator'],
            'notes'         => $data['notes'] ?? null,
            'total_price'   => $data['total_price'],
        ]);

        $rental->setRelation('equipment', $equipment);

        return view('rentals.payment', compact('rental'));
    }

    public function processPayment(Request $request)
    {
        $data = session('rental_data');

        if (! $data) {
            return redirect()
                ->route('client.rentals.index')
                ->withErrors('Brak danych do wypożyczenia.');
        }

        $equipment = Equipment::findOrFail($data['equipment_id']);

        if (! $equipment->isAvailable()) {
            return redirect()
                ->route('client.rentals.index')
                ->withErrors('Sprzęt nie jest już dostępny.');
        }

        $user = Auth::user();

        if ($user->account_balance < $data['total_price']) {
            return redirect()
                ->route('client.rentals.topup')
                ->with('not_enough', true);
        }

        $user->account_balance = round($user->account_balance - $data['total_price'], 2);
        $user->save();

        $rental = Rental::create([
            'user_id'           => $user->id,
            'equipment_id'      => $equipment->id,
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'],
            'status'            => 'oczekujace',
            'notes'             => $data['notes'] ?? null,
            'payment_reference' => 'fake_payment_token_' . uniqid(),
            'total_price'       => $data['total_price'],
            'with_operator'     => $data['with_operator'],
        ]);

        $equipment->availability = 'niedostepny';
        $equipment->save();

        session()->forget('rental_data');

        return redirect()
            ->route('client.rentals.index')
            ->with('success', 'Płatność została zaakceptowana, wypożyczenie oczekuje na akceptacje.');
    }

    public function cancel(Rental $rental)
    {
        $user = Auth::user();

        if ($rental->user_id !== $user->id) {
            abort(403, 'Brak dostępu do tego wypożyczenia.');
        }

        if (!in_array($rental->status, ['oczekujace', 'nadchodzace', 'aktualne'])) {
            return redirect()->back()->withErrors('Nie można anulować tego wypożyczenia.');
        }

        $refundAmount = 0;
        $now = Carbon::now();
        $start = Carbon::parse($rental->start_date);
        $end = Carbon::parse($rental->end_date);

        if ($rental->status === 'oczekujace') {
            $refundAmount = round($rental->total_price, 2);
        } elseif ($rental->status === 'nadchodzace') {
            if ($now->lt($start)) {
                // 80% zwrotu przed rozpoczęciem
                $refundAmount = round($rental->total_price * 0.8, 2);
            } else {
                // częściowy zwrot jeśli już rozpoczęte
                $usedDays = $start->diffInDays($now) + 1;
                $totalDays = $start->diffInDays($end) + 1;
                $dailyRate = $rental->total_price / $totalDays;
                $refundAmount = round($dailyRate * ($totalDays - $usedDays), 2);
            }
        } elseif ($rental->status === 'aktualne') {
            if ($now->between($start, $end)) {
                $usedDays = $start->diffInDays($now) + 1;
                $totalDays = $start->diffInDays($end) + 1;
                $dailyRate = $rental->total_price / $totalDays;
                $refundAmount = round($dailyRate * ($totalDays - $usedDays), 2);
            }
        }

        // Zmieniamy status wypożyczenia
        $rental->status = 'anulowane';
        $rental->save();

        // Zwracamy środki
        if ($refundAmount > 0) {
            $user->account_balance = round($user->account_balance + $refundAmount, 2);
            $user->save();
        }

        // Zmieniamy dostępność sprzętu
        $equipment = $rental->equipment;
        if ($equipment) {
            $equipment->availability = 'dostepny';
            $equipment->save();
        }

        return redirect()
            ->route('client.rentals.index')
            ->with('success', "Wypożyczenie zostało anulowane. Zwrot środków: " . number_format($refundAmount, 2) . " zł.");
    }

    /**
     * GET  /client/biwo/generate
     *    → generuje 6‐cyfrowy kod BIWO, zapisuje go w cache i loguje do laravel.log
     */
    public function generateBiwoCode(Request $request)
    {
        $userId = $request->user()->id;
        // Losowy 6‐cyfrowy ciąg (z wiodącymi zerami)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Zapisujemy w Cache pod kluczem "biwo_{user_id}", ważne 15 min
        $cacheKey = "biwo_{$userId}";
        Cache::put($cacheKey, $code, now()->addMinutes(15));

        // Logujemy w storage/logs/laravel.log
        Log::info("Użytkownik {$userId} wygenerował kod BIWO: {$code} (ważny 15 min)");

        // Wracamy z komunikatem do sesji
        return back()->with('status', 'Kod BIWO został wygenerowany – sprawdź logi serwera.');
    }

    /**
     * POST /client/biwo/pay
     *   → Weryfikuje kod BIWO, a następnie robi dokładnie to, co processPayment():
     *     * Sprawdza, czy są dane w sesji
     *     * Sprawdza dostępność sprzętu
     *     * Tworzy wypożyczenie, ustawia sprzęt na „niedostępny”
     *     * Czyści sesję, zwraca komunikat sukcesu
     */
    public function payWithBiwo(Request $request)
    {
        // 1) Sprawdź, czy w sesji są dane do wypożyczenia
        $data = session('rental_data');
        if (! $data) {
            return redirect()
                ->route('client.rentals.index')
                ->withErrors('Brak danych do wypożyczenia.');
        }

        // 2) WALIDACJA KODU BIWO
        $payload = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId   = Auth::id();
        $cacheKey = "biwo_{$userId}";
        $cachedCode = Cache::get($cacheKey);

        if (! $cachedCode || $cachedCode !== $payload['code']) {
            return back()->withErrors(['code' => 'Nieprawidłowy lub wygasły kod BIWO.']);
        }

        // 3) Usuwamy kod z cache (żeby nie można było użyć dwa razy)
        Cache::forget($cacheKey);

        // 4) Sprawdź dostępność sprzętu
        $equipment = Equipment::findOrFail($data['equipment_id']);
        if (! $equipment->isAvailable()) {
            return redirect()
                ->route('client.rentals.index')
                ->withErrors('Sprzęt nie jest już dostępny.');
        }

        // 5) Tworzymy nowe wypożyczenie (bez sprawdzania salda, bo „zapłacono BIWO”)
        $user = Auth::user();

        $rental = Rental::create([
            'user_id'           => $user->id,
            'equipment_id'      => $equipment->id,
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'],
            'with_operator'     => $data['with_operator'],
            'notes'             => $data['notes'] ?? null,
            // Ponieważ przez BIWO przyjęliśmy płatność, w payment_reference
            // możesz wstawić np. „biwo_{user_id}_{timestamp}”
            'payment_reference' => 'biwo_' . $user->id . '_' . now()->timestamp,
            'status'            => 'oczekujace',
            'total_price'       => $data['total_price'],
        ]);

        // 6) Zaktualizuj dostępność sprzętu
        $equipment->availability = 'niedostepny';
        $equipment->save();

        // 7) Usuń dane z sesji
        session()->forget('rental_data');

        // 8) Przekieruj z komunikatem o sukcesie
        return redirect()
            ->route('client.rentals.index')
            ->with('success', 'Płatność przez BIWO zakończona pomyślnie, wypożyczenie zostało utworzone i oczekuje na akceptację.');
    }

    public function end(Rental $rental)
    {
        if ($rental->status === 'zrealizowane') {
            return redirect()->route('client.rentals.index')
                ->with('info', 'To wypożyczenie jest już zakończone.');
        }

        $now = Carbon::now();
        $end = $rental->end_date;
        $user = $rental->user;
        $equipment = $rental->equipment;

        $rental->status = 'zrealizowane';
        $rental->save();

        if ($equipment) {
            $equipment->availability = 'dostepny';
            $equipment->number_of_rentals = ($equipment->number_of_rentals ?? 0) + 1;
            $equipment->save();
        }

        $penalty = 0;
        if (!$rental->with_operator && $now->gt($end)) {
            $lateDays = $end->diffInDays($now);
            $dailyPenaltyRate = $equipment->rental_price ?? 0;
            $penalty = round($dailyPenaltyRate * $lateDays, 2);

            $user->account_balance = round(($user->account_balance ?? 0) - $penalty, 2);
            $user->save();
        }

        $message = "Wypożyczenie zakończone.";

        if ($penalty > 0) {
            $message .= " Naliczono karę za niezwrócenie sprzętu w terminie: " . number_format($penalty, 2) . " zł.";
            return redirect()->route('client.rentals.index')->withErrors($message);
        } elseif ($rental->with_operator) {
            $message .= " Nie naliczono kary – sprzęt był wypożyczony z operatorem.";
        }

        return redirect()->route('client.rentals.index')->with('success', $message);
    }
}
