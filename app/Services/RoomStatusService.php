<?php

namespace App\Services;

use App\Enums\HousekeepingStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Validation\ValidationException;

class RoomStatusService
{
    private const TRANSITIONS = [
        'occupied' => ['dirty', 'cleaning'], 'dirty' => ['cleaning'], 'cleaning' => ['clean', 'occupied'], 'clean' => ['available'],
        'available' => ['reserved', 'maintenance', 'out_of_order'], 'reserved' => ['occupied', 'available'],
        'maintenance' => ['available', 'out_of_order'], 'out_of_order' => ['maintenance', 'available'],
    ];

    public function transition(Room $room, RoomStatus $status): Room
    {
        $current = $room->status->value;
        if ($current !== $status->value && ! in_array($status->value, self::TRANSITIONS[$current] ?? [], true)) {
            throw ValidationException::withMessages(['status' => "Room cannot transition from {$current} to {$status->value}."]);
        }
        $room->update(['status' => $status, 'housekeeping_status' => match ($status) { RoomStatus::DIRTY => HousekeepingStatus::DIRTY, RoomStatus::CLEANING => HousekeepingStatus::CLEANING, RoomStatus::CLEAN, RoomStatus::AVAILABLE, RoomStatus::OCCUPIED => HousekeepingStatus::CLEAN, default => $room->housekeeping_status }]);

        return $room->refresh();
    }
}