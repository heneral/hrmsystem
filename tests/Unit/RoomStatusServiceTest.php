<?php

namespace Tests\Unit;

use App\Enums\RoomStatus;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\RoomStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RoomStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_room_can_move_to_dirty_then_clean(): void
    {
        $room = $this->room(RoomStatus::OCCUPIED);
        $service = app(RoomStatusService::class);

        $service->transition($room, RoomStatus::DIRTY);
        $service->transition($room->refresh(), RoomStatus::CLEANING);
        $room = $service->transition($room->refresh(), RoomStatus::CLEAN);

        $this->assertSame(RoomStatus::CLEAN, $room->status);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        app(RoomStatusService::class)->transition($this->room(RoomStatus::OCCUPIED), RoomStatus::AVAILABLE);
    }

    private function room(RoomStatus $status): Room
    {
        $hotel = Hotel::create(['name' => 'Status Hotel', 'slug' => fake()->unique()->slug()]);
        $type = RoomType::create(['hotel_id' => $hotel->id, 'name' => 'Status Room', 'slug' => fake()->unique()->slug(), 'base_price' => 100, 'max_adults' => 2, 'max_children' => 0, 'max_occupancy' => 2]);
        return Room::create(['room_type_id' => $type->id, 'room_number' => fake()->unique()->numerify('###'), 'status' => $status]);
    }
}
