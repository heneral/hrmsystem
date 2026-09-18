<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->hasPermission('reservations.manage'), 403);
        $view = $request->string('view', 'week')->toString();
        $defaultDate = Reservation::query()->whereDate('check_out', '>=', today())->orderBy('check_in')->value('check_in') ?? today()->toDateString();
        $anchor = Carbon::parse($request->input('date', $defaultDate));
        [$from, $to] = match ($view) {
            'day' => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
            'month' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
        };
        $reservations = Reservation::with(['room.roomType', 'customer'])->whereDate('check_in', '<=', $to)->whereDate('check_out', '>=', $from)->orderBy('check_in')->get();
        $dates = collect();
        for ($date = $from->copy()->startOfDay(); $date->lte($to); $date->addDay()) $dates->push($date->copy());

        return view('admin/calendar', compact('view', 'anchor', 'from', 'to', 'dates', 'reservations'));
    }
}