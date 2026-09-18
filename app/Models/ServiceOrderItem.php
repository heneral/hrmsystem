<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['service_order_id', 'service_id', 'description', 'quantity', 'unit_price', 'total', 'notes'];
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'total' => 'decimal:2']; }
    public function order(): BelongsTo { return $this->belongsTo(ServiceOrder::class, 'service_order_id'); }
    public function service(): BelongsTo { return $this->belongsTo(HotelService::class, 'service_id'); }
}
