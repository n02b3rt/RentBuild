<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rentals')->insert([
            [
                'user_id' => 2,
                'equipment_id' => 6,
                'start_date' => '2025-05-18',
                'end_date' => '2025-05-25',
                'status' => 'zaplanowane',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'equipment_id' => 9,
                'start_date' => '2025-05-04',
                'end_date' => '2025-05-05',
                'status' => 'aktywne',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'equipment_id' => 2,
                'start_date' => '2025-04-28',
                'end_date' => '2025-05-05',
                'status' => 'zakończone',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'equipment_id' => 3,
                'start_date' => '2025-05-08',
                'end_date' => '2025-05-12',
                'status' => 'anulowane',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6,
                'equipment_id' => 4,
                'start_date' => '2025-05-14',
                'end_date' => '2025-05-15',
                'status' => 'reklamacja',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
