<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmenityRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('rooms.manage') ?? false; }
    public function rules(): array { return ['name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'alpha_dash', 'max:100'], 'description' => ['nullable', 'string']]; }
}