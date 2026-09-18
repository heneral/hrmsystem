<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Models\HousekeepingTask;
use App\Models\MaintenanceRequest;
use App\Models\Reservation;
use App\Services\HousekeepingService;
use App\Services\MaintenanceService;
use App\Services\ReservationWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OperationsWorkflowController extends Controller
{
    public function __construct(private readonly HousekeepingService $housekeeping, private readonly MaintenanceService $maintenance, private readonly ReservationWorkflowService $reservations) {}

    public function checkIn(Reservation $reservation): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('reservations.manage'), 403);
        $this->reservations->checkIn($reservation->load('room'));
        return back()->with('status', "Room {$reservation->room->room_number} checked in.");
    }

    public function checkOut(Reservation $reservation): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('reservations.manage'), 403);
        $this->reservations->checkOut($reservation->load('room'));
        return back()->with('status', "Room {$reservation->room->room_number} checked out and added to housekeeping.");
    }

    public function reportMaintenance(StoreMaintenanceRequest $request): RedirectResponse
    {
        $issue = MaintenanceRequest::create(['room_id' => $request->integer('room_id'), 'reported_by' => $request->user()->id, 'description' => $request->input('description'), 'priority' => $request->input('priority'), 'status' => 'open']);
        $this->maintenance->open($issue->load('room'));
        return back()->with('status', 'Maintenance issue reported and room blocked.');
    }

    public function startHousekeeping(HousekeepingTask $task): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('housekeeping.manage'), 403);
        abort_if($task->task_type === 'daily' && $task->guest_consent !== 'accepted', 422, 'Ask the guest and record consent before starting daily cleaning.');
        $this->housekeeping->start($task->load('room'));
        return back()->with('status', "Room {$task->room->room_number} cleaning started.");
    }

    public function respondToDailyCleaning(Request $request, HousekeepingTask $task): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('housekeeping.manage'), 403);
        abort_unless($task->task_type === 'daily' && $task->status === 'pending', 422);
        $response = $request->validate(['response' => ['required', 'in:accepted,declined']])['response'];
        $task->update(['guest_consent' => $response, 'guest_contacted_at' => now(), 'notes' => $response === 'accepted' ? 'Guest agreed to daily cleaning.' : 'Guest declined daily cleaning.']);
        if ($response === 'accepted') {
            $this->housekeeping->start($task->load('room'));
            return back()->with('status', "Guest agreed. Room {$task->room->room_number} cleaning started.");
        }
        $task->update(['status' => 'declined', 'completed_at' => now()]);
        return back()->with('status', "Guest declined daily cleaning for room {$task->room->room_number}.");
    }

    public function completeHousekeeping(HousekeepingTask $task): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('housekeeping.manage'), 403);
        $this->housekeeping->complete($task->load('room'), request('notes'));
        return back()->with('status', "Room {$task->room->room_number} marked clean.");
    }

    public function resolveMaintenance(MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        abort_unless(request()->user()->hasPermission('housekeeping.manage') || request()->user()->hasPermission('maintenance.manage'), 403);
        $this->maintenance->resolve($maintenanceRequest->load('room'));
        return back()->with('status', "Room {$maintenanceRequest->room->room_number} maintenance resolved.");
    }
}