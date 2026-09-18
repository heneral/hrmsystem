<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\MaintenanceRequest;
use App\Models\Reservation;
use App\Services\HousekeepingService;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function __construct(private readonly HousekeepingService $housekeeping, private readonly MaintenanceService $maintenance) {}

    public function calendar(Request $request)
    {
        abort_unless($request->user()->hasPermission('reservations.manage'), 403);
        $reservations = Reservation::with(['room.roomType', 'customer'])->when($request->date, fn ($q, $date) => $q->whereDate('check_in', '<=', $date)->whereDate('check_out', '>=', $date))->get();

        return response()->json(['data' => $reservations]);
    }

    public function housekeeping(Request $request)
    {
        abort_unless($request->user()->hasPermission('housekeeping.manage') || $request->user()->hasPermission('maintenance.manage'), 403);
        return response()->json(['data' => HousekeepingTask::with('room')->latest()->paginate(50)]);
    }

    public function startHousekeeping(Request $request, HousekeepingTask $task)
    {
        abort_unless($request->user()->hasPermission('housekeeping.manage') || $request->user()->hasPermission('maintenance.manage'), 403);
        return response()->json(['data' => $this->housekeeping->start($task->load('room'))]);
    }

    public function completeHousekeeping(Request $request, HousekeepingTask $task)
    {
        abort_unless($request->user()->hasPermission('housekeeping.manage'), 403);
        return response()->json(['data' => $this->housekeeping->complete($task->load('room'), $request->input('notes'))]);
    }

    public function maintenance(Request $request)
    {
        abort_unless($request->user()->hasPermission('housekeeping.manage') || $request->user()->hasPermission('maintenance.manage'), 403);
        return response()->json(['data' => MaintenanceRequest::with('room')->latest()->paginate(50)]);
    }

    public function resolveMaintenance(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($request->user()->hasPermission('housekeeping.manage') || $request->user()->hasPermission('maintenance.manage'), 403);
        return response()->json(['data' => $this->maintenance->resolve($maintenanceRequest->load('room'))]);
    }
}