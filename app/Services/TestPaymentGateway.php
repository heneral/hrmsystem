<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Str;

class TestPaymentGateway implements PaymentGateway
{
    public function charge(float $amount, string $currency, ?string $paymentMethod, ?string $reference = null): array
    {
        if ($reference === 'test_fail' || $paymentMethod === 'declined') {
            return ['status' => 'failed', 'reference' => $reference ?: 'test-failed-'.Str::lower(Str::random(8)), 'message' => 'Test gateway declined the payment.'];
        }

        return ['status' => 'paid', 'reference' => $reference ?: 'test-'.Str::lower(Str::random(16))];
    }

    public function refund(string $transactionId, float $amount): array
    {
        return ['status' => 'refunded', 'reference' => 'refund-'.$transactionId.'-'.Str::lower(Str::random(8))];
    }
}