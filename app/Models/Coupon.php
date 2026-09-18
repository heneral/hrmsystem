<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description', 'discount_type', 'discount_value', 'starts_on', 'ends_on', 'usage_limit', 'per_customer_limit', 'minimum_amount', 'is_active'];

    protected function casts(): array
    {
        return ['discount_value' => 'decimal:2', 'starts_on' => 'date', 'ends_on' => 'date', 'minimum_amount' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}