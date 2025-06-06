<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;

class LastMonthRentalSeeder extends Seeder
{
    /**
     * Uruchomienie seedera.
     */
    public function run()
    {
        // Zakres: ubiegły miesiąc (od 00:00 pierwszego dnia do 23:59 ostatniego dnia)
        $startLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth()->startOfDay();
        $endLastMonth   = Carbon::now()->subMonthNoOverflow()->endOfMonth()->endOfDay();

        $users = User::all();
        $equipments = Equipment::all();

        if ($users->isEmpty() || $equipments->isEmpty()) {
            $this->command->info('Brak użytkowników lub sprzętów w bazie – seed nie zostanie wykonany.');
            return;
        }

        $count = 150;

        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $equipment = $equipments->random();

            $timestamp = rand($startLastMonth->timestamp, $endLastMonth->timestamp);
            $startDate = Carbon::createFromTimestamp($timestamp)->startOfDay();

            $maxDuration = min(7, $startDate->diffInDays($endLastMonth) + 1);
            $durationDays = rand(1, $maxDuration);
            $endDate = (clone $startDate)->addDays($durationDays - 1)->endOfDay();

            $days = $startDate->diffInDays($endDate) + 1;
            $rentalPricePerDay = $equipment->rental_price;
            $brutto = $rentalPricePerDay * $days;

            // Losujemy, czy wypożyczenie ma operatora (20% szans)
            $withOperator = (rand(1, 100) <= 20);

            // Losujemy created_at w obrębie dnia startDate
            $createdAt = $startDate->copy()
                ->addHours(rand(0, 23))
                ->addMinutes(rand(0, 59))
                ->addSeconds(rand(0, 59));

            Rental::create([
                'user_id'       => $user->id,
                'equipment_id'  => $equipment->id,
                'start_date'    => $startDate->toDateString(),
                'end_date'      => $endDate->toDateString(),
                'total_price'   => $brutto,
                'with_operator' => $withOperator,
                'status'        => 'zrealizowane',
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);
        }

        $this->command->info("Utworzono {$count} wypożyczeń z ostatniego miesiąca (status = 'zrealizowane').");
    }
}
