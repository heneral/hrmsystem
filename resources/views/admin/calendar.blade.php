<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <h2 class="text-2xl font-semibold text-stone-900">
                Reservation calendar
            </h2>
            <div class="flex gap-2">
                @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month'] as $key => $label)
                    <a
                        href="{{ route('admin.calendar', ['view' => $key, 'date' => $anchor->toDateString()]) }}"
                        class="rounded-lg px-3 py-2 text-sm font-semibold {{ $view === $key ? 'bg-amber-400 text-stone-950' : 'border border-stone-300 text-stone-600' }}"
                        >{{ $label }}</a
                    >
                @endforeach
            </div>
        </div>
    </x-slot>
    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-xl border border-stone-200 bg-white shadow-sm"
            >
                <div class="flex items-center justify-between border-b p-5">
                    <div>
                        <p class="text-sm text-stone-500">{{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-amber-700">{{ $reservations->count() }} reservation{{ $reservations->count() === 1 ? '' : 's' }} in view</p>
                    </div>
                    <a
                        href="{{ route('admin.calendar') }}"
                        class="text-sm font-semibold text-stone-600 hover:text-amber-700"
                        >Next upcoming</a
                    >
                </div>
                <div
                    class="grid gap-px bg-stone-200 {{ $view === 'day' ? 'grid-cols-1' : ($view === 'month' ? 'md:grid-cols-4' : 'md:grid-cols-7') }}"
                >
                    @foreach ($dates as $date)
                        <div class="min-h-36 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-widest {{ $date->isToday() ? 'text-amber-700' : 'text-stone-500' }}">{{ $date->format('D') }} · {{ $date->format('M d') }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($reservations->filter(fn ($reservation) => $reservation->check_in->lte($date) && $reservation->check_out->gt($date)) as $reservation)
                                    <a
                                        href="{{ route('reservations.show', $reservation) }}"
                                        class="block rounded-md bg-amber-100 p-2 text-xs text-amber-950"
                                        ><span class="font-semibold"
                                            >Room {{ $reservation->room->room_number }}</span
                                        ><br />{{ $reservation->reservation_number }}<br /><span
                                            class="capitalize"
                                            >{{ str_replace('_', ' ', $reservation->status->value) }}</span
                                        ></a
                                    >
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
