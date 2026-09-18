<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'cancellation_policy', 'minimum_stay', 'maximum_stay', 'advance_days', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function roomRates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }
}