<?php

namespace App\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case OCCUPIED = 'occupied';
    case DIRTY = 'dirty';
    case CLEANING = 'cleaning';
    case CLEAN = 'clean';
    case MAINTENANCE = 'maintenance';
    case OUT_OF_ORDER = 'out_of_order';
}