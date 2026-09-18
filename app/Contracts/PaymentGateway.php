<?php

namespace App\Contracts;

interface PaymentGateway
{
    public function charge(float $amount, string $currency, ?string $paymentMethod, ?string $reference = null): array;

    public function refund(string $transactionId, float $amount): array;
}