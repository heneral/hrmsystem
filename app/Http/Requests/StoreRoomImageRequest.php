<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('rooms.manage') ?? false;
    }

    public function rules(): array
    {
        return ['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'alt_text' => ['nullable', 'string', 'max:160']];
    }
}