<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelService;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function about(): View
    {
        $hotel = Hotel::with(['roomTypes' => fn ($query) => $query->where('is_active', true)->with('amenities')])->where('is_active', true)->firstOrFail();

        return view('public.about', compact('hotel'));
    }

    public function contact(): View
    {
        $hotel = Hotel::where('is_active', true)->firstOrFail();
        $services = HotelService::where('is_active', true)->orderBy('name')->get();

        return view('public.contact', compact('hotel', 'services'));
    }
}
