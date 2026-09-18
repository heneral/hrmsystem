<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->hasRole('front-desk')) {
            return redirect()->route('frontdesk.dashboard');
        }

        if ($user->hasPermission('reservations.manage')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasPermission('housekeeping.manage') || $user->hasPermission('maintenance.manage')) {
            return redirect()->route('admin.operations');
        }

        $reservations = Reservation::with(['room.roomType', 'payments'])
            ->where('user_id', $user->id)
            ->latest('check_in')
            ->get();
        $upcoming = $reservations->filter(fn (Reservation $reservation) => in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED], true) && $reservation->check_in->greaterThanOrEqualTo(today()));
        $current = $reservations->first(fn (Reservation $reservation) => $reservation->status === ReservationStatus::CHECKED_IN);
        $paid = $reservations->flatMap->payments->where('status', 'paid')->sum('amount');

        return view('customer.dashboard', compact('reservations', 'upcoming', 'current', 'paid'));
    }
}