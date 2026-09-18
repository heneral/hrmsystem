<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('payments.manage') ?? false; }

    public function rules(): array
    {
        return ['amount' => ['required', 'numeric', 'gt:0'], 'payment_method' => ['required', 'string', 'max:40'], 'transaction_id' => ['nullable', 'string', 'max:120']];
    }
}