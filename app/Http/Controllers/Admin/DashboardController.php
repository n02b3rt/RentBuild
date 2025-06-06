<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Zakres: ostatnie 30 dni
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $now       = Carbon::now()->endOfDay();

        // Pobieramy wypożyczenia z ostatnich 30 dni wraz z relacjami 'equipment' i 'user'
        $rentals = Rental::with(['equipment', 'user'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue  = $rentals->sum('total_price');
        $totalRentals  = $rentals->count();
        $averageRental = $totalRentals ? round($totalRevenue / $totalRentals, 2) : 0;

        // Obliczamy koszt operatorów (wydatki)
        $operatorCost = $rentals
            ->filter(fn($r) => $r->with_operator)
            ->reduce(function ($carry, Rental $rental) {
                $start = Carbon::parse($rental->start_date);
                $end   = Carbon::parse($rental->end_date);
                $days  = $start->diffInDays($end) + 1;
                $rate  = optional($rental->equipment)->operator_rate ?? 0;
                return $carry + ($rate * $days);
            }, 0);

        // Dane do wykresu dziennych przychodów
        $dailySales = $rentals
            ->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
            ->map(fn($group) => [
                'date'  => $group->first()->created_at->format('Y-m-d'),
                'total' => $group->sum('total_price'),
                'count' => $group->count(),
            ])
            ->sortBy('date')
            ->values();

        // Budujemy kolekcję wierszy tabeli
        $allRows = $rentals->map(function (Rental $rental) {
            $start = Carbon::parse($rental->start_date);
            $end   = Carbon::parse($rental->end_date);
            $days  = $start->diffInDays($end) + 1;
            $opCost = $rental->with_operator
                ? ($days * (optional($rental->equipment)->operator_rate ?? 0))
                : 0;

            $userName = optional($rental->user)->first_name . ' ' . optional($rental->user)->last_name;

            return [
                'id'              => $rental->id,
                'date'            => $rental->created_at->format('Y-m-d'),
                'equipment_name'  => optional($rental->equipment)->name ?? '–',
                'user_name'       => trim($userName) !== '' ? $userName : '–',
                'brutto'          => $rental->total_price,
                'operator_cost'   => $opCost,
                'netto'           => $rental->total_price - $opCost,
            ];
        });

        // Sortowanie według GET: sort i direction
        $sort      = $request->get('sort', 'date');
        $direction = $request->get('direction', 'asc');
        $allowed   = ['date', 'equipment_name', 'user_name', 'brutto', 'operator_cost', 'netto'];
        if (!in_array($sort, $allowed)) {
            $sort = 'date';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $sorted = $allRows->sortBy(
            fn($row) => $row[$sort],
            SORT_REGULAR,
            $direction === 'desc'
        )->values();

        // Paginacja
        $perPage      = 10;
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $rentalDetails = new LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalRentals',
            'averageRental',
            'operatorCost',
            'dailySales',
            'startDate',
            'now',
            'rentalDetails',
            'sort',
            'direction'
        ));
    }
}
