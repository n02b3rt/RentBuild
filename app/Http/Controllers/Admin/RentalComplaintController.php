<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalComplaintController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'updated_at');
        $sortDirection = $request->get('direction', 'desc');

        // Bezpieczne ograniczenie pól do sortowania
        $allowedSorts = ['id', 'status', 'updated_at', 'user'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'updated_at';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = Rental::where('status', 'reklamacja')
            ->orWhere('status', 'like', 'reklamacja_%');

        if ($sortField === 'user') {
            $query = $query->join('users', 'rentals.user_id', '=', 'users.id')
                ->orderBy('users.first_name', $sortDirection)
                ->select('rentals.*');
        } else {
            $query = $query->orderBy($sortField, $sortDirection);
        }

        $complaints = $query->paginate(20)->appends([
            'sort' => $sortField,
            'direction' => $sortDirection,
        ]);

        return view('admin.rentals.complaints.index', compact('complaints'));
    }


    public function show(Rental $rental)
    {
        if (!$rental->isComplaint()) {
            abort(404, 'To wypożyczenie nie ma reklamacji.');
        }

        return view('admin.rentals.complaints.show', compact('rental'));
    }

    public function resolve(Request $request, Rental $rental)
    {
        $request->validate([
            'decision' => 'required|in:weryfikacja,odrzucono,przyjeto',
        ]);

        if (!$rental->isComplaint()) {
            return redirect()->route('admin.rentals.complaints.index')
                ->withErrors(['error' => 'Ta reklamacja nie jest aktywna.']);
        }

        $decision = $request->input('decision');

        if ($decision === 'przyjeto') {
            $rental->acceptComplaint();
        } else {
            $rental->setComplaintStatus($decision);
            $rental->save();
        }

        // Logowanie maila do klienta
        $status = $rental->status;
        $message = "MAIL do użytkownika ID {$rental->user_id} - Zmiana statusu reklamacji wypożyczenia ID {$rental->id}.\n"
            . "Nowy status: {$status}\n\n"
            . "Dzień dobry,\nTwoja reklamacja została zmieniona na status: {$status}.\n"
            . "W razie pytań prosimy o kontakt.\n\nPozdrawiamy,\nZespół Serwisu";

        logger($message);

        return redirect()->route('admin.rentals.complaints.show', $rental)
            ->with('success', 'Reklamacja została rozpatrzona.');
    }

}
