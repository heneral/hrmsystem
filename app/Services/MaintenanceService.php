<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    public function open(MaintenanceRequest $request): MaintenanceRequest
    {
        return DB::transaction(function () use ($request) {
            app(RoomStatusService::class)->transition($request->room, $request->priority === 'critical' ? RoomStatus::OUT_OF_ORDER : RoomStatus::MAINTENANCE);
            $request->update(['status' => 'open']);

            return $request->refresh();
        });
    }

    public function resolve(MaintenanceRequest $request): MaintenanceRequest
    {
        return DB::transaction(function () use ($request) {
            $request->update(['status' => 'resolved', 'completed_at' => now()]);
            app(RoomStatusService::class)->transition($request->room, RoomStatus::AVAILABLE);

            return $request->refresh();
        });
    }
}