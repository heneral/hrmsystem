<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskGuestController extends Controller
{
    public function index(Request $request): View
    {
        $guests = Guest::query()
            ->withCount('reservations')
            ->with(['reservations' => fn ($query) => $query->latest('check_in')->limit(1)])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('frontdesk.guests.index', compact('guests'));
    }
}
