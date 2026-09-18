<?php

namespace App\Enums;

enum HousekeepingStatus: string
{
    case CLEAN = 'clean';
    case DIRTY = 'dirty';
    case CLEANING = 'cleaning';
}