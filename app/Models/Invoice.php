<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'reservation_id', 'user_id', 'subtotal', 'taxes', 'discounts', 'total', 'amount_paid', 'balance', 'status', 'issued_at'];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'taxes' => 'decimal:2', 'discounts' => 'decimal:2', 'total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'balance' => 'decimal:2', 'issued_at' => 'datetime'];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}