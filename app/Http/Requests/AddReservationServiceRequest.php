<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddReservationServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('front-desk') ?? false;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'delivery_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
