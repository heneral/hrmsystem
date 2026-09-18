<?php

namespace App\Models;

use App\Enums\ServicePricingUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelService extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = ['name', 'description', 'category', 'pricing_unit', 'is_active'];

    protected function casts(): array
    {
        return ['pricing_unit' => ServicePricingUnit::class, 'is_active' => 'boolean'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ServicePrice::class, 'service_id');
    }
}