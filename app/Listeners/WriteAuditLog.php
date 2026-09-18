<?php

namespace App\Listeners;

use App\Events\PaymentRecorded;
use App\Events\ReservationCancelled;
use App\Events\ReservationCreated;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteAuditLog implements ShouldQueue
{
    public function __construct(private readonly AuditService $audit) {}

    public function handle(ReservationCreated|ReservationCancelled|PaymentRecorded $event): void
    {
        $entity = match (true) {
            $event instanceof PaymentRecorded => $event->payment,
            default => $event->reservation,
        };
        $action = match (true) {
            $event instanceof ReservationCreated => 'reservation.created',
            $event instanceof ReservationCancelled => 'reservation.cancelled',
            default => 'payment.recorded',
        };
        $userId = $entity->user_id ?? $entity->reservation?->user_id;
        $this->audit->record($userId, $action, $entity, [], ['status' => $entity->status?->value ?? null]);
    }
}