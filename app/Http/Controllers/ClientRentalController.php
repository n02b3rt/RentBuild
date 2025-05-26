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

        // Oblicz liczbę dni + cenę
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $days = $startDate->diffInDays($endDate) + 1;
        $dailyPrice = $equipment->finalPrice();
        $totalPrice = round($days * $dailyPrice, 2);

        if (!$equipment->isAvailable()) {
            return redirect()->back()->withErrors(['equipment_id' => 'Sprzęt nie jest dostępny do wypożyczenia.']);
        }

        Rental::create([
            'user_id' => Auth::id(),
            'equipment_id' => $equipment->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'notes' => null,
            'payment_reference' => null,
            'total_price' => $totalPrice, // zakładam, że masz tę kolumnę
        ]);

        return redirect()->route('client.rentals.index')->with('success', 'Zamówienie zostało złożone i oczekuje na zatwierdzenie.');
    }


    public function summary(Equipment $equipment)
    {
        return view('rentals.summary', compact('equipment'));
    }
}
