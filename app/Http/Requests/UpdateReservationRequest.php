<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('reservations.manage') || $this->route('reservation')?->user_id === $this->user()?->id;
    }

    public function rules(): array
    {
        return ['room_id' => ['required', 'integer', 'exists:rooms,id'], 'check_in' => ['required', 'date', 'after_or_equal:today'], 'check_out' => ['required', 'date', 'after:check_in'], 'adults' => ['required', 'integer', 'min:1', 'max:20'], 'children' => ['nullable', 'integer', 'min:0', 'max:20'], 'special_requests' => ['nullable', 'string', 'max:2000']];
    }
}