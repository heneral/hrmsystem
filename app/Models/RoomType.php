<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'name', 'slug', 'description', 'base_price', 'max_adults', 'max_children', 'max_occupancy', 'bed_configuration', 'size_sqm', 'is_active'];

    protected function casts(): array
    {
        return ['base_price' => 'decimal:2', 'size_sqm' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class);
    }

    public function roomRates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }
}