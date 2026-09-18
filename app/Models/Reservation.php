<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_number', 'user_id', 'room_id', 'check_in', 'check_out', 'adults', 'children', 'number_of_nights', 'rate', 'subtotal', 'taxes', 'discounts', 'total', 'payment_status', 'status', 'cancelled_at', 'cancellation_reason', 'special_requests'];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'cancelled_at' => 'datetime',
            'rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'taxes' => 'decimal:2',
            'discounts' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => ReservationStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(Guest::class, 'reservation_guest')->withPivot('is_primary')->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function folioTransactions(): HasMany
    {
        return $this->hasMany(FolioTransaction::class);
    }
}