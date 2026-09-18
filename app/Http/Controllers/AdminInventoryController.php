<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\StoreRoomImageRequest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\RoomImage;

class AdminInventoryController extends Controller
{
    public function roomTypes(): View
    {
        Gate::authorize('manage', RoomType::class);
        return view('admin/inventory/room-types', ['roomTypes' => RoomType::with('hotel')->withCount('rooms')->latest()->paginate(20), 'hotels' => Hotel::where('is_active', true)->orderBy('name')->get(), 'roomTypeCounts' => ['total' => RoomType::count(), 'active' => RoomType::where('is_active', true)->count(), 'rooms' => Room::count()]]);
    }

    public function storeRoomType(StoreRoomTypeRequest $request): RedirectResponse
    {
        RoomType::create($request->validated());
        return back()->with('status', 'Room type created.');
    }

    public function updateRoomType(StoreRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        Gate::authorize('update', $roomType);
        $roomType->update($request->validated());
        return back()->with('status', 'Room type updated.');
    }

    public function storeRoomImage(StoreRoomImageRequest $request, RoomType $roomType): RedirectResponse
    {
        Gate::authorize('manage', RoomType::class);
        $path = $request->file('image')->store('room-images', 'public');
        $roomType->images()->create(['path' => $path, 'alt_text' => $request->input('alt_text'), 'is_primary' => $roomType->images()->doesntExist()]);

        return back()->with('status', 'Room image uploaded.');
    }

    public function rooms(): View
    {
        Gate::authorize('manage', Room::class);
        $roomCounts = Room::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        return view('admin/inventory/rooms', ['rooms' => Room::with('roomType')->orderBy('room_number')->paginate(30), 'roomTypes' => RoomType::where('is_active', true)->orderBy('name')->get(), 'statuses' => RoomStatus::cases(), 'roomCounts' => $roomCounts]);
    }

    public function storeRoom(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());
        return back()->with('status', 'Room created.');
    }

    public function updateRoom(StoreRoomRequest $request, Room $room): RedirectResponse
    {
        Gate::authorize('update', $room);
        $room->update($request->validated());
        return back()->with('status', 'Room updated.');
    }
}