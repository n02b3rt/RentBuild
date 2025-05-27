<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        if (!$equipment->isAvailable()) {
            return redirect()->back()->withErrors(['equipment_id' => 'Sprzęt nie jest dostępny do wypożyczenia.']);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        $dailyPrice = $equipment->finalPrice();
        $equipmentCost = round($days * $dailyPrice, 2);

        $withOperator = $request->has('with_operator');
        $operatorCost = $withOperator ? $days * 350 : 0;

        $totalPrice = $equipmentCost + $operatorCost;

        session([
            'rental_data' => [
                'equipment_id' => $equipment->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'with_operator' => $withOperator,
                'notes' => $withOperator ? 'Z operatorem' : null,
                'total_price' => $totalPrice,
            ]
        ]);

        return redirect()->route('client.rentals.payment');
    }





    public function index()
    {
        $rentals = Rental::with('equipment')->where('user_id', Auth::id())->latest()->get();
        return view('rentals.index', compact('rentals'));
    }


    public function summary(Equipment $equipment)
    {
        return view('rentals.summary', compact('equipment'));
    }

    public function payment()
    {
        $data = session('rental_data');

        if (!$data) {
            return redirect()->route('client.rentals.index')->withErrors('Brak danych do wypożyczenia.');
        }

        $equipment = Equipment::findOrFail($data['equipment_id']);

        $rental = new Rental([
            'equipment_id' => $equipment->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'with_operator' => $data['with_operator'],
            'notes' => $data['notes'],
            'total_price' => $data['total_price'],
        ]);

        $rental->setRelation('equipment', $equipment);

        return view('rentals.payment', compact('rental'));
    }


    public function processPayment(Request $request)
    {
        $data = session('rental_data');

        if (!$data) {
            return redirect()->route('client.rentals.index')->withErrors('Brak danych do wypożyczenia.');
        }

        $equipment = Equipment::findOrFail($data['equipment_id']);

        if (!$equipment->isAvailable()) {
            return redirect()->route('client.rentals.index')->withErrors('Sprzęt nie jest już dostępny.');
        }

        $user = Auth::user();

        if ($user->account_balance < $data['total_price']) {
            return redirect()->route('client.rentals.topup')->with('not_enough', true);
        }

        $user->account_balance -= $data['total_price'];
        $user->save();

        $rental = Rental::create([
            'user_id' => $user->id,
            'equipment_id' => $equipment->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'oczekujace',
            'notes' => $data['notes'],
            'payment_reference' => 'fake_payment_token_' . uniqid(),
            'total_price' => $data['total_price'],
            'with_operator' => $data['with_operator'],
        ]);

        $equipment->availability = 'niedostepny';
        $equipment->save();

        session()->forget('rental_data');

        return redirect()->route('client.rentals.index')->with('success', 'Płatność została zaakceptowana, wypożyczenie aktywne.');
    }




}
