<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Rental;
use Carbon\Carbon;

class AdminRentalController extends Controller
{
    // -------------------------------------------------------------------------
    // 1. Lista wypożyczeń (bez zmian)
    public function index(Request $request)
    {
        session(['previous_url' => $request->fullUrl()]);

        $status = $request->input('status', 'oczekujace');
        $query = Rental::query();

        if ($status !== 'wszystkie') {
            $query->where('status', $status);
        }

        $rentals = $query->paginate(15)->appends(['status' => $status]);

        return view('admin.rentals.list.index', compact('rentals'));
    }

    public function show(Rental $rental)
    {
        return view('admin.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        return view('admin.rentals.edit', compact('rental'));
    }

    // -------------------------------------------------------------------------
    // 2. Tworzenie nowego wypożyczenia – krok 0 (opcjonalny): lista dostępnego sprzętu
    public function create()
    {
        $equipments = Equipment::where('availability', 'dostepny')->get();
        return view('admin.rentals.create', compact('equipments'));
    }

    // -------------------------------------------------------------------------
    // 3. Zatwierdzanie, odrzucanie, anulowanie, update, refund (bez zmian)
    public function approve($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'oczekujace') {
            return back()->with('error', 'Tylko oczekujące wypożyczenia mogą zostać zatwierdzone.');
        }

        $rental->status = 'nadchodzace';
        $rental->save();

        return redirect(session('previous_url', route('admin.rentals.list.index')))
            ->with('success', 'Wypożyczenie zostało zatwierdzone (nadchodzące).');
    }

    public function reject(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->status !== 'oczekujace') {
            return back()->with('error', 'Tylko oczekujące wypożyczenia mogą zostać odrzucone.');
        }

        $rental->status = 'odrzucone';
        $rental->notes = $request->input('rejection_note', null);
        $rental->save();

        $equipment = $rental->equipment;
        if ($equipment) {
            $equipment->availability = 'dostepny';
            $equipment->save();
        }

        $this->refundClient($rental, $rental->total_price);

