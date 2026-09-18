<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRecordedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Payment $payment) {}

    public function via(object $notifiable): array { return ['mail', 'database']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('HotelHub payment confirmation')->line('Your payment has been recorded.')->line('Amount: $'.number_format((float) $this->payment->amount, 2))->line('Reservation: '.$this->payment->reservation->reservation_number);
    }

    public function toArray(object $notifiable): array
    {
        return ['payment_id' => $this->payment->id, 'amount' => $this->payment->amount, 'message' => 'Payment recorded.'];
    }
}