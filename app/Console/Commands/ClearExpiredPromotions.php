<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipment;
use Carbon\Carbon;

class ClearExpiredPromotions extends Command
{
    protected $signature = 'promotions:clear-expired';
    protected $description = 'Czyści zakończone promocje z produktów';

    public function handle()
    {
        $now = Carbon::now();

        $expired = Equipment::where('promotion_type', 'kategoria')
            ->where('end_datetime', '<', $now)
            ->get();

        $count = 0;

        foreach ($expired as $equipment) {
            $equipment->promotion_type = null;
            $equipment->discount = null;
            $equipment->start_datetime = null;
            $equipment->end_datetime = null;
            $equipment->save();
            $count++;
        }
        \Log::info("Wyczyszczono $count zakończonych promocji.");
        $this->info("Wyczyszczono $count zakończonych promocji.");
    }
}
