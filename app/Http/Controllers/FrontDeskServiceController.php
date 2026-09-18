<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddReservationServiceRequest;
use App\Models\HotelService;
use App\Models\Reservation;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FrontDeskServiceController extends Controller
{
    public function __construct(private readonly ServiceOrderService $orders) {}

    public function index(): View
    {
        $reservations = Reservation::with(['room', 'customer', 'items', 'payments'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest('check_in')
            ->paginate(12);
        $services = HotelService::with(['prices' => fn ($query) => $query->whereDate('starts_on', '<=', today())->whereDate('ends_on', '>=', today())])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $serviceGroups = $services->groupBy('category');

        $orders = ServiceOrder::with(['reservation.room', 'items'])->latest('requested_at')->limit(20)->get();

        return view('frontdesk.services.index', compact('reservations', 'services', 'serviceGroups', 'orders'));
    }

    public function store(AddReservationServiceRequest $request, Reservation $reservation): RedirectResponse
    {
        $service = HotelService::where('is_active', true)->findOrFail($request->integer('service_id'));
        $order = $this->orders->create($reservation->load('room'), $service, $request->integer('quantity'), $request->user()->id, $request->input('notes'), $request->input('delivery_location'));

        return back()->with('status', "Service request {$order->order_number} created and charged to room {$reservation->room->room_number}.");
    }
}
