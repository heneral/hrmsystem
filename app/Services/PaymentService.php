<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Events\PaymentRecorded;
use App\Notifications\PaymentRecordedNotification;
use App\Contracts\PaymentGateway;

class PaymentService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    public function record(Reservation $reservation, float $amount, string $method = 'manual', string $gateway = 'manual', ?string $transactionId = null): Payment
    {
        return DB::transaction(function () use ($reservation, $amount, $method, $gateway, $transactionId) {
            $paid = (float) $reservation->payments()->whereIn('status', [PaymentStatus::PAID, PaymentStatus::PARTIALLY_REFUNDED])->sum('amount');
            $folioCharges = (float) $reservation->folioTransactions()->where('status', 'posted')->sum('amount');
            $balance = (float) $reservation->total + $folioCharges - $paid;
            if ($amount <= 0 || $amount > $balance) {
                throw ValidationException::withMessages(['amount' => 'Payment exceeds the outstanding reservation balance.']);
            }
            $charge = $this->gateway->charge($amount, 'USD', $method, $transactionId);
            $status = $charge['status'] === 'paid' ? PaymentStatus::PAID : PaymentStatus::FAILED;
            $payment = $reservation->payments()->create(['amount' => $amount, 'currency' => 'USD', 'payment_method' => $method, 'gateway' => $gateway, 'transaction_id' => $charge['reference'], 'status' => $status, 'paid_at' => $status === PaymentStatus::PAID ? now() : null]);
            $payment->transactions()->create(['type' => 'payment', 'amount' => $amount, 'provider_reference' => $charge['reference'], 'payload' => $charge]);
            if ($status === PaymentStatus::FAILED) {
                throw ValidationException::withMessages(['payment' => $charge['message'] ?? 'The payment gateway declined the payment.']);
            }
            $reservation->loadMissing('customer');
            $reservation->customer?->notify(new PaymentRecordedNotification($payment->load('reservation')));
            PaymentRecorded::dispatch($payment->load('reservation'));

            return $payment;
        });
    }

    public function refund(Payment $payment, float $amount): Payment
    {
        return DB::transaction(function () use ($payment, $amount) {
            $remaining = (float) $payment->amount - (float) $payment->refunded_amount;
            if ($amount <= 0 || $amount > $remaining) {
                throw ValidationException::withMessages(['amount' => 'Refund exceeds the refundable payment balance.']);
            }
            $refund = $this->gateway->refund((string) $payment->transaction_id, $amount);
            $refunded = (float) $payment->refunded_amount + $amount;
            $payment->update(['refunded_amount' => $refunded, 'refunded_at' => now(), 'status' => $refunded == (float) $payment->amount ? PaymentStatus::REFUNDED : PaymentStatus::PARTIALLY_REFUNDED]);
            $payment->transactions()->create(['type' => 'refund', 'amount' => $amount, 'provider_reference' => $refund['reference'], 'payload' => $refund]);

            return $payment->refresh();
        });
    }
}