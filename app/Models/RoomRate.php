<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRate extends Model
{
    use HasFactory;

    protected $fillable = ['rate_plan_id', 'room_type_id', 'starts_on', 'ends_on', 'price'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'price' => 'decimal:2'];
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}