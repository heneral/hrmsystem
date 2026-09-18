<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Front desk</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                Room board
            </h2>
        </div>
    </x-slot>

    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
            >
                <h3 class="font-semibold">Physical rooms</h3>
                <p class="mt-1 text-sm text-stone-500">View readiness and current stays. Housekeeping and maintenance states are controlled by their workflows.</p>
            </section>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($rooms as $room)
                    @php
                        $statusColor = in_array($room->status->value, ['available', 'clean'])
                            ? 'bg-emerald-500'
                            : (in_array($room->status->value, ['maintenance', 'out_of_order']) ? 'bg-rose-500' : 'bg-amber-400');
                    @endphp
                    <div
                        class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-semibold">{{ $room->room_number }}</p>
                            <span
                                class="h-3 w-3 rounded-full {{ $statusColor }}"
                            ></span>
                        </div>
                        <p class="mt-1 text-sm text-stone-500">{{ $room->roomType->name }}</p>
                        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-stone-500">{{ str_replace('_', ' ', $room->status->value) }}</p>
                        @if ($room->reservations->first())
                            <p class="mt-3 border-t border-stone-100 pt-3 text-xs text-stone-500">
                                Current reservation<br />
                                <span
                                    class="font-semibold text-stone-800"
                                    >{{ $room->reservations->first()->reservation_number }}</span
                                >
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
