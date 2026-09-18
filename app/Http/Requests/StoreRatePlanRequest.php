<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatePlanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('hotel.manage') ?? false; }
    public function rules(): array { return ['name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'alpha_dash', 'max:100'], 'room_type_id' => ['required', 'exists:room_types,id'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after_or_equal:starts_on'], 'price' => ['required', 'numeric', 'min:0'], 'minimum_stay' => ['nullable', 'integer', 'min:1']]; }
}