<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function generate(Request $request, Reservation $reservation)
    {
        abort_unless($request->user()->hasPermission('payments.manage'), 403);
        return $this->download($request, $this->invoices->create($reservation->load('payments')));
    }

    public function download(Request $request, Invoice $invoice)
    {
        abort_unless($request->user()->hasPermission('payments.manage') || $invoice->reservation->user_id === $request->user()->id, 403);
        $invoice->load(['items', 'reservation.room.roomType']);
        $pdf = Pdf::loadView('invoices.show', compact('invoice'));
        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return response()->download(Storage::disk('local')->path($path), "{$invoice->invoice_number}.pdf");
    }
}