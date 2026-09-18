<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskPaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(Request $request): View
    {
        $reservations = Reservation::with(['room', 'customer', 'guests', 'payments', 'folioTransactions'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());
                $query->where('reservation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            })
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();
        $payments = Payment::with('reservation')->latest()->limit(12)->get();

        return view('frontdesk.payments.index', compact('reservations', 'payments'));
    }

    public function store(ProcessPaymentRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->payments->record($reservation, $request->float('amount'), $request->string('payment_method')->toString(), 'manual', $request->input('transaction_id'));

        return back()->with('status', "Payment recorded for {$reservation->reservation_number}.");
    }
}
