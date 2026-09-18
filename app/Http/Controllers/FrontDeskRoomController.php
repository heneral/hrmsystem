<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\View\View;

class FrontDeskRoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with(['roomType', 'reservations' => fn ($query) => $query->where('status', 'checked_in')->latest()->limit(1)])->orderBy('room_number')->get();

        return view('frontdesk.rooms.board', compact('rooms'));
    }
}
