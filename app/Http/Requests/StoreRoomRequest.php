<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('rooms.manage') ?? false;
    }

    public function rules(): array
    {
        return ['room_type_id' => ['required', 'exists:room_types,id'], 'room_number' => ['required', 'string', 'max:20'], 'floor' => ['nullable', 'integer', 'min:0'], 'status' => ['required', 'in:available,reserved,occupied,dirty,cleaning,clean,maintenance,out_of_order'], 'is_active' => ['sometimes', 'boolean']];
    }
}