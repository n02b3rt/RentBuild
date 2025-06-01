<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClearExpiredPromotions extends Command
{
    protected $signature = 'promotions:clear-expired';
    protected $description = 'Czyści zakończone promocje z produktów';

    public function handle()
    {
        // Używamy czasu z bazy danych, by ominąć błędny czas serwera PHP
        $appNow = Carbon::now()->format('Y-m-d H:i:s');
        $dbNow = DB::selectOne('SELECT NOW() as now')->now;

        $this->info("App time: {$appNow}");
        $this->info("DB time:  {$dbNow}");

        // Pobierz wszystkie promocje pojedyncze i kategoria, wygasłe względem czasu DB
        $expired = Equipment::whereIn('promotion_type', ['kategoria', 'pojedyncza'])
            ->whereRaw('end_datetime < NOW()')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Brak wygasłych promocji do wyświetlenia.');
            return 0;
        }

        // Wyświetl tabelę z wygasłymi promocjami
        $this->table(
            ['ID', 'Nazwa', 'Typ promocji', 'Koniec promocji'],
            $expired->map(function ($item) {
                return [
                    $item->id,
                    $item->name,
                    $item->promotion_type,
                    Carbon::parse($item->end_datetime)->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        );

        if (! $this->confirm('Czy na pewno chcesz wyczyścić powyższe promocje?')) {
            $this->info('Anulowano operację.');
            return 0;
        }

        // Wyczyść pola promocji wszystkich wygasłych
        $count = Equipment::whereIn('promotion_type', ['kategoria', 'pojedyncza'])
            ->whereRaw('end_datetime < NOW()')
            ->update([
                'promotion_type' => null,
                'discount'       => null,
                'start_datetime' => null,
                'end_datetime'   => null,
            ]);

        $this->info("Wyczyszczono {$count} zakończonych promocji.");
        Log::info("Wyczyszczono {$count} zakończonych promocji.");

        return 0;
    }
}
