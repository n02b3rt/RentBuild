<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Equipment extends Model
{
    // Jeśli tabela w bazie danych nazywa się inaczej, to ją określamy
    protected $table = 'equipment';

    // Określenie, które kolumny mogą być masowo przypisane
    protected $fillable = [
        'name',
        'description',
        'availability',
        'rental_price',
        'thumbnail',
        'folder_photos',
        'technical_state',
        'category',
        'promotion_type',
        'discount',
        'start_datetime',
        'end_datetime',
        'number_of_rentals',
    ];

    public $timestamps = false;

    // Przykładowa metoda pomocnicza
    public function isAvailable(): bool
    {
        return $this->availability === 'dostepny';
    }

    public function finalPrice(): float
    {
        if ($this->discount !== null && $this->isPromotionActive()) {
            return round($this->rental_price * (1 - $this->discount / 100), 2);
        }
        return $this->rental_price;
    }

    // Relacja z modelem Rental
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function isPromotionActive(): bool
    {
        // Jeśli brak discount, to nie ma promocji
        if ($this->discount === null) {
            return false;
        }

        // Jeśli typ promocji to "kategoria"
        if ($this->promotion_type === 'kategoria') {
            if ($this->start_datetime === null || $this->end_datetime === null) {
                return false;
            }

            $now = Carbon::now();

            return $now->between(
                Carbon::parse($this->start_datetime),
                Carbon::parse($this->end_datetime)
            );
        }

        // Jeśli typ promocji to "pojedyncza"
        if ($this->promotion_type === 'pojedyncza') {
            // Jeśli NIE ustawiono daty – promocja zawsze aktywna
            if ($this->start_datetime === null || $this->end_datetime === null) {
                return true;
            }

            // Jeśli daty są ustawione, sprawdzamy zakres
            $now = Carbon::now();

            return $now->between(
                Carbon::parse($this->start_datetime),
                Carbon::parse($this->end_datetime)
            );
        }

        // Dla innych przypadków zwróć false
        return false;
    }
}

