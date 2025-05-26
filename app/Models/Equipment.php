<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        if ($this->discount !== null) {
            return round($this->rental_price * (1 - $this->discount / 100), 2);
        }
        return $this->rental_price;
    }

    // Relacja z modelem Rental
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}

