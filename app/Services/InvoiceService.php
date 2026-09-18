<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(Reservation $reservation): Invoice
    {
        return DB::transaction(function () use ($reservation) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-'.str()->upper(str()->random(10)),
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'subtotal' => $reservation->subtotal + $reservation->folioTransactions()->where('status', 'posted')->sum('amount'),
                'taxes' => $reservation->taxes,
                'discounts' => $reservation->discounts,
                'total' => $reservation->total + $reservation->folioTransactions()->where('status', 'posted')->sum('amount'),
                'amount_paid' => $reservation->payments()->whereIn('status', ['paid', 'partially_refunded'])->sum('amount'),
                'balance' => $reservation->total + $reservation->folioTransactions()->where('status', 'posted')->sum('amount') - $reservation->payments()->where('status', 'paid')->sum('amount'),
                'status' => 'issued',
                'issued_at' => now(),
            ]);
            $invoice->items()->create(['item_type' => 'room', 'description' => 'Room accommodation', 'quantity' => $reservation->number_of_nights, 'unit_price' => $reservation->rate, 'total' => $reservation->subtotal]);
            foreach ($reservation->folioTransactions()->where('status', 'posted')->get() as $charge) {
                $invoice->items()->create(['item_type' => 'service', 'description' => $charge->description, 'quantity' => 1, 'unit_price' => $charge->amount, 'total' => $charge->amount]);
            }

            return $invoice->load('items');
        });
    }
}