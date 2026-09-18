<?php

namespace App\Enums;

enum ServicePricingUnit: string
{
    case PER_BOOKING = 'per_booking';
    case PER_NIGHT = 'per_night';
    case PER_GUEST = 'per_guest';
    case PER_ITEM = 'per_item';
}