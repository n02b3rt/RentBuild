<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'rental_id',
        'amount',
        'payment_date',
        'status',
    ];

    public function isPaid(): bool
    {
        return $this->payment && $this->payment->status === 'opłacone';
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
