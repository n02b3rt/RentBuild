<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

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
            $query->whereNotNull('discount');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        if ($request->filled('sort')) {
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
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $equipments = $query->paginate(10)->appends($request->query());

        return view('equipments.index', compact('equipments', 'categories', 'maxPrice'));
    }

    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipments.show', compact('equipment'));
    }
}
