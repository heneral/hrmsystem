<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\ReservationConfirmed;
use App\Events\ReservationCreated;
use App\Models\HotelService;

class BookingService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
        private readonly CouponService $coupons,
    ) {}

    public function create(User $customer, Room $room, Guest $primaryGuest, Carbon $checkIn, Carbon $checkOut, int $adults, int $children = 0, ?string $specialRequests = null, array $serviceSelections = [], ?string $couponCode = null): Reservation
    {
        return DB::transaction(function () use ($customer, $room, $primaryGuest, $checkIn, $checkOut, $adults, $children, $specialRequests, $serviceSelections, $couponCode) {
            $lockedRoom = Room::query()->with('roomType')->lockForUpdate()->findOrFail($room->id);

            if (! $this->availability->isAvailable($lockedRoom->id, $checkIn, $checkOut, $adults, $children)) {
                throw ValidationException::withMessages(['room' => 'This room is no longer available for the selected dates.']);
            }

            $pricing = $this->pricing->calculate($lockedRoom->roomType, $checkIn, $checkOut);
            $serviceItems = [];
            foreach (array_unique($serviceSelections) as $serviceId) {
                $service = HotelService::with('prices')->where('is_active', true)->findOrFail($serviceId);
                $servicePrice = $service->prices->first(fn ($price) => $checkIn->betweenIncluded($price->starts_on, $price->ends_on));
                $unitPrice = $servicePrice ? (float) $servicePrice->price : 0;
                $quantity = match ($service->pricing_unit->value) {
                    'per_night' => $checkIn->diffInDays($checkOut),
                    'per_guest' => $adults + $children,
                    default => 1,
                };
                $serviceItems[] = ['service' => $service, 'quantity' => $quantity, 'unit_price' => $unitPrice, 'total' => $unitPrice * $quantity];
            }
            $serviceTotal = collect($serviceItems)->sum('total');
            $discount = 0;
            $coupon = $couponCode ? $this->coupons->validate($couponCode, $customer, (float) $pricing['subtotal'] + $serviceTotal, $checkIn) : null;
            if ($coupon) {
                $discount = $this->coupons->discount($coupon, (float) $pricing['subtotal'] + $serviceTotal);
            }
            $subtotal = (float) $pricing['subtotal'] + $serviceTotal;
            $taxes = (float) $pricing['taxes'];
            $reservation = Reservation::create([
                'reservation_number' => 'HH-'.str()->upper(str()->random(10)),
                'user_id' => $customer->id,
                'room_id' => $lockedRoom->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'adults' => $adults,
                'children' => $children,
                'status' => ReservationStatus::CONFIRMED,
                'payment_status' => 'pending',
                'special_requests' => $specialRequests,
                'number_of_nights' => $pricing['number_of_nights'], 'rate' => $pricing['rate'], 'subtotal' => $subtotal, 'taxes' => $taxes, 'discounts' => $discount, 'total' => max(0, $subtotal + $taxes - $discount),
            ]);
            foreach ($serviceItems as $item) {
                $reservation->items()->create(['item_type' => 'service', 'description' => $item['service']->name, 'quantity' => $item['quantity'], 'unit_price' => $item['unit_price'], 'total' => $item['total']]);
            }
            if ($coupon) {
                $this->coupons->record($coupon, $customer, $reservation->id, $discount);
            }
            $reservation->guests()->attach($primaryGuest->id, ['is_primary' => true]);
            $lockedRoom->update(['status' => RoomStatus::RESERVED]);

            $reservation = $reservation->load(['room.roomType', 'guests']);
            $customer->notify(new ReservationConfirmed($reservation));
            ReservationCreated::dispatch($reservation);

            return $reservation;
        });
    }
}