        return redirect(session('previous_url', route('admin.rentals.list.index')))
            ->with('success', 'Wypożyczenie zostało odrzucone, klient otrzymał zwrot pieniędzy.');
    }

    public function cancel($id)
    {
        $rental = Rental::findOrFail($id);

        if (!in_array($rental->status, ['nadchodzace', 'aktualne'])) {
            return back()->with('error', 'Tylko nadchodzące lub aktualne wypożyczenia mogą zostać anulowane.');
        }

        $now = Carbon::now();
        $start = Carbon::parse($rental->start_date);
        $end = Carbon::parse($rental->end_date);
        $refundAmount = 0;

        if ($rental->status === 'nadchodzace') {
            if ($now->lt($start)) {
                $refundAmount = round($rental->total_price * 0.8, 2);
            } else {
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

        $rental->status = 'anulowane';
        $rental->save();

        if ($refundAmount > 0) {
            $this->refundClient($rental, $refundAmount);
        }

        $equipment = $rental->equipment;
        if ($equipment) {
            $equipment->availability = 'dostepny';
            $equipment->save();
        }

        return redirect(session('previous_url', route('admin.rentals.list.index')))
            ->with('success', "Wypożyczenie zostało anulowane. Zwrot dla klienta: " . number_format($refundAmount, 2) . " zł.");
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'status'      => 'required|in:oczekujace,nadchodzace,aktualne,zrealizowane,odrzucone,anulowane',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $rental->update($validated);

        return redirect(session('previous_url', route('admin.rentals.list.index')))
            ->with('success', 'Wypożyczenie zostało zaktualizowane.');
    }

    protected function refundClient(Rental $rental, float $amount)
    {
        if ($amount <= 0) {
            return;
        }

        $user = $rental->user;
        if (!$user) {
            return;
        }

        $user->account_balance = round($user->account_balance + $amount, 2);
        $user->save();
    }

    // -------------------------------------------------------------------------
    // 4. Multi-step tworzenie – krok 1: wybór użytkownika
    public function createStep1(Request $request)
    {
        $search = $request->input('search');

        $usersQuery = User::query();
        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(10);
        return view('admin.rentals.create.step1', compact('users'));
    }

    public function postSelectUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        return redirect()->route('admin.rentals.create.step2', [
            'user' => $request->input('user_id')
        ]);
    }

    // -------------------------------------------------------------------------
    // 5. Multi-step tworzenie – krok 2: wybór sprzętu
    public function createStep2(Request $request)
    {
        $request->validate([
            'user' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->query('user'));
        $equipments = Equipment::where('availability', 'dostepny')->get();
        return view('admin.rentals.create.step2', compact('user', 'equipments'));
    }

    public function postSelectEquipment(Request $request)
    {
        $validated = $request->validate([
            'user_id'            => 'required|integer|exists:users,id',
            'selected_equipment' => 'required|integer|exists:equipment,id',
        ]);

        return redirect()->route('admin.rentals.create.summary', [
            'user'      => $validated['user_id'],
            'equipment' => $validated['selected_equipment'],
        ]);
    }

    // -------------------------------------------------------------------------
    // 6. Multi-step tworzenie – krok 3: podsumowanie
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'user'      => 'required|integer|exists:users,id',
            'equipment' => 'required|integer|exists:equipment,id',
        ]);

        $user      = User::findOrFail($validated['user']);
        $equipment = Equipment::findOrFail($validated['equipment']);

        // Jeśli sprzęt nie jest globalnie dostępny → od razu błąd
        if ($equipment->availability !== 'dostepny') {
            return redirect()->route('admin.rentals.create.step1')
                ->with('error', 'Wybrany sprzęt jest już niedostępny.');
        }

        session([
            'rental_data.user_id'      => $validated['user'],
            'rental_data.equipment_id' => $validated['equipment'],
        ]);

        return view('admin.rentals.create.summary', compact('user', 'equipment'));
    }

    // -------------------------------------------------------------------------
    // 7. Multi-step tworzenie – krok 4: płatność (POST z summary) – tu metoda payment()
    public function payment(Request $request)
    {
        $rentalData = session('rental_data', []);

        if (empty($rentalData['user_id']) || empty($rentalData['equipment_id'])) {
            return redirect()->route('admin.rentals.create.step1')
                ->with('error', 'Brak danych użytkownika lub sprzętu w sesji.');
        }

        $validated = $request->validate([
            'start_date'    => 'required|date|after_or_equal:' . now()->toDateString(),
            'end_date'      => 'required|date|after_or_equal:start_date',
            'with_operator' => 'nullable|boolean',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $equipment = Equipment::findOrFail($rentalData['equipment_id']);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date']);
        $days      = $startDate->diffInDays($endDate) + 1;


        // Checkbox – domyślnie false, jeśli nie zaznaczono
        $needsOperator = $validated['with_operator'] ?? false;

        // Obliczenie ceny dziennej (z promocją, jeśli jest aktywna)
        $dailyPrice   = $equipment->isPromotionActive()
            ? $equipment->finalPrice()
            : $equipment->rental_price;
        $operatorRate = $equipment->operator_daily_rate ?? 0;

        $baseCost     = $days * $dailyPrice;
        $operatorCost = $needsOperator
            ? ($days * $operatorRate)
            : 0;
        $totalPrice   = round($baseCost + $operatorCost, 2);

        // Zapisujemy wszystkie dane do sesji
        session([
            'rental_data.start_date'    => $validated['start_date'],
            'rental_data.end_date'      => $validated['end_date'],
            'rental_data.with_operator' => $needsOperator,
            'rental_data.notes'         => $validated['notes'] ?? null,
            'rental_data.total_price'   => $totalPrice,
        ]);

        return view('admin.rentals.create.payment', [
            'totalPrice' => $totalPrice,
        ]);
    }

    // -------------------------------------------------------------------------
    // 8. Multi-step tworzenie – krok 5: finalize (POST z payment)
    public function finalize()
    {
        $rentalData = session('rental_data', []);

        if (
            empty($rentalData['user_id'])      ||
            empty($rentalData['equipment_id']) ||
            empty($rentalData['start_date'])   ||
            empty($rentalData['end_date'])     ||
            !isset($rentalData['total_price'])
        ) {
            return redirect()->route('admin.rentals.create.step1')
                ->with('error', 'Brak kompletu danych do finalizacji wypożyczenia.');
        }

        $equipment = Equipment::findOrFail($rentalData['equipment_id']);
        if ($equipment->availability !== 'dostepny') {
            return redirect()->route('admin.rentals.create.step2')
                ->with('error', 'Wybrany sprzęt jest już niedostępny.');
        }

        $startDate = Carbon::parse($rentalData['start_date']);
        $endDate   = Carbon::parse($rentalData['end_date']);

        Rental::create([
            'user_id'       => $rentalData['user_id'],
            'equipment_id'  => $rentalData['equipment_id'],
            'start_date'    => $rentalData['start_date'],
            'end_date'      => $rentalData['end_date'],
            'total_price'   => $rentalData['total_price'],
            'with_operator' => $rentalData['with_operator'] ?? false,
            'notes'         => $rentalData['notes'] ?? null,
            'status'        => 'nadchodzace',
        ]);

        $equipment->availability = 'niedostepny';
        $equipment->save();

        session()->forget('rental_data');

        return redirect()->route('admin.rentals.list.index')
            ->with('success', 'Wypożyczenie zostało utworzone a płatność zatwierdzona');
    }

    public function payWithBiwo(Request $request)
    {

    }
}
