<x-public-layout :hotel="$hotel" :title="'Contact '.$hotel->name.' | HotelHub'">
    <section class="bg-[#f3efe8]">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">We are here to help</p>
            <h1
                class="mt-4 max-w-2xl text-5xl font-semibold leading-tight tracking-tight sm:text-6xl"
            >
                Make your arrival easy.
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-stone-600">Reach the {{ $hotel->name }} team for booking help, arrival planning, transport, or anything that will make your stay smoother.</p>
        </div>
    </section>

    <section
        class="mx-auto grid max-w-7xl gap-6 px-5 py-16 sm:px-6 lg:grid-cols-[.8fr_1.2fr] lg:px-8"
    >
        <div class="space-y-4">
            <div class="rounded-xl border border-stone-200 bg-white p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Address</p>
                <p class="mt-3 font-semibold">{{ $hotel->address }}</p>
                <p class="mt-1 text-sm text-stone-500">{{ $hotel->city }}, {{ $hotel->country }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-white p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Front desk</p>
                <p class="mt-3 font-semibold">Available 24 hours</p>
                <p class="mt-1 text-sm text-stone-500">Our team can help with arrivals, room questions, and guest services.</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-white p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">Guest services</p>
                <p class="mt-3 text-sm leading-7 text-stone-600">{{ $services->pluck('name')->join(' · ') }}</p>
            </div>
        </div>
        <div class="rounded-2xl bg-stone-950 p-7 text-white shadow-xl sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Plan your stay</p>
            <h2 class="mt-4 text-3xl font-semibold">Tell us what you need.</h2>
            <p class="mt-4 leading-7 text-stone-300">For the fastest response, start by checking room availability. You can add guest services such as breakfast and airport transfer during booking.</p>
            <a
                href="{{ route('home') }}#search"
                class="mt-8 inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-stone-950"
                >Search rooms <span class="ml-3">→</span></a
            >
        </div>
    </section>

    <section class="border-y border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[.7fr_1.3fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Before you arrive</p>
                    <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                        A smoother first day.
                    </h2>
                    <p class="mt-5 max-w-sm text-sm leading-7 text-stone-500">Use your reservation details to keep every part of the stay together.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-stone-50 p-5">
                        <p class="text-2xl text-amber-600">01</p>
                        <h3 class="mt-5 font-semibold">Search</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500">Choose dates and see genuine availability.</p>
                    </div>
                    <div class="rounded-xl bg-stone-50 p-5">
                        <p class="text-2xl text-amber-600">02</p>
                        <h3 class="mt-5 font-semibold">Personalize</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500">Add breakfast, transfers, or other available services.</p>
                    </div>
                    <div class="rounded-xl bg-stone-50 p-5">
                        <p class="text-2xl text-amber-600">03</p>
                        <h3 class="mt-5 font-semibold">Arrive</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500">Your booking and payment details stay together.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-stone-950 text-white">
        <div class="mx-auto max-w-7xl px-5 py-16 sm:px-6 lg:px-8">
            <div
                class="flex flex-col justify-between gap-8 sm:flex-row sm:items-end"
            >
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Need a room?</p>
                    <h2 class="mt-3 text-4xl font-semibold">
                        Let’s find the right stay.
                    </h2>
                </div>
                <a
                    href="{{ route('home') }}#rooms"
                    class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-stone-950"
                    >Explore rooms <span class="ml-3">→</span></a
                >
            </div>
        </div>
    </section>
</x-public-layout>
