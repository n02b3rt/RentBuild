<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;


class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Get max price for slider range
        $maxPrice = Equipment::max('rental_price');

        // Get distinct categories for filter
        $categories = Equipment::select('category')->distinct()->pluck('category');

        // Filters
        if ($request->filled('min_price')) {
            $query->where('rental_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('rental_price', '<=', $request->max_price);
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        if ($request->filled('technical_state')) {
            $query->where('technical_state', $request->technical_state);
        }

        if ($request->filled('discount')) {
            $now = Carbon::now();

            $query->whereNotNull('discount')
                ->where(function ($q) use ($now) {
                    $q->where(function ($q2) use ($now) {
                        // Typ "kategoria": promocja aktywna tylko jeśli daty są w zakresie
                        $q2->where('promotion_type', 'kategoria')
                            ->whereNotNull('start_datetime')
                            ->whereNotNull('end_datetime')
                            ->where('start_datetime', '<=', $now)
                            ->where('end_datetime', '>=', $now);
                    })->orWhere(function ($q2) use ($now) {
                        // Typ "pojedyncza": jeśli brak dat – zawsze aktywna
                        // lub jeśli daty są w zakresie
                        $q2->where('promotion_type', 'pojedyncza')
                            ->where(function ($q3) use ($now) {
                                $q3->whereNull('start_datetime')
                                    ->orWhere(function ($q4) use ($now) {
                                        $q4->whereNotNull('start_datetime')
                                            ->whereNotNull('end_datetime')
                                            ->where('start_datetime', '<=', $now)
                                            ->where('end_datetime', '>=', $now);
                                    });
                            });
                    });
                });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        switch ($request->sort) {
            case 'cena_asc':
                $query->orderBy('rental_price', 'asc');
                break;
            case 'cena_desc':
                $query->orderBy('rental_price', 'desc');
                break;
            case 'wypozyczenia_desc':
                $query->orderBy('number_of_rentals', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc'); #default sorting by id
        }


        $equipments = $query->paginate(10)->appends($request->query());

        return view('equipments.index', compact('equipments', 'categories', 'maxPrice'));
    }

    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);
        $additionalPhotos = $equipment->galleryPhotos();

        return view('equipments.show', compact('equipment', 'additionalPhotos'));
    }

}
