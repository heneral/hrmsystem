<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $hotel = Hotel::with(['roomTypes' => fn ($query) => $query->where('is_active', true)->with(['amenities', 'images'])->withCount('rooms')])->where('is_active', true)->firstOrFail();
        $services = HotelService::where('is_active', true)->with('prices')->orderBy('name')->get();

        return view('welcome', compact('hotel', 'services'));
    }
}