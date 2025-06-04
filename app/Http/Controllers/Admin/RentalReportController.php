<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalReportController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->subDays(30);
        $now       = Carbon::now();

        $rentals = Rental::where('created_at', '>=', $startDate)
            ->with(['user', 'equipment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $rentals->sum('total_price');
        $totalRentals = $rentals->count();
        $averageRental = $totalRentals ? $totalRevenue / $totalRentals : 0;

        $dailySales = $rentals->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
            ->map(fn($group) => [
                'date'  => $group->first()->created_at->format('Y-m-d'),
                'total' => $group->sum('total_price'),
                'count' => $group->count(),
            ])
            ->values();

        $averageDailyRevenue = $dailySales->avg('total');
        $maxDailyRevenue     = $dailySales->max('total');
        $maxDailyRevenueDate = $dailySales->first(fn($d) => $d['total'] === $maxDailyRevenue)['date'] ?? null;

        $averageDailyCount = $dailySales->avg('count');
        $maxDailyCount     = $dailySales->max('count');
        $maxDailyCountDate = $dailySales->first(fn($d) => $d['count'] === $maxDailyCount)['date'] ?? null;

        $topEquipment = $rentals->groupBy('equipment_id')
            ->map(fn($group) => [
                'equipment' => optional($group->first()->equipment)->name ?? 'Nieznany',
                'count'     => $group->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $topUser = $rentals->groupBy('user_id')
            ->map(fn($group) => [
                'user'  => optional($group->first()->user)->name ?? 'Nieznany',
                'total' => $group->sum('total_price'),
            ])
            ->sortByDesc('total')
            ->first();

        $complaintStats = [
            'reklamacyjne' => $rentals->filter(fn($r) => $r->isComplaint())->count(),
            'pozostałe'    => $rentals->filter(fn($r) => !$r->isComplaint())->count(),
        ];
        $totalComplaints      = $complaintStats['reklamacyjne'];
        $complaintPercentage  = $totalRentals ? round($totalComplaints / $totalRentals * 100, 1) : 0;

        $statusDistribution = $rentals->groupBy('status')
            ->map->count();

        $complaintRentals = $rentals->filter(fn($r) => str_starts_with($r->status, 'reklamacja'));
        $totalComplaintLosses = $complaintRentals->sum('total_price');
        $topComplainedEquipment = $complaintRentals->groupBy('equipment_id')
            ->map(fn($group) => [
                'equipment' => optional($group->first()->equipment)->name ?? 'Nieznany',
                'count'     => $group->count(),
                'loss'      => $group->sum('total_price'),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $complaintChartSummary = "{$totalComplaints} reklamacji ({$complaintPercentage}%)";

        $mostCommonStatus       = $statusDistribution->sortDesc()->keys()->first();
        $mostCommonStatusCount  = $statusDistribution->sortDesc()->first();
        $statusTotal            = $rentals->count();
        $mostCommonStatusPct    = $statusTotal
            ? round($mostCommonStatusCount / $statusTotal * 100, 1)
            : 0;
        $statusChartSummary     = "{$mostCommonStatus}: {$mostCommonStatusCount} ({$mostCommonStatusPct}%)";

        return view('admin.raports.index', compact(
            'rentals',
            'totalRevenue',
            'totalRentals',
            'averageRental',
            'dailySales',
            'averageDailyRevenue',
            'maxDailyRevenue',
            'maxDailyRevenueDate',
            'averageDailyCount',
            'maxDailyCount',
            'maxDailyCountDate',
            'topEquipment',
            'topUser',
            'complaintStats',
            'complaintChartSummary',
            'statusDistribution',
            'statusChartSummary',
            'totalComplaintLosses',
            'topComplainedEquipment'
        ));
    }
}
