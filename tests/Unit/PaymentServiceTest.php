<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_can_be_recorded_and_partially_refunded(): void
    {
        $reservation = $this->reservation();
        $payment = app(PaymentService::class)->record($reservation, 50);
        $refunded = app(PaymentService::class)->refund($payment, 20);

        $this->assertSame(PaymentStatus::PARTIALLY_REFUNDED, $refunded->status);
        $this->assertSame(20.0, (float) $refunded->refunded_amount);
    }

    public function test_payment_above_balance_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        app(PaymentService::class)->record($this->reservation(), 101);
    }

    public function test_test_gateway_can_decline_a_payment(): void
    {
        $this->expectException(ValidationException::class);
        app(PaymentService::class)->record($this->reservation(), 50, 'card', 'test', 'test_fail');
    }

    private function reservation(): Reservation
    {
        $hotel = Hotel::create(['name' => 'Payment Hotel', 'slug' => fake()->unique()->slug()]);
        $type = RoomType::create(['hotel_id' => $hotel->id, 'name' => 'Payment Room', 'slug' => fake()->unique()->slug(), 'base_price' => 100, 'max_adults' => 2, 'max_children' => 0, 'max_occupancy' => 2]);
        $room = Room::create(['room_type_id' => $type->id, 'room_number' => fake()->unique()->numerify('###')]);
        return Reservation::create(['reservation_number' => fake()->unique()->bothify('HH-##########'), 'user_id' => User::factory()->create()->id, 'room_id' => $room->id, 'check_in' => today()->addDay(), 'check_out' => today()->addDays(2), 'adults' => 1, 'number_of_nights' => 1, 'rate' => 100, 'subtotal' => 100, 'taxes' => 0, 'total' => 100, 'status' => 'confirmed']);
    }
}
