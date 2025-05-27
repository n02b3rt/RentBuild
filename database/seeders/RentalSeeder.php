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

            [
                'user_id' => 2,
                'equipment_id' => 6,
                'start_date' => '2025-05-18',
                'end_date' => '2025-05-25',
                'status' => 'oczekujace',
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
                'status' => 'aktualne',
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
                'status' => 'zrealizowane',
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
                'status' => 'zrealizowane',
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
                'status' => 'zrealizowane',
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

            unset($rental['daily_price']);
        }

        DB::table('rentals')->insert($rentals);
    }
}
