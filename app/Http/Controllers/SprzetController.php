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

        // Sortowanie
        if ($request->filled('sortuj')) {
            switch ($request->sortuj) {
                case 'cena_asc':
                    $query->orderBy('cena_wynajmu', 'asc');
                    break;
                case 'cena_desc':
                    $query->orderBy('cena_wynajmu', 'desc');
                    break;
                case 'wypozyczenia_desc':
                    $query->orderBy('ilosc_wypozyczen', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc'); // domyślne
        }

        $sprzety = $query->paginate(10)->appends($request->query());

        return view('sprzety.index', compact('sprzety', 'kategorie', 'maxPrice'));
    }


    public function show($id)
    {
        $sprzet = Sprzet::findOrFail($id);
        return view('sprzety.show', compact('sprzet'));
    }
}
