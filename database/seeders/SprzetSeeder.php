<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SprzetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sprzety')->insert([
            [
                'nazwa' => 'Młot udarowy Bosch GSH 11',
                'opis' => 'Moc 1500W, siła udaru 16.8J, idealny do kucia betonu.',
                'dostepnosc' => 'dostepny',
                'cena_wynajmu' => 89.99,
                'zdjecie_glowne' => 'images/mlot-udarowy/glowne.jpg',
                'folder_zdjec' => 'images/mlot-udarowy/',
                'stan_techniczny' => 'uzywany',
                'kategoria' => 'Młoty',
                'upust' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

