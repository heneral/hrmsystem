<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Front desk</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">Check-in</h2>
        </div>
    </x-slot>
    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-stone-500">Today's confirmed arrivals. Verify payment and guest details before checking in.</p>
            @forelse ($reservations as $reservation)
                <section
                    class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
                >
                    <div
                        class="flex flex-wrap items-start justify-between gap-4"
                    >
                        <div>
                            <p class="font-semibold">{{ $reservation->reservation_number }} · {{ $reservation->customer?->name ?? 'Guest' }}</p>
                            <p class="mt-1 text-sm text-stone-500">Room {{ $reservation->room?->room_number ?? 'Unassigned' }} · {{ $reservation->room?->roomType?->name ?? 'Room pending' }} · {{ $reservation->adults }} adults, {{ $reservation->children }} children</p>
                            <p class="mt-2 text-xs uppercase tracking-widest text-stone-500">Payment: {{ $reservation->payment_status }} · Check-in: {{ $reservation->check_in->format('M j, Y') }} · Check-out: {{ $reservation->check_out->format('M j, Y') }}</p>
                        </div>
                        <form
                            method="post"
                            action="{{ route('admin.operations.reservations.check-in', $reservation) }}"
                        >
                            @csrf
                            <button
                                class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                Check in guest
                            </button>
                        </form>
                    </div>
                    @if ($reservation->special_requests)
                        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Special request: {{ $reservation->special_requests }}</p>
                    @endif
                </section>
            @empty
                <div
                    class="rounded-xl border border-stone-200 bg-white p-6 text-sm text-stone-500"
                >
                    No confirmed arrivals scheduled for today.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
