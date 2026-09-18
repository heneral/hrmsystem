<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('hotel.manage') ?? false; }
    public function rules(): array { return ['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string'], 'pricing_unit' => ['required', 'in:per_booking,per_night,per_guest,per_item'], 'price' => ['required', 'numeric', 'min:0']]; }
}