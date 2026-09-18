<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Reservation $reservation) {}

    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('HotelHub reservation confirmed')->greeting('Your stay is confirmed.')->line("Confirmation: {$this->reservation->reservation_number}")->line("Check-in: {$this->reservation->check_in->format('M d, Y')}")->line("Check-out: {$this->reservation->check_out->format('M d, Y')}");
    }

    public function toArray(object $notifiable): array
    {
        return ['reservation_id' => $this->reservation->id, 'reservation_number' => $this->reservation->reservation_number, 'message' => 'Your reservation is confirmed.'];
    }
}