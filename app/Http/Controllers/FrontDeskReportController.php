<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskReportController extends Controller
{
    public function daily(Request $request): View
    {
        $date = $request->date('date') ?? today();
        $arrivals = Reservation::whereDate('check_in', $date)->count();
        $departures = Reservation::whereDate('check_out', $date)->count();
        $newReservations = Reservation::whereDate('created_at', $date)->count();
        $cancelled = Reservation::whereDate('cancelled_at', $date)->count();
        $checkedIn = Reservation::whereDate('updated_at', $date)->where('status', 'checked_in')->count();
        $checkedOut = Reservation::whereDate('updated_at', $date)->where('status', 'checked_out')->count();
        $paymentsCollected = (float) Payment::whereDate('paid_at', $date)->whereIn('status', ['paid', 'partially_refunded'])->sum('amount');
        $outstandingBalances = Reservation::withSum(['payments as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get()
            ->sum(fn ($reservation) => max(0, (float) $reservation->total - (float) ($reservation->paid_total ?? 0)));
        $metrics = compact('arrivals', 'departures', 'newReservations', 'cancelled', 'checkedIn', 'checkedOut', 'paymentsCollected', 'outstandingBalances');

        return view('frontdesk.reports.daily', compact('date', 'metrics'));
    }
}
