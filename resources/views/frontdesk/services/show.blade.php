<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Guest services</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                {{ $serviceOrder->order_number }}
            </h2>
        </div>
    </x-slot>
    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">{{ str_replace('_', ' ', $serviceOrder->category) }}</p>
                        <h3 class="mt-2 text-xl font-semibold">
                            {{ $serviceOrder->reservation->customer?->name ?? 'Guest' }}
                        </h3>
                        <p class="mt-1 text-sm text-stone-500">Room {{ $serviceOrder->reservation->room->room_number }} · {{ $serviceOrder->reservation->reservation_number }}</p>
                    </div>
                    <span
                        class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase text-amber-700"
                        >{{ $serviceOrder->status }}</span
                    >
                </div>
                <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div>
                        <dt
                            class="text-xs uppercase tracking-widest text-stone-400"
                        >
                            Requested
                        </dt>
                        <dd class="mt-1 font-semibold">
                            {{ $serviceOrder->requested_at->format('M j, Y g:i A') }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs uppercase tracking-widest text-stone-400"
                        >
                            Delivery
                        </dt>
                        <dd class="mt-1 font-semibold">
                            {{ $serviceOrder->delivery_location ?? 'Room delivery' }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs uppercase tracking-widest text-stone-400"
                        >
                            Created by
                        </dt>
                        <dd class="mt-1 font-semibold">
                            {{ $serviceOrder->user->name }}
                        </dd>
                    </div>
                </dl>
                @if ($serviceOrder->notes)
                    <p class="mt-6 rounded-lg bg-amber-50 p-4 text-sm text-amber-900">{{ $serviceOrder->notes }}</p>
                @endif
            </section>
            <section
                class="rounded-xl border border-stone-200 bg-white shadow-sm"
            >
                <div class="border-b border-stone-200 p-5">
                    <h3 class="font-semibold">Order items and folio</h3>
                </div>
                <div class="divide-y divide-stone-100">
                    @foreach ($serviceOrder->items as $item)
                        <div class="flex justify-between gap-4 p-5">
                            <div>
                                <p class="font-semibold">{{ $item->description }} × {{ $item->quantity }}</p>
                                <p class="mt-1 text-sm text-stone-500">Price snapshot: ${{ number_format($item->unit_price, 2) }} each</p>
                            </div>
                            <p class="font-semibold">${{ number_format($item->total, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-stone-200 p-5">
                    <div class="flex justify-between">
                        <span class="font-semibold">Posted folio charge</span
                        ><span class="font-semibold"
                            >${{ number_format($serviceOrder->folioTransactions->where('status', 'posted')->sum('amount'), 2) }}</span
                        >
                    </div>
                </div>
            </section>
            <a
                href="{{ route('frontdesk.services.index') }}"
                class="inline-flex rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700"
                >Back to guest services</a
            >
        </div>
    </div>
</x-app-layout>
