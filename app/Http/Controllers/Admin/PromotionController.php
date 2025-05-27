<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Carbon\Carbon;

class PromotionController extends Controller
{
    public function index()
    {
        // Pobierz wszystkie produkty, które mają przypisaną promocję typu 'kategoria'
        $promotions = Equipment::where('promotion_type', 'kategoria')->get();

        // Przetwarzamy każdą promocję, aby określić jej status
        $promotionsWithStatus = $promotions->map(function ($promotion) {
            $currentDate = Carbon::now();
            $startDate = Carbon::parse($promotion->start_datetime);
            $endDate = Carbon::parse($promotion->end_datetime);

            // Określenie statusu promocji
            if ($currentDate->lt($startDate)) {
                $status = 'Nadchodząca';
            } elseif ($currentDate->between($startDate, $endDate)) {
                $status = 'Aktywna';
            } else {
                $status = 'Zakończona';
            }

            // Dodajemy status do obiektu promocji
            $promotion->status = $status;

            return $promotion;
        });

        // Grupujemy promocje po kategoriach (zakładając, że kategorie są oddzielone przecinkiem)
        $promotionsByCategory = $promotionsWithStatus->groupBy(function ($promotion) {
            return explode(',', $promotion->category); // Dzielimy kategorię po przecinku
        });

        // Renderujemy widok 'admin.promotions.category' z pogrupowanymi promocjami
        return view('admin.promotions.category', compact('promotionsByCategory'));
    }

    public function destroyCategoryPromotion($category)
    {
        Equipment::where('category', $category)
            ->where('promotion_type', 'kategoria')
            ->update([
                'promotion_type' => null,
                'discount' => null,
                'start_datetime' => null,
                'end_datetime' => null,
            ]);

        return redirect()->route('admin.promotions.category')->with('success', "Promocja dla kategorii '{$category}' została usunięta.");
    }

}
