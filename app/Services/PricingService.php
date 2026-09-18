<?php

namespace App\Services;

use App\Models\RoomType;
use Illuminate\Support\Carbon;

class PricingService
{
    public function calculate(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights = $checkIn->diffInDays($checkOut);
        $roomRates = $roomType->roomRates()->whereHas('ratePlan', fn ($query) => $query->where('is_active', true))->where('starts_on', '<=', $checkOut->copy()->subDay()->toDateString())->where('ends_on', '>=', $checkIn->toDateString())->get();
        $nightlyRates = collect();

        for ($night = $checkIn->copy(); $night->lt($checkOut); $night->addDay()) {
            $configuredRate = $roomRates->first(fn ($roomRate) => $night->betweenIncluded($roomRate->starts_on, $roomRate->ends_on));
            $rate = $configuredRate ? (float) $configuredRate->price : (float) $roomType->base_price;
            if ($night->isWeekend() && ! $configuredRate) {
                $rate = round($rate * 1.1, 2);
            }
            $nightlyRates->push($rate);
        }

        $subtotal = $nightlyRates->sum();
        $rate = $nights > 0 ? round($subtotal / $nights, 2) : 0;
        $taxes = round($subtotal * 0.12, 2);

        return [
            'number_of_nights' => $nights,
            'rate' => $rate,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'discounts' => 0,
            'total' => $subtotal + $taxes,
        ];
    }
}