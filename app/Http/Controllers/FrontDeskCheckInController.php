<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\View\View;

class FrontDeskCheckInController extends Controller
{
    public function __invoke(): View
    {
        $reservations = Reservation::with(['room.roomType', 'customer', 'guests', 'payments'])
            ->whereDate('check_in', today())
            ->where('status', 'confirmed')
            ->orderBy('check_in')
            ->get();

        return view('frontdesk.checkin.index', compact('reservations'));
    }
}
