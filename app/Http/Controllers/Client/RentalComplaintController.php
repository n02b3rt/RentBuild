<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalComplaintController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $complaints = Rental::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'reklamacja')
                    ->orWhere('status', 'like', 'reklamacja_%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('client.rentals.complaints.index', compact('complaints'));
    }

    public function show(Rental $rental)
    {
        // Sprawdź, czy wypożyczenie należy do zalogowanego użytkownika
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.rentals.complaints.show', compact('rental'));
    }

    public function create(Rental $rental)
    {
        // Sprawdź, czy aktualny user jest właścicielem wypożyczenia
//        $this->authorize('view', $rental);

        // Sprawdź czy można zgłosić reklamację (np. status nie jest reklamacyjny)
        if ($rental->isComplaint()) {
            return redirect()->route('client.rentals.index')
                ->withErrors(['complaint' => 'Reklamacja dla tego wypożyczenia została już zgłoszona.']);
        }

        return view('client.rentals.complaint', compact('rental'));
    }

    public function store(Request $request, Rental $rental)
    {
        // $this->authorize('update', $rental);

        $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        if ($rental->isComplaint()) {
            return redirect()->route('client.rentals.index')
                ->withErrors(['complaint' => 'Reklamacja dla tego wypożyczenia została już zgłoszona.']);
        }

        $rental->submitComplaint($request->input('description'));

        // Log "maila" do użytkownika
        logger("MAIL do użytkownika ID {$rental->user_id} - Reklamacja zgłoszona",
            [
                'to' => $rental->user->email ?? 'brak email',
                'subject' => 'Potwierdzenie zgłoszenia reklamacji',
                'message' => "Dzień dobry,\n\nTwoja reklamacja dotycząca wypożyczenia nr {$rental->id} została zgłoszona.\nPostaramy się ją rozpatrzyć jak najszybciej.\n\nPozdrawiamy,\nZespół Serwisu"
            ]
        );

        return redirect()->route('client.rentals.index')
            ->with('success', 'Reklamacja została zgłoszona pomyślnie.');
    }

}
