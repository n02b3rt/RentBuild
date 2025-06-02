<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use Carbon\Carbon;

class AdminRentalController extends Controller
{
    public function index(Request $request)
    {
        session(['previous_url' => $request->fullUrl()]);

        $status = $request->input('status', 'oczekujace');
        $query = Rental::query();

        if ($status !== 'wszystkie') {
            $query->where('status', $status);
        }

        $rentals = $query->paginate(15);

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

    public function create()
    {
        return view('admin.rentals.create');
    }

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
            'status' => 'required|in:oczekujace,nadchodzace,aktualne,zrealizowane,odrzucone,anulowane',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
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
}
