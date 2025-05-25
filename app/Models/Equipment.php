<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'description',
        'availability',
        'rental_price',
        'thumbnail',
        'folder_photos',
        'technical_state',
        'category',
        'discount',
        'number_of_rentals',
    ];

    // Jeśli nie używasz timestampów (created_at, updated_at), ustaw:
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

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

}
