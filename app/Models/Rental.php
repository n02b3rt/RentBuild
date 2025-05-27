<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = [
        'user_id',
        'equipment_id',
        'start_date',
        'end_date',
        'status',
        'notes',
        'payment_reference',
        'total_price',
        'with_operator',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
