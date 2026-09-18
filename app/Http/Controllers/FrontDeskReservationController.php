<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = Reservation::query()
            ->with(['room.roomType', 'customer', 'guests', 'payments'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where(function ($query) use ($search) {
                    $query->where('reservation_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('guests', fn ($query) => $query->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhereHas('room', fn ($query) => $query->where('room_number', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->string('payment_status')->toString()))
            ->when($request->filled('room_type'), fn ($query) => $query->whereHas('room', fn ($query) => $query->where('room_type_id', $request->integer('room_type'))))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('check_in', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('check_out', '<=', $request->date('to')))
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        return view('frontdesk.reservations.index', ['reservations' => $reservations, 'roomTypes' => RoomType::where('is_active', true)->orderBy('name')->get()]);
    }
}
