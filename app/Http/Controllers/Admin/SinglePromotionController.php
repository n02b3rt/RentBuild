<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SinglePromotionController extends Controller
{
    public function index()
    {
        // 1. pobieramy DB time
        $dbNowRaw    = DB::selectOne('SELECT NOW() as now')->now;
        $currentDate = Carbon::parse($dbNowRaw);

        // 2. reszta bez zmian, ale korzysta już z $currentDate
        $promotions = Equipment::where('promotion_type', 'pojedyncza')->get();

        $promotionsWithStatus = $promotions->map(function ($promotion) use ($currentDate) {
            if ($promotion->start_datetime && $promotion->end_datetime) {
                $startDate = Carbon::parse($promotion->start_datetime);
                $endDate   = Carbon::parse($promotion->end_datetime);

                if ($currentDate->lt($startDate)) {
                    $status = 'Nadchodząca';
                } elseif ($currentDate->between($startDate, $endDate)) {
                    $status = 'Aktywna';
                } else {
                    $status = 'Zakończona';
                }
            } else {
                $status = 'Aktywna (bezterminowa)';
            }

            $promotion->status = $status;
            return $promotion;
        });

        return view('admin.promotions.single.index', compact('promotionsWithStatus'));
    }


    public function create()
    {
        $now = Carbon::now();

        $equipment = Equipment::where(function ($q) use ($now) {
            // brak promocji
            $q->whereNull('promotion_type')

                // lub promocja pojedyncza, ale nieaktywna
                ->orWhere(function ($q2) use ($now) {
                    $q2->where('promotion_type', 'pojedyncza')
                        ->where(function ($q3) use ($now) {
                            $q3->whereNull('start_datetime')
                                ->orWhere('end_datetime', '<', $now);
                        });
                })

                // lub promocja kategoria, ale nieaktywna
                ->orWhere(function ($q2) use ($now) {
                    $q2->where('promotion_type', 'kategoria')
                        ->where(function ($q3) use ($now) {
                            $q3->whereNull('start_datetime')
                                ->orWhere('end_datetime', '<', $now);
                        });
                });
        })->get();

        return view('admin.promotions.single.create', compact('equipment'));
    }



    public function store(Request $request)
    {
        \Log::info('Store promotion request data:', $request->all());

        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'discount' => 'required|integer|min:1|max:100',
            'has_dates' => 'nullable|boolean',
            'start_datetime' => 'required_if:has_dates,1|date',
            'end_datetime' => 'required_if:has_dates,1|date|after_or_equal:start_datetime',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        $equipment->promotion_type = 'pojedyncza';
        $equipment->discount = $request->discount;

        if ($request->has('has_dates') && $request->has_dates) {
            $equipment->start_datetime = $request->start_datetime;
            $equipment->end_datetime = $request->end_datetime;
        } else {
            $equipment->start_datetime = null;
            $equipment->end_datetime = null;
            \Log::info('Before save5:');
        }

        $saved = $equipment->save();

        return redirect()->route('admin.promotions.single.index')->with('success', 'Promocja została dodana.');
    }


    public function edit($id)
    {
        $promotion = Equipment::findOrFail($id);

        if ($promotion->promotion_type !== 'pojedyncza') {
            abort(404);
        }

        // Widok: resources/views/admin/promotions/single/edit.blade.php
        return view('admin.promotions.single.edit', compact('promotion'));
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update promotion request data:', $request->all());

        $request->validate([
            'discount' => 'required|integer|min:1|max:100',
            'has_dates' => 'nullable|boolean',
            'start_datetime' => 'required_if:has_dates,1|date',
            'end_datetime' => 'required_if:has_dates,1|date|after_or_equal:start_datetime',
        ]);

        $promotion = Equipment::findOrFail($id);

        if ($promotion->promotion_type !== 'pojedyncza') {
            abort(404);
        }

        $promotion->discount = $request->discount;

        if ($request->has('has_dates') && $request->has_dates) {
            $promotion->start_datetime = $request->start_datetime;
            $promotion->end_datetime = $request->end_datetime;
        } else {
            $promotion->start_datetime = null;
            $promotion->end_datetime = null;
        }

        $promotion->save();

        \Log::info('Promotion updated for equipment ID: ' . $promotion->id);

        return redirect()->route('admin.promotions.single.index')
            ->with('success', 'Promocja została pomyślnie zaktualizowana.');
    }


    public function destroy($id)
    {
        $promotion = Equipment::findOrFail($id);

        if ($promotion->promotion_type !== 'pojedyncza') {
            abort(404);
        }

        $promotion->promotion_type = null;
        $promotion->discount = null;
        $promotion->start_datetime = null;
        $promotion->end_datetime = null;
        $promotion->save();

        return redirect()->route('admin.promotions.single.index')->with('success', 'Promocja została usunięta.');
    }
}
