<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Reporting workspace</p>
                <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                    Financial report
                </h2>
            </div>
            <span
                class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                >Live ledger</span
            >
        </div>
    </x-slot>
    <div class="min-h-screen bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['label' => 'Payments', 'value' => $summary['payments']], ['label' => 'Revenue', 'value' => '$'.number_format($summary['revenue'], 2)], ['label' => 'Refunded', 'value' => '$'.number_format($summary['refunded'], 2)], ['label' => 'Outstanding', 'value' => '$'.number_format(max(0, $summary['outstanding']), 2)]] as $metric)
                    <div
                        class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
                    >
                        <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">{{ $metric['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $metric['value'] }}</p>
                    </div>
                @endforeach
            </div>
            <section
                class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"
            >
                <div>
                    <h3 class="text-xl font-semibold">Filter payments</h3>
                    <p class="mt-1 text-sm text-stone-500">Review successful gateway transactions by date.</p>
                </div>
                <form method="get" class="mt-5 grid gap-3 md:grid-cols-3">
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="rounded-lg border-stone-300"
                    /><input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="rounded-lg border-stone-300"
                    /><button
                        class="rounded-lg bg-stone-900 px-4 py-3 text-sm font-semibold text-white hover:bg-amber-700"
                    >
                        Apply filters
                    </button>
                </form>
            </section>
            <section
                class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm"
            >
                <div class="border-b border-stone-200 p-6">
                    <h3 class="text-xl font-semibold">Payment activity</h3>
                    <p class="mt-1 text-sm text-stone-500">{{ $payments->total() }} successful payments</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr
                                class="border-b bg-stone-50 text-xs uppercase tracking-widest text-stone-500"
                            >
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Reservation</th>
                                <th class="px-6 py-4">Gateway</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr
                                    class="border-b last:border-0 hover:bg-amber-50"
                                >
                                    <td class="px-6 py-4 text-stone-600">
                                        {{ optional($payment->paid_at)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold">
                                        {{ $payment->reservation->reservation_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold"
                                            >{{ $payment->gateway }}</span
                                        >
                                    </td>
                                    <td
                                        class="px-6 py-4 capitalize text-stone-600"
                                    >
                                        {{ $payment->payment_method }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-semibold"
                                    >
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-6 py-12 text-center text-stone-500"
                                    >
                                        No payments match these filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-stone-100 p-6">
                    {{ $payments->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
