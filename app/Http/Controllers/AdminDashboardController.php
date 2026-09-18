<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\Reservation;
use App\Models\Room;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $today = today();
        $metrics = [
            'arrivals' => Reservation::whereDate('check_in', $today)->where('status', 'confirmed')->count(),
            'departures' => Reservation::whereDate('check_out', $today)->where('status', 'checked_in')->count(),
            'occupancy' => Room::where('status', RoomStatus::OCCUPIED->value)->count(),
            'available' => Room::whereIn('status', [RoomStatus::AVAILABLE->value, RoomStatus::CLEAN->value])->count(),
            'maintenance' => Room::whereIn('status', [RoomStatus::MAINTENANCE->value, RoomStatus::OUT_OF_ORDER->value])->count(),
            'upcoming' => Reservation::whereDate('check_in', '>=', $today)->whereIn('status', ['pending', 'confirmed'])->count(),
            'reservations' => Reservation::count(),
            'revenue_month' => Reservation::whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->sum('total'),
        ];
        $recentReservations = Reservation::with(['room.roomType', 'customer'])->latest()->limit(8)->get();
        $reservationTrend = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = today()->subDays($daysAgo);

            return ['label' => $date->format('D'), 'count' => Reservation::whereDate('created_at', $date)->count(), 'revenue' => (float) Reservation::whereDate('created_at', $date)->sum('total')];
        });
        $roomStatus = collect([
            ['label' => 'Available', 'value' => Room::whereIn('status', [RoomStatus::AVAILABLE->value, RoomStatus::CLEAN->value])->count(), 'color' => 'bg-emerald-500'],
            ['label' => 'Occupied', 'value' => Room::where('status', RoomStatus::OCCUPIED->value)->count(), 'color' => 'bg-blue-500'],
            ['label' => 'Maintenance', 'value' => Room::whereIn('status', [RoomStatus::MAINTENANCE->value, RoomStatus::OUT_OF_ORDER->value])->count(), 'color' => 'bg-rose-500'],
            ['label' => 'Reserved', 'value' => Room::where('status', RoomStatus::RESERVED->value)->count(), 'color' => 'bg-amber-500'],
        ]);

        return view('admin.dashboard', compact('metrics', 'recentReservations', 'reservationTrend', 'roomStatus'));
    }
}