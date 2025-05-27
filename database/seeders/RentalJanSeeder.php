<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalJanSeeder extends Seeder
{
    public function run(): void
    {
        // Dodatkowe wypożyczenia dla Jana

        $today = Carbon::today();
        $additionalRentals = [];

        // Jan – oczekujące dziś lub później
        for ($i = 0; $i < 3; $i++) {
            $start = $today->copy()->addDays($i);
            $end = $start->copy()->addDays(2);
            $days = $start->diffInDays($end) + 1;
            $daily = 350;
            $withOperator = false;

            $additionalRentals[] = [
                'user_id' => 1,
                'equipment_id' => rand(1, 10),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'oczekujace',
                'notes' => null,
                'payment_reference' => 'token_jan_o_' . $i,
                'with_operator' => $withOperator,
                'total_price' => $days * $daily,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Jan – zatwierdzone dziś lub później
        for ($i = 0; $i < 3; $i++) {
            $start = $today->copy()->addDays($i);
            $end = $start->copy()->addDays(2);
            $days = $start->diffInDays($end) + 1;
            $daily = 350;
            $withOperator = false;

            $additionalRentals[] = [
                'user_id' => 1,
                'equipment_id' => rand(1, 10),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'aktualne',
                'notes' => null,
                'payment_reference' => 'token_jan_o_' . $i,
                'with_operator' => $withOperator,
                'total_price' => $days * $daily,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Jan – zakończone w 2011
        for ($i = 0; $i < 3; $i++) {
            $start = Carbon::create(2011, 5, 1 + $i);
            $end = $start->copy()->addDays(2);
            $days = $start->diffInDays($end) + 1;
            $daily = 400;
            $withOperator = true;

            $additionalRentals[] = [
                'user_id' => 1,
                'equipment_id' => rand(1, 10),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'zrealizowane',
                'notes' => 'archiwalne',
                'payment_reference' => 'token_jan_z_' . $i,
                'with_operator' => $withOperator,
                'total_price' => $days * $daily + $days * 350,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Jan – przyszłe w 2069
        for ($i = 0; $i < 3; $i++) {
            $start = Carbon::create(2069, 6, 1 + $i);
            $end = $start->copy()->addDays(2);
            $days = $start->diffInDays($end) + 1;
            $daily = 500;
            $withOperator = false;

            $additionalRentals[] = [
                'user_id' => 1,
                'equipment_id' => rand(1, 10),
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'nadchodzace',
                'notes' => null,
                'payment_reference' => 'token_jan_n_' . $i,
                'with_operator' => $withOperator,
                'total_price' => $days * $daily,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('rentals')->insert($additionalRentals);
    }
}
