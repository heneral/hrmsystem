<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('hotel.manage') ?? false; }
    public function rules(): array { return ['code' => ['required', 'string', 'max:40'], 'description' => ['nullable', 'string'], 'discount_type' => ['required', 'in:percentage,fixed'], 'discount_value' => ['required', 'numeric', 'min:0'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'minimum_amount' => ['nullable', 'numeric', 'min:0'], 'usage_limit' => ['nullable', 'integer', 'min:1'], 'per_customer_limit' => ['nullable', 'integer', 'min:1']]; }
}