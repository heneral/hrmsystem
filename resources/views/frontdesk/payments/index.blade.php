<x-app-layout>
    <x-slot name="header"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Front desk</p><h2 class="mt-1 text-2xl font-semibold text-stone-900">Payments</h2></div></x-slot>
    <div class="bg-stone-50 py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        <section class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"><form method="get" class="flex flex-col gap-3 sm:flex-row"><input name="search" value="{{ request('search') }}" placeholder="Search reservation or guest" class="w-full rounded-lg border-stone-300 text-sm"><button class="rounded-lg bg-stone-900 px-5 py-2 text-sm font-semibold text-white">Search</button></form></section>
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"><div class="border-b border-stone-200 p-5"><h3 class="font-semibold">Outstanding balances</h3><p class="mt-1 text-sm text-stone-500">Record permitted payments; completed transactions cannot be edited here.</p></div><div class="divide-y divide-stone-100">
            @forelse($reservations as $reservation)
                @php($paid = (float) $reservation->payments->whereIn('status', ['paid', 'partially_refunded'])->sum('amount'))
                @php($folio = (float) $reservation->folioTransactions->where('status', 'posted')->sum('amount'))
                @if($paid < (float) $reservation->total + $folio)
                    <div class="p-5"><div class="flex flex-wrap items-start justify-between gap-4"><div><p class="font-semibold">{{ $reservation->reservation_number }}</p><p class="mt-1 text-sm text-stone-500">{{ $reservation->customer?->name ?? 'Guest' }} · Room {{ $reservation->room->room_number }}</p><p class="mt-2 text-sm">Balance: <span class="font-semibold">${{ number_format((float) $reservation->total + $folio - $paid, 2) }}</span></p></div><form method="post" action="{{ route('frontdesk.payments.store', $reservation) }}" class="flex flex-wrap items-center gap-2">@csrf<input name="amount" type="number" min="0.01" step="0.01" max="{{ (float) $reservation->total + $folio - $paid }}" required placeholder="Amount" class="w-28 rounded-lg border-stone-300 text-sm"><select name="payment_method" class="rounded-lg border-stone-300 text-sm"><option value="cash">Cash</option><option value="card">Card</option><option value="bank_transfer">Bank transfer</option></select><button class="rounded-lg bg-stone-900 px-3 py-2 text-xs font-semibold text-white">Record payment</button></form></div></div>
                @endif
            @empty
                <div class="p-6 text-sm text-stone-500">No active balances found.</div>
            @endforelse
        </div><div class="border-t border-stone-200 p-5">{{ $reservations->links() }}</div></section>
        <section class="rounded-xl border border-stone-200 bg-white shadow-sm"><div class="border-b border-stone-200 p-5"><h3 class="font-semibold">Recent payment activity</h3></div><div class="divide-y divide-stone-100">
            @forelse($payments as $payment)
                <div class="flex justify-between gap-4 p-4 text-sm"><span>{{ $payment->reservation?->reservation_number ?? 'Reservation' }}</span><span class="font-semibold">${{ number_format($payment->amount, 2) }} · {{ $payment->status->value }}</span></div>
            @empty
                <div class="p-5 text-sm text-stone-500">No payment activity yet.</div>
            @endforelse
        </div></section>
    </div></div>
</x-app-layout>
