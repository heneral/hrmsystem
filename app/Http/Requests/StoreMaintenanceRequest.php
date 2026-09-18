<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasPermission('maintenance.manage') || $this->user()?->hasPermission('housekeeping.manage') || false; }
    public function rules(): array { return ['room_id' => ['required', 'exists:rooms,id'], 'description' => ['required', 'string', 'max:2000'], 'priority' => ['required', 'in:low,normal,high,critical']]; }
}