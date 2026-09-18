<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Reporting workspace</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                Occupancy report
            </h2>
        </div>
    </x-slot>
    <div class="min-h-screen bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['label' => 'Average occupancy', 'value' => $summary['average'].'%'], ['label' => 'Peak occupancy', 'value' => $summary['peak'].'%'], ['label' => 'Occupied room-days', 'value' => $summary['occupied_days']], ['label' => 'Active inventory', 'value' => $summary['inventory'].' rooms']] as $metric)
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
                    <h3 class="text-xl font-semibold">Occupancy window</h3>
                    <p class="mt-1 text-sm text-stone-500">Compare occupied rooms with active inventory by date.</p>
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
                        Apply dates
                    </button>
                </form>
            </section>
            <section
                class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"
            >
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-xl font-semibold">Daily occupancy</h3>
                        <p class="mt-1 text-sm text-stone-500">{{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}</p>
                    </div>
                    <span
                        class="text-xs font-semibold uppercase tracking-widest text-amber-700"
                        >{{ count($days) }} days</span
                    >
                </div>
                <div class="mt-8 space-y-5">
                    @forelse ($days as $day)
                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span
                                    class="font-medium"
                                    >{{ \Carbon\Carbon::parse($day['date'])->format('D, M d') }}</span
                                ><span class="font-semibold"
                                    >{{ $day['occupied'] }}/{{ $day['rooms'] }} rooms
                                    · {{ $day['rate'] }}%</span
                                >
                            </div>
                            <div class="h-3 rounded-full bg-stone-100">
                                <div
                                    class="h-3 rounded-full {{ $day['rate'] >= 80 ? 'bg-rose-500' : ($day['rate'] >= 50 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                    data-occupancy-width="{{ $day['rate'] }}"
                                    x-data
                                    x-init="
                                        $el.style.width =
                                            $el.dataset.occupancyWidth + '%'
                                    "
                                ></div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-lg bg-stone-50 p-6 text-sm text-stone-500"
                        >
                            No occupancy data for this period.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
