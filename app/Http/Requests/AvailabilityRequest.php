<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['check_in' => ['required', 'date', 'after_or_equal:today'], 'check_out' => ['required', 'date', 'after:check_in'], 'adults' => ['required', 'integer', 'min:1', 'max:20'], 'children' => ['nullable', 'integer', 'min:0', 'max:20']];
    }
}