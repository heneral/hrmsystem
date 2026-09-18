<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">HotelHub control room</p>
                <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                    Hotel operations
                </h2>
            </div>
            <span
                class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                >Live data</span
            >
        </div>
    </x-slot>

    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($metrics as $label => $value)
                    <div
                        class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
                    >
                        <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">{{ str_replace('_', ' ', $label) }}</p>
                        <p class="mt-3 text-3xl font-semibold">{{ str_contains($label, 'revenue') ? '$'.number_format($value, 2) : $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
                <section
                    class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold">Reservation activity</h3>
                            <p class="mt-1 text-sm text-stone-500">Bookings created over the last seven days</p>
                        </div>
                        <span
                            class="text-xs font-semibold uppercase tracking-widest text-stone-400"
                            >7 days</span
                        >
                    </div>
                    <div
                        class="mt-8 flex h-48 items-end gap-3 border-b border-stone-200"
                    >
                        @php ($maxCount = max(1, $reservationTrend->max('count')))
                        <div
                            class="flex h-full flex-1 items-end justify-between gap-3"
                        >
                            @foreach ($reservationTrend as $point)
                                <div
                                    class="flex h-full flex-1 flex-col items-center justify-end gap-2"
                                >
                                    <span
                                        class="text-xs font-semibold text-stone-500"
                                        >{{ $point['count'] }}</span
                                    >
                                    <div
                                        class="w-full max-w-10 rounded-t-md bg-amber-400 transition hover:bg-amber-500"
                                        data-bar-height="{{ max(8, ($point['count'] / $maxCount) * 78) }}"
                                        x-data
                                        x-init="
                                            $el.style.height =
                                                $el.dataset.barHeight + '%'
                                        "
                                        title="{{ $point['count'] }} reservations, ${{ number_format($point['revenue'], 2) }} revenue"
                                    ></div>
                                    <span
                                        class="text-xs text-stone-500"
                                        >{{ $point['label'] }}</span
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section
                    class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm"
                >
                    <h3 class="font-semibold">Room status</h3>
                    <p class="mt-1 text-sm text-stone-500">Current inventory by operating state</p>
                    <div class="mt-7 space-y-5">
                        @foreach ($roomStatus as $status)
                            <div>
                                <div class="mb-2 flex justify-between text-sm">
                                    <span class="flex items-center gap-2"
                                        ><span
                                            class="h-2.5 w-2.5 rounded-full {{ $status['color'] }}"
                                        ></span
                                        >{{ $status['label'] }}</span
                                    >
                                    <span
                                        class="font-semibold"
                                        >{{ $status['value'] }}</span
                                    >
                                </div>
                                <div class="h-2 rounded-full bg-stone-100">
                                    <div
                                        class="h-2 rounded-full {{ $status['color'] }}"
                                        data-bar-width="{{ $metrics['reservations'] > 0 ? min(100, ($status['value'] / max(1, $metrics['available'] + $metrics['occupancy'] + $metrics['maintenance'])) * 100) : 0 }}"
                                        x-data
                                        x-init="
                                            $el.style.width =
                                                $el.dataset.barWidth + '%'
                                        "
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section
                class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"
            >
                <div class="border-b border-stone-200 p-5">
                    <h3 class="font-semibold">Recent reservations</h3>
                </div>
                <div class="divide-y">
                    @forelse ($recentReservations as $reservation)
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <p class="font-medium">{{ $reservation->reservation_number }}</p>
                                <p class="text-sm text-stone-500">Room {{ $reservation->room->room_number }} · {{ $reservation->check_in->format('M d') }} to {{ $reservation->check_out->format('M d') }}</p>
                            </div>
                            <span
                                class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold"
                                >{{ $reservation->status->value }}</span
                            >
                        </div>
                    @empty
                        <div class="p-6 text-stone-500">
                            No reservations have been created yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
