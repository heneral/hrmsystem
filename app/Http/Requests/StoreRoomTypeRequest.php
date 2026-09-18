<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('rooms.manage') ?? false;
    }

    public function rules(): array
    {
        return ['hotel_id' => ['required', 'exists:hotels,id'], 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', 'max:120'], 'description' => ['nullable', 'string'], 'base_price' => ['required', 'numeric', 'min:0'], 'max_adults' => ['required', 'integer', 'min:1', 'max:20'], 'max_children' => ['required', 'integer', 'min:0', 'max:20'], 'max_occupancy' => ['required', 'integer', 'min:1', 'max:40'], 'bed_configuration' => ['nullable', 'string', 'max:120'], 'size_sqm' => ['nullable', 'numeric', 'min:0'], 'is_active' => ['sometimes', 'boolean']];
    }
}