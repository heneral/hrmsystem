<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_percentage_coupon_discount_is_calculated(): void
    {
        $coupon = Coupon::create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'starts_on' => today()->subDay(), 'ends_on' => today()->addDay(), 'minimum_amount' => 50]);
        $service = app(CouponService::class);

        $this->assertSame(10.0, $service->discount($coupon, 100));
        $this->assertSame($coupon->id, $service->validate('save10', User::factory()->create(), 100, Carbon::today())->id);
    }

    public function test_coupon_below_minimum_is_rejected(): void
    {
        Coupon::create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'starts_on' => today()->subDay(), 'ends_on' => today()->addDay(), 'minimum_amount' => 100]);

        $this->expectException(ValidationException::class);
        app(CouponService::class)->validate('SAVE10', User::factory()->create(), 50, today());
    }
}
