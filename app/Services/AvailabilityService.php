<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    public function search(Carbon $checkIn, Carbon $checkOut, int $adults, int $children = 0, ?int $ignoreReservationId = null): Builder
    {
        if ($checkIn->isSameDay($checkOut) || $checkIn->greaterThan($checkOut)) {
            throw new \InvalidArgumentException('Check-out must be after check-in.');
        }

        return Room::query()
            ->with(['roomType.amenities', 'roomType.images'])
            ->where('is_active', true)
            ->whereNotIn('status', [RoomStatus::MAINTENANCE->value, RoomStatus::OUT_OF_ORDER->value])
            ->whereHas('roomType', function (Builder $query) use ($adults, $children) {
                $query->where('is_active', true)
                    ->where('max_adults', '>=', $adults)
                    ->where('max_children', '>=', $children)
                    ->where('max_occupancy', '>=', $adults + $children);
            })
            ->whereDoesntHave('reservations', function (Builder $query) use ($checkIn, $checkOut, $ignoreReservationId) {
                $query->whereNotIn('status', [ReservationStatus::CANCELLED->value, ReservationStatus::NO_SHOW->value])
                    ->whereDate('check_in', '<', $checkOut->toDateString())
                    ->whereDate('check_out', '>', $checkIn->toDateString());
                if ($ignoreReservationId !== null) {
                    $query->where('reservations.id', '<>', $ignoreReservationId);
                }
            });
    }

    public function isAvailable(int $roomId, Carbon $checkIn, Carbon $checkOut, int $adults, int $children = 0, ?int $ignoreReservationId = null): bool
    {
        return $this->search($checkIn, $checkOut, $adults, $children, $ignoreReservationId)->whereKey($roomId)->exists();
    }
}