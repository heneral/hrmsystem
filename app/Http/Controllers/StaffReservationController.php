<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use App\Services\ReservationWorkflowService;
use Illuminate\Http\Request;

class StaffReservationController extends Controller
{
    public function __construct(private readonly ReservationWorkflowService $workflow, private readonly PaymentService $payments) {}

    public function checkIn(Request $request, Reservation $reservation)
    {
        abort_unless($request->user()->hasPermission('reservations.manage'), 403);
        $reservation = $this->workflow->checkIn($reservation->load('room'));

        return response()->json(['data' => $reservation]);
    }

    public function checkOut(Request $request, Reservation $reservation)
    {
        abort_unless($request->user()->hasPermission('reservations.manage'), 403);
        $reservation = $this->workflow->checkOut($reservation->load('room'));

        return response()->json(['data' => $reservation]);
    }

    public function payment(ProcessPaymentRequest $request, Reservation $reservation)
    {
        $payment = $this->payments->record($reservation, $request->float('amount'), $request->string('payment_method')->toString(), 'manual', $request->input('transaction_id'));

        return response()->json(['data' => $payment], 201);
    }

    public function refund(Request $request, Payment $payment)
    {
        abort_unless($request->user()->hasPermission('payments.manage'), 403);
        $payment = $this->payments->refund($payment, $request->float('amount'));

        return response()->json(['data' => $payment]);
    }
}