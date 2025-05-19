<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;  // Zakładam, że masz model Rental
use Illuminate\Support\Facades\Auth;

class ClientRentalController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Przykładowe pobranie wypożyczeń podzielonych na statusy:
        $currentRentals = Rental::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        $pastRentals = Rental::where('user_id', $userId)
            ->where('status', 'past')
            ->get();

        $futureRentals = Rental::where('user_id', $userId)
            ->where('status', 'future')
            ->get();

        // Przekazujemy zmienne do widoku
        return view('client.rentals.index', compact('currentRentals', 'pastRentals', 'futureRentals'));
    }

    // Dodaj inne metody, np. store, show, return, cancel, jeśli masz je w routes
}

