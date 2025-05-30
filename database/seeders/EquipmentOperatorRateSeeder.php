<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentOperatorRateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pobieramy unikalne kategorie z bazy
        $categories = DB::table('equipment')
            ->select('category')
            ->distinct()
            ->pluck('category');

        $this->command->info('Znalezione kategorie:');
        foreach ($categories as $cat) {
            $this->command->line("  – “{$cat}”");
        }

        // 2. Mapa stawek
        $rates = [
            'Młoty wyburzeniowe / udarowe' => 500,
            'Pilarki i piły mechaniczne'   => 200,
            'Minikoparki'                  => 650,
            'Podnośniki i zwyżki'          => 450,
            'Zagęszczarki i ubijaki'       => 300,
            'Betoniarki'                   => 250,
            'Wiertarki i wkrętarki'        => 180,
            'Ładowarki'                    => 550,
            'Osuszacze i nagrzewnice'      => 150,
            'Sprężarki i kompresory'       => 400,
            'Agregaty prądotwórcze'        => 350,
            'Rusztowania i drabiny'        => 220,
            'Koparki'                      => 700,
            'Pompy wodne'                  => 280,
            'Szlifierki i przecinarki'     => 260,
            'Lasery budowlane i niwelatory'=> 300,
        ];

        // 3. Aktualizacja i logowanie
        foreach ($categories as $category) {
            $rate = $rates[$category] ?? null;
            if ($rate === null) {
                $this->command->warn("Brak stawki dla kategorii “{$category}” – zostawiamy 0");
                continue;
            }
            DB::table('equipment')
                ->where('category', $category)
                ->update(['operator_rate' => $rate]);

            $this->command->info("Ustawiono operator_rate={$rate} dla kategorii “{$category}”");
        }
    }
}
