<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\HousekeepingTask;
use Illuminate\Support\Facades\DB;

class HousekeepingService
{
    public function start(HousekeepingTask $task): HousekeepingTask
    {
        return DB::transaction(function () use ($task) {
            app(RoomStatusService::class)->transition($task->room, RoomStatus::CLEANING);
            $task->update(['status' => 'in_progress', 'started_at' => now()]);

            return $task->refresh();
        });
    }

    public function complete(HousekeepingTask $task, ?string $notes = null): HousekeepingTask
    {
        return DB::transaction(function () use ($task, $notes) {
            app(RoomStatusService::class)->transition($task->room, $task->task_type === 'daily' ? RoomStatus::OCCUPIED : RoomStatus::CLEAN);
            $task->update(['status' => 'completed', 'completed_at' => now(), 'notes' => $notes ?? $task->notes]);

            return $task->refresh();
        });
    }
}