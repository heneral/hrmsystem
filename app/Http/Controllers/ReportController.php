<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Room;

class ReportController extends Controller
{
    public function reservations(Request $request)
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);
        $query = Reservation::with('room.roomType')->when($request->date_from, fn ($q, $date) => $q->whereDate('check_in', '>=', $date))->when($request->date_to, fn ($q, $date) => $q->whereDate('check_out', '<=', $date))->when($request->status, fn ($q, $status) => $q->where('status', $status));
        $reservations = $query->latest()->paginate(50)->withQueryString();
        $summary = ['total' => Reservation::count(), 'confirmed' => Reservation::where('status', 'confirmed')->count(), 'checked_in' => Reservation::where('status', 'checked_in')->count(), 'revenue' => Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->sum('total')];

        return view('admin.reports.reservations', compact('reservations', 'summary'));
    }

    public function financial(Request $request)
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);
        $payments = Payment::query()->where('status', 'paid')->when($request->date_from, fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))->when($request->date_to, fn ($q, $date) => $q->whereDate('paid_at', '<=', $date))->with('reservation')->latest()->paginate(50)->withQueryString();
        $summary = ['payments' => Payment::where('status', 'paid')->count(), 'revenue' => Payment::where('status', 'paid')->sum('amount'), 'refunded' => Payment::whereIn('status', ['refunded', 'partially_refunded'])->sum('refunded_amount'), 'outstanding' => Reservation::whereIn('status', ['confirmed', 'checked_in'])->sum('total') - Payment::whereIn('status', ['paid', 'partially_refunded'])->sum('amount')];

        return view('admin.reports.financial', compact('payments', 'summary'));
    }

    public function occupancy(Request $request)
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);
        $from = $request->date_from ? now()->parse($request->date_from)->startOfDay() : today()->subDays(6)->startOfDay();
        $to = $request->date_to ? now()->parse($request->date_to)->endOfDay() : today()->endOfDay();
        $totalRooms = max(1, Room::where('is_active', true)->count());
        $days = collect();
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $occupied = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->whereDate('check_in', '<=', $date)->whereDate('check_out', '>', $date)->count();
            $days->push(['date' => $date->toDateString(), 'occupied' => $occupied, 'rooms' => $totalRooms, 'rate' => round(($occupied / $totalRooms) * 100, 1)]);
        }
        $summary = ['average' => round($days->avg('rate'), 1), 'peak' => round($days->max('rate'), 1), 'occupied_days' => $days->sum('occupied'), 'inventory' => $totalRooms];

        return view('admin/reports/occupancy', compact('days', 'from', 'to', 'summary'));
    }

    public function reservationsCsv(Request $request)
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);
        $rows = Reservation::with('room')->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w'); fputcsv($handle, ['Confirmation', 'Room', 'Check-in', 'Check-out', 'Status', 'Total']);
            foreach ($rows as $reservation) fputcsv($handle, [$reservation->reservation_number, $reservation->room->room_number, $reservation->check_in->toDateString(), $reservation->check_out->toDateString(), $reservation->status->value, $reservation->total]);
            fclose($handle);
        }, 'hotelhub-reservations.csv', ['Content-Type' => 'text/csv']);
    }
}