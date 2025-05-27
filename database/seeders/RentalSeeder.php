<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $rentals = [
            // Jan Kowalski - różne wypożyczenia
            [
                'user_id' => 1,
                'equipment_id' => 1,
                'start_date' => '2025-05-10',
                'end_date' => '2025-05-15',
                'status' => 'oczekujace',
                'notes' => 'Pilne',
                'payment_reference' => 'token_jk_1',
                'with_operator' => false,
                'daily_price' => 300,
            ],
            [
                'user_id' => 1,
                'equipment_id' => 2,
                'start_date' => '2025-05-20',
                'end_date' => '2025-05-25',
                'status' => 'trwajace',
                'notes' => 'Z operatorem',
                'payment_reference' => 'token_jk_2',
                'with_operator' => true,
                'daily_price' => 400,
            ],
            [
                'user_id' => 1,
                'equipment_id' => 3,
                'start_date' => '2025-06-01',
                'end_date' => '2025-06-05',
                'status' => 'nadchodzace',
                'notes' => null,
                'payment_reference' => 'token_jk_3',
                'with_operator' => false,
                'daily_price' => 350,
            ],
            [
                'user_id' => 1,
                'equipment_id' => 4,
                'start_date' => '2025-04-01',
                'end_date' => '2025-04-07',
                'status' => 'zakonczone',
                'notes' => 'Bez problemów',
                'payment_reference' => 'token_jk_4',
                'with_operator' => true,
                'daily_price' => 450,
            ],
            [
                'user_id' => 1,
                'equipment_id' => 5,
                'start_date' => '2025-05-05',
                'end_date' => '2025-05-06',
                'status' => 'anulowane',
                'notes' => 'Klient anulował',
                'payment_reference' => 'token_jk_5',
                'with_operator' => false,
                'daily_price' => 500,
            ],
            [
                'user_id' => 1,
                'equipment_id' => 6,
                'start_date' => '2025-05-02',
                'end_date' => '2025-05-03',
                'status' => 'reklamacja',
                'notes' => 'Sprzęt uszkodzony',
                'payment_reference' => 'token_jk_6',
                'with_operator' => false,
                'daily_price' => 600,
            ],
            [
                'user_id' => 2,
                'equipment_id' => 6,
                'start_date' => '2025-05-18',
                'end_date' => '2025-05-25',
                'status' => 'zaplanowane',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_1',
                'with_operator' => true,
                'daily_price' => 500,
            ],
            [
                'user_id' => 3,
                'equipment_id' => 9,
                'start_date' => '2025-05-04',
                'end_date' => '2025-05-05',
                'status' => 'aktywne',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_2',
                'with_operator' => false,
                'daily_price' => 600,
            ],
            [
                'user_id' => 3,
                'equipment_id' => 2,
                'start_date' => '2025-04-28',
                'end_date' => '2025-05-05',
                'status' => 'zakończone',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_3',
                'with_operator' => true,
                'daily_price' => 450,
            ],
            [
                'user_id' => 5,
                'equipment_id' => 3,
                'start_date' => '2025-05-08',
                'end_date' => '2025-05-12',
                'status' => 'anulowane',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_4',
                'with_operator' => false,
                'daily_price' => 700,
            ],
            [
                'user_id' => 6,
                'equipment_id' => 4,
                'start_date' => '2025-05-14',
                'end_date' => '2025-05-15',
                'status' => 'reklamacja',
                'notes' => 'Brak uwag',
                'payment_reference' => 'token_5',
                'with_operator' => true,
                'daily_price' => 400,
            ],
        ];

        foreach ($rentals as &$rental) {
            $start = Carbon::parse($rental['start_date']);
            $end = Carbon::parse($rental['end_date']);
            $days = $start->diffInDays($end) + 1;

            $equipmentCost = $days * $rental['daily_price'];
            $operatorCost = $rental['with_operator'] ? $days * 350 : 0;

            $rental['total_price'] = $equipmentCost + $operatorCost;
            $rental['created_at'] = now();
            $rental['updated_at'] = now();

            unset($rental['daily_price']); // niepotrzebne przy insert
        }

        DB::table('rentals')->insert($rentals);
    }
}
