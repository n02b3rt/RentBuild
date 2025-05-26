<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class PromotionAddController extends Controller
{
    public function create()
    {
        // Bierzemy wszystkie kategorie z tabeli Equipment
        $allCategories = Equipment::select('category')->distinct()->pluck('category');

        // Zbieramy kategorie, które MAJĄ już aktywną lub nadchodzącą promocję
        $categoriesWithPromotion = Equipment::where('promotion_type', 'kategoria')
            ->where(function ($query) {
                $query->where('end_datetime', '>=', now());
            })
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Filtrujemy tylko te, które NIE mają aktywnej/nadchodzącej promocji
        $availableCategories = $allCategories->diff($categoriesWithPromotion);

        return view('admin.promotions.add', [
            'categories' => $availableCategories
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Dodawanie promocji rozpoczęte', $request->all());
        // Walidacja formularza
        $validated = $request->validate([
            'category' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'discount_percentage' => 'required|numeric|min:1|max:100',
        ]);

        // Wyszukiwanie produktów w wybranej kategorii
        $products = Equipment::where('category', $validated['category'])->get();

        // Sprawdzamy, czy znaleźliśmy produkty w tej kategorii
        if ($products->isEmpty()) {
            return back()->with('error', 'Nie znaleziono produktów w tej kategorii.');
        }

        \Log::info('Znalezione sprzęty', ['count' => $products->count()]);

        foreach ($products as $product) {
            \Log::info('Aktualizacja sprzętu', ['id' => $product->id, 'name' => $product->name]);

            $product->promotion_type = 'kategoria';
            $product->start_datetime = $validated['start_datetime'];
            $product->end_datetime = $validated['end_datetime'];
            $product->discount = $validated['discount_percentage'];
            $product->save();
        }


        // Po zapisaniu przekierowujemy na stronę z promocjami
        return redirect()->route('admin.promotions.add')
            ->with('success', 'Promocja została przypisana do produktów w tej kategorii.');
    }
}

