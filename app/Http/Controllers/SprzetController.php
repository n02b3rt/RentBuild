<?php

namespace App\Http\Controllers;

use App\Models\Sprzet;
use Illuminate\Http\Request;

class SprzetController extends Controller
{
    public function index(Request $request)
    {
        $query = Sprzet::query();

        // Pobieranie maksymalnej ceny
        $maxPrice = Sprzet::max('cena_wynajmu');

        // Pobieranie unikalnych kategorii
        $kategorie = Sprzet::select('kategoria')->distinct()->pluck('kategoria');

        // Filtry
        if ($request->filled('min_price')) {
            $query->where('cena_wynajmu', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('cena_wynajmu', '<=', $request->max_price);
        }

        if ($request->filled('dostepnosc')) {
            $query->where('dostepnosc', $request->dostepnosc);
        }

        if ($request->filled('stan_techniczny')) {
            $query->where('stan_techniczny', $request->stan_techniczny);
        }

        if ($request->filled('upust')) {
            $query->whereNotNull('upust');
        }

        if ($request->filled('kategoria')) {
            $query->where('kategoria', $request->kategoria);
        }

        $sprzety = $query->paginate(10);

        return view('sprzety.index', compact('sprzety', 'kategorie', 'maxPrice'));
    }

    public function show($id)
    {
        $sprzet = Sprzet::findOrFail($id);
        return view('sprzety.show', compact('sprzet'));
    }
}
