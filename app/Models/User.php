<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'address',
        'shipping_address',
        'payment_token',
        'payment_provider',
        'role',
        'rentals_count',
        'account_balance',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'two_factor_failed_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at'          => 'datetime',
        'account_balance'            => 'decimal:2',
        'two_factor_enabled'         => 'boolean',
        'two_factor_secret'          => 'encrypted',
        'two_factor_recovery_codes'  => 'array',
        'two_factor_failed_attempts' => 'integer',
    ];

    /**
     * Send custom email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Relationship to rentals.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Check user role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Enable Two-Factor Authentication: store secret and hashed recovery codes.
     */
    public function enableTwoFactor(string $secret, array $recoveryCodes): void
    {
        $this->two_factor_secret = $secret;
        $this->two_factor_recovery_codes = array_map(fn($code) => bcrypt($code), $recoveryCodes);
        $this->two_factor_enabled = true;
        $this->two_factor_failed_attempts = 0;
        $this->save();
    }

    /**
     * Disable Two-Factor Authentication: clear secret and codes.
     */
    public function disableTwoFactor(): void
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_enabled = false;
        $this->two_factor_failed_attempts = 0;
        $this->save();
    }

    /**
     * Increment the count of failed 2FA attempts.
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('two_factor_failed_attempts');
    }

    /**
     * Reset the failed 2FA attempts counter.
     */
    public function resetFailedAttempts(): void
    {
        $this->two_factor_failed_attempts = 0;
        $this->save();
    }

    /**
     * Validate a recovery code: if valid, remove it and reset attempts.
     */
    public function validateRecoveryCode(string $code): bool
    {
        $valid = false;
        $codes = $this->two_factor_recovery_codes ?? [];

        foreach ($codes as $idx => $hashed) {
            if (password_verify($code, $hashed)) {
                $valid = true;
                unset($codes[$idx]);
                break;
            }
        }

        if ($valid) {
            $this->two_factor_recovery_codes = array_values($codes);
            $this->resetFailedAttempts();
            $this->save();
        } else {
            $this->incrementFailedAttempts();
        }

        return $valid;
    }

    /**
     * Generate random recovery codes (uppercase strings by default).
     */
    public static function generateRecoveryCodes(int $count = 10, int $length = 8): array
    {
        return array_map(fn() => Str::upper(Str::random($length)), range(1, $count));
    }
}
