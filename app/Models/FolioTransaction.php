<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FolioTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'service_order_id', 'type', 'description', 'amount', 'status', 'posted_at'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'posted_at' => 'datetime']; }
    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function serviceOrder(): BelongsTo { return $this->belongsTo(ServiceOrder::class); }
}
