<x-public-layout :hotel="$hotel" :title="'About '.$hotel->name.' | HotelHub'">
    <section class="bg-[#f3efe8]">
        <div
            class="mx-auto grid max-w-7xl gap-10 px-5 py-20 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8"
        >
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">The HotelHub feeling</p>
                <h1
                    class="mt-4 text-5xl font-semibold leading-tight tracking-tight sm:text-6xl"
                >
                    A softer way to arrive in {{ $hotel->city }}.
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-stone-600">{{ $hotel->description }}</p>
                <a
                    href="{{ route('home') }}#search"
                    class="mt-8 inline-flex rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-white"
                    >Find your room <span class="ml-3">→</span></a
                >
            </div>
            <img
                src="{{ asset('images/hotel-hero.jpg') }}"
                alt="{{ $hotel->name }} pool terrace"
                class="h-96 w-full rounded-2xl object-cover shadow-xl"
            />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[.75fr_1.25fr]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Designed for the pause</p>
                <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                    Everything you need, nothing you do not.
                </h2>
                <p class="mt-5 max-w-md text-sm leading-7 text-stone-500">{{ $hotel->name }} is a calm base for city days, quiet mornings, and evenings that do not need a schedule.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-3">
                <div class="rounded-xl border border-stone-200 bg-white p-5">
                    <p class="text-2xl text-amber-600">24/7</p>
                    <h3 class="mt-4 font-semibold">Front desk</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Helpful support before arrival, throughout the stay, and on the way home.</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-5">
                    <p class="text-2xl text-amber-600">{{ $hotel->roomTypes->count() }}</p>
                    <h3 class="mt-4 font-semibold">Room styles</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">Comfortable spaces for short city stays, work trips, and longer escapes.</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-5">
                    <p class="text-2xl text-amber-600">Local</p>
                    <h3 class="mt-4 font-semibold">Easy to reach</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-500">A calm base for discovering {{ $hotel->city }} at your own pace.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-stone-200 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
            >
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">The stay, your way</p>
                    <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                        Choose the room that fits the moment.
                    </h2>
                </div>
                <a
                    href="{{ route('home') }}#rooms"
                    class="text-sm font-semibold text-amber-700"
                    >Explore rooms <span class="ml-1">→</span></a
                >
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($hotel->roomTypes as $roomType)
                    <article class="rounded-xl border border-stone-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold">
                                    {{ $roomType->name }}
                                </h3>
                                <p class="mt-2 text-sm text-stone-500">{{ $roomType->bed_configuration }} · up to {{ $roomType->max_occupancy }} guests</p>
                            </div>
                            <span class="text-right text-sm font-semibold"
                                >${{ number_format($roomType->base_price, 0) }}<span
                                    class="block text-xs font-normal text-stone-400"
                                    >nightly</span
                                ></span
                            >
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach ($roomType->amenities->take(4) as $amenity)
                                <span
                                    class="rounded-full bg-stone-100 px-3 py-1 text-xs text-stone-600"
                                    >{{ $amenity->name }}</span
                                >
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-stone-950 text-white">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Make it yours</p>
                    <h2
                        class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl"
                    >
                        Small extras, thoughtfully arranged.
                    </h2>
                    <p class="mt-5 max-w-md leading-7 text-stone-300">Add useful comforts to an active reservation, from breakfast and room service to airport transfers and room extras.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($hotel->roomTypes->flatMap->amenities->unique('id')->take(6) as $amenity)
                        <div class="rounded-xl border border-stone-700 p-5">
                            <p class="font-semibold">{{ $amenity->name }}</p>
                            <p class="mt-2 text-sm leading-6 text-stone-400">Available with selected room styles.</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-amber-400">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-14 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
        >
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-stone-700">Ready when you are</p>
                <h2 class="mt-2 text-3xl font-semibold text-stone-950">
                    Your next stay starts here.
                </h2>
            </div>
            <a
                href="{{ route('home') }}#search"
                class="inline-flex w-fit rounded-full bg-stone-950 px-6 py-3 text-sm font-semibold text-white"
                >Check availability <span class="ml-3">→</span></a
            >
        </div>
    </section>
</x-public-layout>
