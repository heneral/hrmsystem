<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Services\AvailabilityService;
use Illuminate\Support\Carbon;
use App\Models\HotelService;

class AvailabilityController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function index(AvailabilityRequest $request)
    {
        $rooms = $this->availability->search(Carbon::parse($request->check_in), Carbon::parse($request->check_out), $request->integer('adults'), $request->integer('children'))->get();

        $services = HotelService::with('prices')->where('is_active', true)->orderBy('name')->get();

        return $request->expectsJson() ? response()->json(['data' => $rooms, 'services' => $services]) : view('booking.rooms', compact('rooms', 'services'));
    }
}