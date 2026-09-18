<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function validate(string $code, User $customer, float $amount, ?Carbon $date = null): Coupon
    {
        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();
        $date ??= now();

        if (! $coupon || ! $coupon->is_active || $date->lt($coupon->starts_on) || $date->gt($coupon->ends_on)) {
            throw ValidationException::withMessages(['coupon' => 'This coupon is invalid or expired.']);
        }
        if ($amount < (float) $coupon->minimum_amount) {
            throw ValidationException::withMessages(['coupon' => 'The booking total does not meet this coupon minimum.']);
        }
        if ($coupon->usage_limit !== null && $coupon->usages()->count() >= $coupon->usage_limit) {
            throw ValidationException::withMessages(['coupon' => 'This coupon has reached its usage limit.']);
        }
        if ($coupon->per_customer_limit !== null && $coupon->usages()->where('user_id', $customer->id)->count() >= $coupon->per_customer_limit) {
            throw ValidationException::withMessages(['coupon' => 'You have already used this coupon.']);
        }

        return $coupon;
    }

    public function discount(Coupon $coupon, float $amount): float
    {
        return round(min(
            $coupon->discount_type === 'percentage' ? $amount * ((float) $coupon->discount_value / 100) : (float) $coupon->discount_value,
            $amount,
        ), 2);
    }

    public function record(Coupon $coupon, User $customer, int $reservationId, float $discount): CouponUsage
    {
        return $coupon->usages()->create(['user_id' => $customer->id, 'reservation_id' => $reservationId, 'discount_amount' => $discount, 'used_at' => now()]);
    }
}