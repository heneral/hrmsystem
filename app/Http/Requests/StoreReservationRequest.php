<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return ['room_id' => ['required', 'integer', 'exists:rooms,id'], 'check_in' => ['required', 'date', 'after_or_equal:today'], 'check_out' => ['required', 'date', 'after:check_in'], 'adults' => ['required', 'integer', 'min:1', 'max:20'], 'children' => ['nullable', 'integer', 'min:0', 'max:20'], 'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'special_requests' => ['nullable', 'string', 'max:2000'], 'coupon_code' => ['nullable', 'string', 'max:40'], 'services' => ['nullable', 'array'], 'services.*' => ['integer', 'exists:services,id']];
    }
}