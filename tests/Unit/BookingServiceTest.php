<?php

namespace Tests\Unit;

use App\Enums\RoomStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_booking_for_the_same_dates_is_rejected(): void
    {
        $room = $this->createRoom();
        $firstCustomer = User::factory()->create();
        $secondCustomer = User::factory()->create();
        $firstGuest = Guest::create(['first_name' => 'First', 'last_name' => 'Guest']);
        $secondGuest = Guest::create(['first_name' => 'Second', 'last_name' => 'Guest']);
        $bookingService = app(BookingService::class);

        $bookingService->create($firstCustomer, $room, $firstGuest, Carbon::parse('2026-11-10'), Carbon::parse('2026-11-12'), 2);

        try {
            $bookingService->create($secondCustomer, $room, $secondGuest, Carbon::parse('2026-11-10'), Carbon::parse('2026-11-12'), 2);
            $this->fail('A duplicate booking should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('room', $exception->errors());
        }

        $this->assertSame(RoomStatus::RESERVED, $room->fresh()->status);
    }

    private function createRoom(): Room
    {
        $hotel = Hotel::create(['name' => 'Booking Test Hotel', 'slug' => fake()->unique()->slug()]);
        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Booking Test Room',
            'slug' => fake()->unique()->slug(),
            'base_price' => 150,
            'max_adults' => 2,
            'max_children' => 1,
            'max_occupancy' => 3,
        ]);

        return Room::create([
            'room_type_id' => $roomType->id,
            'room_number' => fake()->unique()->numerify('###'),
        ]);
    }
}