<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\View\View;

class FrontDeskCheckOutController extends Controller
{
    public function __invoke(): View
    {
        $reservations = Reservation::with(['room.roomType', 'customer', 'payments', 'items', 'folioTransactions'])
            ->whereDate('check_out', today())
            ->where('status', 'checked_in')
            ->orderBy('check_out')
            ->get();

        return view('frontdesk.checkout.index', compact('reservations'));
    }
}
