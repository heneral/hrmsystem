<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePrice extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'starts_on', 'ends_on', 'price'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'price' => 'decimal:2'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(HotelService::class, 'service_id');
    }
}