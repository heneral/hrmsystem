<?php

namespace Tests\Unit;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_boundary_is_available_for_the_next_stay(): void
    {
        $room = $this->createRoom(maxAdults: 2, maxChildren: 1);
        $this->createReservation($room, '2026-09-20', '2026-09-25');

        $available = app(AvailabilityService::class)->search(
            Carbon::parse('2026-09-25'),
            Carbon::parse('2026-09-28'),
            adults: 2,
        )->pluck('id');

        $this->assertTrue($available->contains($room->id));
    }

    public function test_overlapping_stay_is_not_available(): void
    {
        $room = $this->createRoom(maxAdults: 2, maxChildren: 1);
        $this->createReservation($room, '2026-09-20', '2026-09-25');

        $available = app(AvailabilityService::class)->search(
            Carbon::parse('2026-09-24'),
            Carbon::parse('2026-09-28'),
            adults: 2,
        )->pluck('id');

        $this->assertFalse($available->contains($room->id));
    }

    public function test_room_capacity_is_enforced(): void
    {
        $room = $this->createRoom(maxAdults: 2, maxChildren: 0);

        $available = app(AvailabilityService::class)->search(
            Carbon::parse('2026-10-01'),
            Carbon::parse('2026-10-03'),
            adults: 2,
            children: 1,
        )->pluck('id');

        $this->assertFalse($available->contains($room->id));
    }

    private function createRoom(int $maxAdults, int $maxChildren): Room
    {
        $hotel = Hotel::create(['name' => 'Test Hotel', 'slug' => fake()->unique()->slug()]);
        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room',
            'slug' => fake()->unique()->slug(),
            'base_price' => 100,
            'max_adults' => $maxAdults,
            'max_children' => $maxChildren,
            'max_occupancy' => $maxAdults + $maxChildren,
        ]);

        return Room::create([
            'room_type_id' => $roomType->id,
            'room_number' => fake()->unique()->numerify('###'),
            'status' => RoomStatus::RESERVED,
        ]);
    }

    private function createReservation(Room $room, string $checkIn, string $checkOut): Reservation
    {
        $user = User::factory()->create();
        $guest = Guest::create(['first_name' => 'Test', 'last_name' => 'Guest']);

        $reservation = Reservation::create([
            'reservation_number' => fake()->unique()->bothify('HH-##########'),
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => 2,
            'number_of_nights' => 5,
            'rate' => 100,
            'subtotal' => 500,
            'taxes' => 60,
            'total' => 560,
            'status' => ReservationStatus::CONFIRMED,
        ]);
        $reservation->guests()->attach($guest->id, ['is_primary' => true]);

        return $reservation;
    }
}