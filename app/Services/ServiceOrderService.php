<?php

namespace App\Services;

use App\Models\FolioTransaction;
use App\Models\HotelService;
use App\Models\Reservation;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceOrderService
{
    public function create(Reservation $reservation, HotelService $service, int $quantity, int $userId, ?string $notes = null, ?string $deliveryLocation = null): ServiceOrder
    {
        return DB::transaction(function () use ($reservation, $service, $quantity, $userId, $notes, $deliveryLocation) {
            if (! in_array($reservation->status->value, ['confirmed', 'checked_in'], true)) {
                throw ValidationException::withMessages(['reservation' => 'The reservation is no longer active.']);
            }
            $price = $service->prices()->whereDate('starts_on', '<=', today())->whereDate('ends_on', '>=', today())->first();
            if (! $price) {
                throw ValidationException::withMessages(['service' => 'The selected service is currently unavailable.']);
            }
            $total = (float) $price->price * $quantity;
            $category = $service->category ?: 'other';
            $prefix = match ($category) { 'food_and_beverage' => 'FB', 'transport' => 'TR', 'room_extra' => 'EX', default => 'SV' };
            $order = ServiceOrder::create(['order_number' => $prefix.'-'.str()->upper(str()->random(8)), 'reservation_id' => $reservation->id, 'user_id' => $userId, 'category' => $category, 'status' => 'new', 'notes' => $notes, 'delivery_location' => $deliveryLocation ?: 'Room '.$reservation->room->room_number, 'requested_at' => now()]);
            $order->items()->create(['service_id' => $service->id, 'description' => $service->name, 'quantity' => $quantity, 'unit_price' => $price->price, 'total' => $total, 'notes' => $notes]);
            FolioTransaction::create(['reservation_id' => $reservation->id, 'service_order_id' => $order->id, 'type' => 'service_charge', 'description' => $service->name.' x '.$quantity, 'amount' => $total, 'status' => 'posted', 'posted_at' => now()]);

            return $order->load(['items', 'reservation.room']);
        });
    }
}
