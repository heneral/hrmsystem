<?php

namespace App\Models;

use App\Enums\HousekeepingStatus;
use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'room_number', 'floor', 'status', 'housekeeping_status', 'maintenance_status', 'is_active'];

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'housekeeping_status' => HousekeepingStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function isBookable(): bool
    {
        return $this->is_active && in_array($this->status, [RoomStatus::AVAILABLE, RoomStatus::CLEAN], true);
    }
}