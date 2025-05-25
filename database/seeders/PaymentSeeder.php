<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payments')->insert([
            [
                'rental_id' => 1,
                'amount' => 482.73,
                'payment_date' => '2025-05-17',
                'status' => 'oplacone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rental_id' => 2,
                'amount' => 352.51,
                'payment_date' => '2025-05-03',
                'status' => 'oplacone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rental_id' => 3,
                'amount' => 311.21,
                'payment_date' => '2025-04-27',
                'status' => 'oplacone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rental_id' => 4,
                'amount' => 328.35,
                'payment_date' => '2025-05-07',
                'status' => 'oplacone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rental_id' => 5,
                'amount' => 425.44,
                'payment_date' => '2025-05-13',
                'status' => 'oplacone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
