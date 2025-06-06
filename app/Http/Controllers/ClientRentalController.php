<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rental;
use App\Models\Equipment;
use Carbon\Carbon;
use App\Http\Controllers\LoyalCustomerPromotionController;


class ClientRentalController extends Controller
{
    private function getLoyalDiscount(int $currentRentalsCount): int
    {
        // 1) Pierwsze wypożyczenie (rentals_count == 0) → 20%
        if ($currentRentalsCount === 0) {
            return 20;
        }
        // 2) Co 20-te wypożyczenie → 50%
        if ((($currentRentalsCount + 1) % 20) === 0) {
            return 50;
        }
        // 3) Co 5-te (ale nie 20-te) → 25%
        if ((($currentRentalsCount + 1) % 5) === 0) {
            return 25;
        }
        // 4) Inne przypadki → brak rabatu
        return 0;
    }

    public function store(Request $request)
    {
        // Walidacja pól
        $request->validate([
            'equipment_id'      => 'required|exists:equipment,id',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'with_operator'     => 'sometimes|boolean',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        // Obliczamy dni wypożyczenia
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $days  = $start->diffInDays($end) + 1;

        // Stawki
        $dailyEq      = $equipment->finalPrice();
        $operatorRate = $equipment->operator_daily_rate;

        // Surowe koszty
        $equipmentCost = round($days * $dailyEq, 2);
        $operatorCost  = $request->has('with_operator')
            ? round($days * $operatorRate, 2)
            : 0;

        $rawTotal = round($equipmentCost + $operatorCost, 2);

        // 1) Pobieramy liczbę dotychczasowych wypożyczeń użytkownika
        $user = Auth::user();
        $currentRentalsCount = $user->rentals_count;

        // 2) Wyliczamy procent rabatu lojalnościowego
        $discountPercent = $this->getLoyalDiscount($currentRentalsCount);

        // 3) Wyliczamy kwotę rabatu i kwotę po rabacie
        $discountAmount  = round($rawTotal * ($discountPercent / 100), 2);
        $discountedTotal = round($rawTotal - $discountAmount, 2);

        // Notatki (opcjonalnie)
        $notes = $request->has('with_operator')
            ? 'Wypożyczenie z operatorem'
            : null;

        // Zapisujemy do sesji wszystkie niezbędne dane
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
                // Tym razem 'total_price' = wartość PO rabacie:
                'total_price'          => $discountedTotal,
                'discount_percent'     => $discountPercent,
                'discount_amount'      => $discountAmount,
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

        // Ładujemy Equipment i „tymczasowy” Rental, by mieć relację equipment itp.
        $equipment = Equipment::findOrFail($data['equipment_id']);
        $rental = new Rental([
            'equipment_id'  => $equipment->id,
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'with_operator' => $data['with_operator'],
            'notes'         => $data['notes'] ?? null,
            // Ta wartość jest już price po rabacie:
            'total_price'   => $data['total_price'],
        ]);
        $rental->setRelation('equipment', $equipment);

        // Pobieramy z sesji to, co zapisaliśmy wcześniej:
        $totalPrice      = $data['total_price'];         // kwota PO rabacie
        $discountPercent = $data['discount_percent']     ?? 0;
        $discountAmount  = $data['discount_amount']      ?? 0;

        return view('rentals.payment', compact(
            'rental',
            'totalPrice',
            'discountPercent',
            'discountAmount'
        ));
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

        // 1) Pobieramy z requesta ostateczną kwotę do zapłaty (już po rabacie)
        //    Tę samą, którą zapisaliśmy w sesji jako 'total_price'
        $finalPrice = $request->input('total_price');

        // 2) Sprawdzamy saldo
        if ($user->account_balance < $finalPrice) {
            return redirect()
                ->route('client.rentals.payment')
                ->withErrors('Brak wystarczających środków na koncie.');
        }

        // 3) Odejmujemy od salda
        $user->account_balance = round($user->account_balance - $finalPrice, 2);
        $user->save();

        // 4) Tworzymy Rental z zachowaniem zapłaty po rabacie
        $rental = Rental::create([
            'user_id'           => $user->id,
            'equipment_id'      => $equipment->id,
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'],
            'status'            => 'oczekujace',
            'notes'             => $data['notes'] ?? null,
            'payment_reference' => 'fake_payment_token_' . uniqid(),
            'total_price'       => $finalPrice,
            'with_operator'     => $data['with_operator'],
            // Jeśli chcesz, możesz dodać:
            // 'discount_percent' => $data['discount_percent'],
            // 'discount_amount'  => $data['discount_amount'],
        ]);

        $equipment->availability = 'niedostepny';
        $equipment->save();

        // 5) Zapomnij dane sesji
        session()->forget('rental_data');

        return redirect()
            ->route('client.rentals.index')
            ->with('success', 'Płatność została zaakceptowana, wypożyczenie oczekuje na akceptację.');
    }

    public function cancel(Rental $rental)
    {
        $user = Auth::user();

        if ($rental->user_id !== $user->id) {
            abort(403, 'Brak dostępu do tego wypożyczenia.');
        }

        if ($rental->status === 'zrealizowane' || $rental->end_date && $rental->end_date->isPast()) {
            return redirect()->back()->withErrors('Nie można anulować zakończonego wypożyczenia.');
        }

        $refundAmount = 0;
        $status = $rental->status;
        $now = Carbon::now();
        $start = Carbon::parse($rental->start_date);
        $end = Carbon::parse($rental->end_date);

        if ($status === 'oczekujace') {
            $refundAmount = round($rental->total_price, 2);
        } else {
            if ($now->lt($start)) {
                $refundAmount = round($rental->total_price * 0.8, 2);
            } elseif ($now->between($start, $end)) {
                $usedDays = $start->diffInDays($now) + 1;
                $totalDays = $start->diffInDays($end) + 1;
                $dailyRate = $rental->total_price / $totalDays;
                $refundAmount = round($dailyRate * ($totalDays - $usedDays), 2);
            }
        }

        $user->account_balance = round($user->account_balance + $refundAmount, 2);
        $user->save();

        $rental->status = 'anulowane';
        $rental->save();

        $equipment = $rental->equipment;
        if ($equipment) {
            $equipment->availability = 'dostepny';
            $equipment->save();
        }

        return redirect()->route('client.rentals.index')
            ->with('success', "Wypożyczenie anulowane. Zwrot: " . number_format($refundAmount, 2) . " zł.");
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

    public function payWithBiwo(Request $request)
    {

    }
}
