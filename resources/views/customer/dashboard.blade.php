<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">HotelHub guest portal</p>
                <h2 class="mt-1 text-2xl font-semibold text-stone-900">Welcome back, {{ auth()->user()->name }}</h2>
            </div>
            <a href="#search" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Find a room</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-stone-50 py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Upcoming stays</p><p class="mt-3 text-3xl font-semibold">{{ $upcoming->count() }}</p></div>
                <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Current stay</p><p class="mt-3 text-3xl font-semibold">{{ $current ? 'Active' : 'None' }}</p></div>
                <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Payments recorded</p><p class="mt-3 text-3xl font-semibold">${{ number_format($paid, 2) }}</p></div>
            </div>

            <section id="search" class="rounded-xl bg-stone-900 p-6 text-white shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">Plan your next stay</p>
                <h3 class="mt-2 text-2xl font-semibold">Search real room availability</h3>
                <form action="{{ route('rooms.search') }}" method="get" class="mt-5 grid gap-4 md:grid-cols-5">
                    <label class="text-sm">Check-in<input required type="date" name="check_in" min="{{ today()->toDateString() }}" class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-stone-900"></label>
                    <label class="text-sm">Check-out<input required type="date" name="check_out" class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-stone-900"></label>
                    <label class="text-sm">Adults<input required type="number" min="1" value="2" name="adults" class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-stone-900"></label>
                    <label class="text-sm">Children<input type="number" min="0" value="0" name="children" class="mt-1 block w-full rounded-lg border-0 bg-white px-3 py-2 text-stone-900"></label>
                    <button class="self-end rounded-lg bg-amber-400 px-4 py-2 font-semibold text-stone-950 hover:bg-amber-300">Search rooms</button>
                </form>
            </section>

            <section id="reservations" class="rounded-xl border border-stone-200 bg-white shadow-sm">
                <div class="border-b border-stone-200 p-6"><h3 class="text-lg font-semibold">Your reservations</h3></div>
                @forelse($reservations as $reservation)
                    <a href="{{ route('reservations.show', $reservation) }}" class="flex flex-col gap-3 border-b border-stone-100 p-6 transition hover:bg-amber-50 sm:flex-row sm:items-center sm:justify-between">
                        <div><p class="text-xs font-semibold uppercase tracking-widest text-amber-700">{{ $reservation->reservation_number }}</p><p class="mt-1 text-lg font-semibold">{{ $reservation->room->roomType->name }} · Room {{ $reservation->room->room_number }}</p><p class="mt-1 text-sm text-stone-500">{{ $reservation->check_in->format('M d, Y') }} to {{ $reservation->check_out->format('M d, Y') }}</p></div>
                        <div class="text-left sm:text-right"><p class="text-sm font-semibold capitalize">{{ str_replace('_', ' ', $reservation->status->value) }}</p><p class="mt-1 text-sm text-stone-500">${{ number_format($reservation->total, 2) }}</p></div>
                    </a>
                @empty
                    <div class="p-8 text-stone-600">You have no reservations yet. Use the search above to plan your first stay.</div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>