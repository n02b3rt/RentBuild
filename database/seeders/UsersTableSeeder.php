<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Jan',
                'last_name' => 'Kowalski',
                'phone' => '600123456',
                'email' => 'jan.kowalski@example.com',
                'password' => Hash::make('password123'),
                'address' => 'ul. Przykładowa 1, 00-001 Warszawa',
                'shipping_address' => 'ul. Magazynowa 10, 00-002 Warszawa',
                'payment_token' => null,
                'payment_provider' => null,
                'role' => 'klient',
                'rentals_count' => 2,
                'account_balance' => 150.00,
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Anna',
                'last_name' => 'Nowak',
                'phone' => '501765432',
                'email' => 'admin@example.com',
                'password' => Hash::make('adminadmin'),
                'address' => 'ul. Admina 5, 00-003 Kraków',
                'shipping_address' => null,
                'payment_token' => null,
                'payment_provider' => null,
                'role' => 'administrator',
                'rentals_count' => 0,
                'account_balance' => 0.00,
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
