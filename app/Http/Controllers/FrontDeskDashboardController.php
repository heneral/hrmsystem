<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ServiceOrder;
use Illuminate\View\View;

class FrontDeskDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $arrivals = Reservation::with(['room.roomType', 'customer', 'guests', 'payments'])
            ->whereDate('check_in', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in')
            ->get();
        $departures = Reservation::with(['room.roomType', 'customer', 'payments'])
            ->whereDate('check_out', $today)
            ->where('status', 'checked_in')
            ->orderBy('check_out')
            ->get();
        $currentGuests = Reservation::where('status', 'checked_in')->count();
        $availableRooms = Room::whereIn('status', [RoomStatus::AVAILABLE->value, RoomStatus::CLEAN->value])->count();
        $pendingReservations = Reservation::whereIn('status', ['pending', 'confirmed'])->count();
        $pendingCheckIns = Reservation::whereDate('check_in', $today)->where('status', 'confirmed')->count();
        $unpaidBalances = Reservation::query()
            ->withSum(['payments as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get()
            ->filter(fn ($reservation) => (float) $reservation->total > (float) ($reservation->paid_total ?? 0))
            ->count();
        $housekeepingPending = HousekeepingTask::whereIn('status', ['pending', 'in_progress'])->count();
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        $recentReservations = Reservation::with(['room', 'customer'])->latest()->limit(8)->get();
        $serviceRequests = ServiceOrder::with(['reservation.room', 'items'])->latest('requested_at')->limit(5)->get();
        $serviceCounts = ServiceOrder::query()->whereIn('status', ['new', 'preparing', 'ready'])->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $metrics = [
            ['label' => "Today's arrivals", 'value' => $arrivals->count(), 'tone' => 'amber'],
            ['label' => "Today's departures", 'value' => $departures->count(), 'tone' => 'rose'],
            ['label' => 'Current guests', 'value' => $currentGuests, 'tone' => 'blue'],
            ['label' => 'Available rooms', 'value' => $availableRooms, 'tone' => 'emerald'],
            ['label' => 'Pending reservations', 'value' => $pendingReservations, 'tone' => 'violet'],
            ['label' => 'Pending check-ins', 'value' => $pendingCheckIns, 'tone' => 'cyan'],
            ['label' => 'Unpaid balances', 'value' => $unpaidBalances, 'tone' => 'orange'],
            ['label' => 'Housekeeping pending', 'value' => $housekeepingPending, 'tone' => 'slate'],
        ];

        return view('frontdesk.dashboard', compact('arrivals', 'departures', 'currentGuests', 'availableRooms', 'rooms', 'recentReservations', 'metrics', 'serviceRequests', 'serviceCounts'));
    }
}
