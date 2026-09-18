<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Front desk reports</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                Daily front desk report
            </h2>
        </div>
    </x-slot>
    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form
                method="get"
                class="flex max-w-md gap-3 rounded-xl border border-stone-200 bg-white p-4 shadow-sm"
            >
                <input
                    type="date"
                    name="date"
                    value="{{ $date->toDateString() }}"
                    class="w-full rounded-lg border-stone-300 text-sm"
                /><button
                    class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white"
                >
                    View report
                </button>
            </form>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($metrics as $label => $value)
                    <div
                        class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"
                    >
                        <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">{{ ucwords(str_replace('_', ' ', $label)) }}</p>
                        <p class="mt-3 text-3xl font-semibold">{{ in_array($label, ['payments_collected', 'outstanding_balances'], true) ? '$'.number_format($value, 2) : $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
