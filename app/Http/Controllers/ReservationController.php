<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\ReservationWorkflowService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private readonly BookingService $booking, private readonly ReservationWorkflowService $workflow) {}

    public function store(StoreReservationRequest $request)
    {
        $guest = Guest::create($request->safe()->only(['first_name', 'last_name', 'email', 'phone']));
        $reservation = $this->booking->create($request->user(), Room::findOrFail($request->integer('room_id')), $guest, Carbon::parse($request->check_in), Carbon::parse($request->check_out), $request->integer('adults'), $request->integer('children'), $request->input('special_requests'), $request->input('services', []), $request->input('coupon_code'));

        return $request->expectsJson() ? response()->json(['data' => $reservation], 201) : redirect()->route('reservations.show', $reservation);
    }

    public function show(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id || $request->user()->hasPermission('reservations.manage'), 403);

        return view('booking.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id || $request->user()->hasPermission('reservations.manage'), 403);
        $this->workflow->cancel($reservation, $request->input('reason'));

        return back()->with('status', 'Reservation cancelled.');
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $reservation = $this->workflow->modify($reservation, $request->integer('room_id'), Carbon::parse($request->check_in), Carbon::parse($request->check_out), $request->integer('adults'), $request->integer('children'), $request->input('special_requests'));

        return $request->expectsJson() ? response()->json(['data' => $reservation]) : redirect()->route('reservations.show', $reservation)->with('status', 'Reservation updated.');
    }
}