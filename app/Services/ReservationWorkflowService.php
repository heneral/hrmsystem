<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Events\ReservationCancelled;
use App\Services\PricingService;

class ReservationWorkflowService
{
    public function __construct(private readonly RoomStatusService $roomStatus, private readonly AvailabilityService $availability, private readonly PricingService $pricing) {}

    public function modify(Reservation $reservation, int $roomId, \Illuminate\Support\Carbon $checkIn, \Illuminate\Support\Carbon $checkOut, int $adults, int $children, ?string $specialRequests = null): Reservation
    {
        if (! in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED], true)) {
            throw ValidationException::withMessages(['reservation' => 'Only pending or confirmed reservations can be modified.']);
        }

        return DB::transaction(function () use ($reservation, $roomId, $checkIn, $checkOut, $adults, $children, $specialRequests) {
            $room = \App\Models\Room::query()->with('roomType')->lockForUpdate()->findOrFail($roomId);
            $oldRoom = $reservation->room()->lockForUpdate()->first();
            if (! $this->availability->isAvailable($room->id, $checkIn, $checkOut, $adults, $children, $reservation->id)) {
                throw ValidationException::withMessages(['room' => 'The selected room is not available for these dates.']);
            }
            $pricing = $this->pricing->calculate($room->roomType, $checkIn, $checkOut);
            $reservation->update(['room_id' => $room->id, 'check_in' => $checkIn, 'check_out' => $checkOut, 'adults' => $adults, 'children' => $children, 'special_requests' => $specialRequests, ...$pricing]);
            if ($oldRoom->id !== $room->id && $oldRoom->status === RoomStatus::RESERVED) {
                $this->roomStatus->transition($oldRoom, RoomStatus::AVAILABLE);
            }
            if ($room->status === RoomStatus::AVAILABLE || $room->status === RoomStatus::CLEAN) {
                $this->roomStatus->transition($room, RoomStatus::RESERVED);
            }

            return $reservation->refresh()->load(['room.roomType', 'guests']);
        });
    }

    public function cancel(Reservation $reservation, ?string $reason = null): Reservation
    {
        if (! in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED], true)) {
            throw ValidationException::withMessages(['reservation' => 'This reservation cannot be cancelled.']);
        }

        return DB::transaction(function () use ($reservation, $reason) {
            $reservation->update(['status' => ReservationStatus::CANCELLED, 'cancelled_at' => now(), 'cancellation_reason' => $reason]);
            if ($reservation->room->status === RoomStatus::RESERVED) {
                $this->roomStatus->transition($reservation->room, RoomStatus::AVAILABLE);
            }
            ReservationCancelled::dispatch($reservation->refresh(), $reason);

            return $reservation->refresh();
        });
    }

    public function checkIn(Reservation $reservation): Reservation
    {
        if ($reservation->status !== ReservationStatus::CONFIRMED) {
            throw ValidationException::withMessages(['reservation' => 'Only confirmed reservations can be checked in.']);
        }
        if ((float) $reservation->payments()->where('status', 'paid')->sum('amount') < (float) $reservation->total) {
            throw ValidationException::withMessages(['payment' => 'The reservation must be paid before check-in.']);
        }

        return DB::transaction(function () use ($reservation) {
            $this->roomStatus->transition($reservation->room, RoomStatus::OCCUPIED);
            $reservation->update(['status' => ReservationStatus::CHECKED_IN]);

            return $reservation->refresh();
        });
    }

    public function checkOut(Reservation $reservation): Reservation
    {
        if ($reservation->status !== ReservationStatus::CHECKED_IN) {
            throw ValidationException::withMessages(['reservation' => 'Only checked-in reservations can be checked out.']);
        }
        $paid = (float) $reservation->payments()->where('status', 'paid')->sum('amount');
        $folioCharges = (float) $reservation->folioTransactions()->where('status', 'posted')->sum('amount');
        if ($paid < (float) $reservation->total + $folioCharges) {
            throw ValidationException::withMessages(['payment' => 'Settle the outstanding balance before checkout.']);
        }

        return DB::transaction(function () use ($reservation) {
            $this->roomStatus->transition($reservation->room, RoomStatus::DIRTY);
            $reservation->update(['status' => ReservationStatus::CHECKED_OUT]);
            HousekeepingTask::create(['room_id' => $reservation->room_id, 'reservation_id' => $reservation->id, 'task_type' => 'checkout', 'status' => 'pending', 'priority' => 'high', 'notes' => 'Checkout cleaning task']);

            return $reservation->refresh();
        });
    }
}