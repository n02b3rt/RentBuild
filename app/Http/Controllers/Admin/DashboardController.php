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

        // Obliczamy ogólne wskaźniki
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

        // Grupujemy po user_id i agregujemy:
        //    - liczbę wypożyczeń
        //    - sumę brutto (total_price)
        //    - sumę kosztów operatora
        //    - sumę netto (brutto - operator_cost)
        $userStats = [];

        foreach ($rentals as $rental) {
            if (! $rental->user) {
                continue;
            }
            $uid = $rental->user_id;

            // obliczamy koszt operatora dla tego wypożyczenia
            $start = Carbon::parse($rental->start_date);
            $end   = Carbon::parse($rental->end_date);
            $days  = $start->diffInDays($end) + 1;
            $opRate = optional($rental->equipment)->operator_rate ?? 0;
            $opCostRental = $rental->with_operator ? ($days * $opRate) : 0;

            if (! isset($userStats[$uid])) {
                $userStats[$uid] = [
                    'count'          => 0,
                    'brutto'         => 0.0,
                    'operator_cost'  => 0.0,
                    'netto'          => 0.0,
                    'first_name'     => $rental->user->first_name,
                    'last_name'      => $rental->user->last_name,
                ];
            }

            $userStats[$uid]['count']         += 1;
            $userStats[$uid]['brutto']        += $rental->total_price;
            $userStats[$uid]['operator_cost'] += $opCostRental;
            $userStats[$uid]['netto']         += ($rental->total_price - $opCostRental);
        }

        // Znajdujemy „top” użytkownika po sumie netto (lub brutto—tu używamy netto)
        if (! empty($userStats)) {
            // sortujemy malejąco po 'netto'
            uasort($userStats, fn($a, $b) => $b['netto'] <=> $a['netto']);
            $top = reset($userStats);

            $topUserName      = trim($top['first_name'] . ' ' . $top['last_name']);
            $topUserCount     = $top['count'];
            $topUserBrutto    = $top['brutto'];
            $topUserOpCost    = $top['operator_cost'];
            $topUserNetto     = $top['netto'];
        } else {
            $topUserName      = '–';
            $topUserCount     = 0;
            $topUserBrutto    = 0.0;
            $topUserOpCost    = 0.0;
            $topUserNetto     = 0.0;
        }

        // Budujemy kolekcję wierszy tabeli (ze sprzętem i użytkownikiem)
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
            'topUserName',
            'topUserCount',
            'topUserBrutto',
            'topUserOpCost',
            'topUserNetto',
            'rentalDetails',
            'sort',
            'direction'
        ));
    }
}
