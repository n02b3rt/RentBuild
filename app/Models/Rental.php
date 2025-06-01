<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    // --- Stałe statusów reklamacji ---
    public const COMPLAINT_STATUS_PREFIX = 'reklamacja_';

    public const STATUS_REKLAMACJA = 'reklamacja';
    public const COMPLAINT_STATUS_WERYFIKACJA = 'weryfikacja';
    public const COMPLAINT_STATUS_ODRZUCONO = 'odrzucono';
    public const COMPLAINT_STATUS_PRZYJETO = 'przyjeto';

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

    // --- Relacje ---

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

    // --- Metody związane z reklamacją ---

    /**
     * Sprawdza, czy status jest reklamacyjny (czy zaczyna się od 'reklamacja_')
     */
    public function isComplaint(): bool
    {
        return $this->status === self::STATUS_REKLAMACJA
            || str_starts_with($this->status, self::COMPLAINT_STATUS_PREFIX);
    }

    /**
     * Zwraca podstatus reklamacji, np. 'weryfikacja', 'odrzucono', 'przyjeto'
     * Lub null, jeśli status nie jest reklamacyjny
     */
    public function complaintStatus(): ?string
    {
        if ($this->isComplaint()) {
            return substr($this->status, strlen(self::COMPLAINT_STATUS_PREFIX));
        }
        return null;
    }

    /**
     * Ustawia status reklamacji na podany podstatus (weryfikacja, odrzucono, przyjeto).
     * Rzuca wyjątek jeśli podstatus jest nieprawidłowy.
     */
    public function setComplaintStatus(string $subStatus): void
    {
        $valid = [
            self::COMPLAINT_STATUS_WERYFIKACJA,
            self::COMPLAINT_STATUS_ODRZUCONO,
            self::COMPLAINT_STATUS_PRZYJETO,
        ];

        if (!in_array($subStatus, $valid)) {
            throw new \InvalidArgumentException("Nieprawidłowy podstatus reklamacji: $subStatus");
        }

        $this->status = self::COMPLAINT_STATUS_PREFIX . $subStatus;
    }

    /**
     * Dodaje zgłoszenie reklamacji — wpisuje datę i opis na początek notes
     * i ustawia status na 'reklamacja_weryfikacja'.
     */
    public function submitComplaint(string $description): void
    {
        $date = now()->format('Y-m-d H:i');
        $complaintNote = "[Reklamacja zgłoszona: $date]\nOpis reklamacji: $description\n\n";

        $this->notes = $complaintNote . $this->notes;
        $this->status = self::STATUS_REKLAMACJA;  // teraz ustawiamy ogólny status reklamacji
        $this->save();
    }

    /**
     * Akceptuje reklamację — zmienia status i zwraca kwotę do konta użytkownika
     */
    public function acceptComplaint(): void
    {
        $this->setComplaintStatus(self::COMPLAINT_STATUS_PRZYJETO);
        $this->save();

        $user = $this->user;
        $user->account_balance += $this->total_price;
        $user->save();
    }
}
