<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'amount', 'currency', 'payment_method', 'transaction_id', 'gateway', 'status', 'paid_at', 'refunded_amount', 'refunded_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'refunded_amount' => 'decimal:2', 'status' => PaymentStatus::class, 'paid_at' => 'datetime', 'refunded_at' => 'datetime'];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}