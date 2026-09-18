<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\HousekeepingTask;
use App\Models\MaintenanceRequest;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\View\View;

class OperationsDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $arrivals = Reservation::with(['room.roomType', 'guests'])->whereDate('check_in', $today)->where('status', 'confirmed')->orderBy('check_in')->get();
        $departures = Reservation::with(['room.roomType', 'customer'])->whereDate('check_out', $today)->where('status', 'checked_in')->orderBy('check_out')->get();
        $rooms = Room::with(['roomType', 'reservations' => fn ($query) => $query->where('status', 'checked_in')->latest()->limit(1)])->orderBy('room_number')->get();
        foreach ($rooms->where('status', RoomStatus::OCCUPIED) as $room) {
            $hasOpenTask = HousekeepingTask::where('room_id', $room->id)->whereIn('status', ['pending', 'in_progress'])->exists();
            $hasDailyTask = HousekeepingTask::where('room_id', $room->id)->where('task_type', 'daily')->whereDate('created_at', $today)->exists();
            if (! $hasOpenTask && ! $hasDailyTask) {
                HousekeepingTask::create(['room_id' => $room->id, 'reservation_id' => $room->reservations->first()?->id, 'task_type' => 'daily', 'status' => 'pending', 'priority' => 'normal', 'notes' => 'Daily guest room cleaning']);
            }
        }
        $housekeepingTasks = HousekeepingTask::with(['room', 'reservation.customer'])->whereIn('status', ['pending', 'in_progress'])->latest()->limit(8)->get();
        $maintenanceRequests = MaintenanceRequest::with('room')->whereIn('status', ['open', 'in_progress'])->latest()->limit(8)->get();

        $roomCounts = [
            'available' => $rooms->whereIn('status', [RoomStatus::AVAILABLE, RoomStatus::CLEAN])->count(),
            'occupied' => $rooms->where('status', RoomStatus::OCCUPIED)->count(),
            'dirty' => $rooms->where('status', RoomStatus::DIRTY)->count(),
            'maintenance' => $rooms->whereIn('status', [RoomStatus::MAINTENANCE, RoomStatus::OUT_OF_ORDER])->count(),
        ];

        return view('admin.operations', compact('arrivals', 'departures', 'rooms', 'housekeepingTasks', 'maintenanceRequests', 'roomCounts'));
    }
}