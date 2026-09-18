<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'reservation_id', 'user_id', 'category', 'status', 'notes', 'delivery_location', 'requested_at'];

    protected function casts(): array
    {
        return ['requested_at' => 'datetime'];
    }

    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(ServiceOrderItem::class); }
    public function folioTransactions(): HasMany { return $this->hasMany(FolioTransaction::class); }
}
