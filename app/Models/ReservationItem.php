<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'item_type', 'description', 'quantity', 'unit_price', 'total'];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}