<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $hotel->name }} | HotelHub</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-900 antialiased">
    <header
        class="absolute inset-x-0 top-0 z-20 border-b border-white/20 text-white"
    >
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 sm:py-5 lg:px-8"
        >
            <a
                href="{{ route('home') }}"
                class="text-xl font-semibold tracking-tight"
                >Hotel<span class="text-amber-300">Hub</span></a
            >
            <nav class="flex items-center gap-3 text-sm font-medium">
                <a
                    href="{{ route('public.about') }}"
                    class="hidden px-2 py-2 text-xs hover:text-amber-300 sm:inline"
                    >About</a
                >
                <a
                    href="{{ route('public.contact') }}"
                    class="hidden px-2 py-2 text-xs hover:text-amber-300 sm:inline"
                    >Contact</a
                >
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-full border border-white/40 px-3 py-2 text-xs sm:px-4 sm:text-sm hover:bg-white hover:text-stone-900"
                        >Dashboard</a
                    >
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-2 py-2 text-xs hover:text-amber-300 sm:px-3 sm:text-sm"
                        >Log in</a
                    >
                    <a
                        href="{{ route('register') }}"
                        class="rounded-full bg-amber-400 px-3 py-2 text-xs font-semibold text-stone-950 hover:bg-amber-300 sm:px-4 sm:text-sm"
                        >Create account</a
                    >
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section
            class="relative isolate min-h-[600px] overflow-hidden bg-stone-950 text-white sm:min-h-[680px]"
        >
            <img
                src="{{ asset('images/hotel-hero.jpg') }}"
                alt="Hotel pool and tropical terrace"
                class="absolute inset-0 h-full w-full object-cover object-center"
            />
            <div
                class="absolute inset-0 bg-gradient-to-r from-stone-950/90 via-stone-950/55 to-stone-950/20"
            ></div>
            <div
                class="relative mx-auto flex min-h-[600px] max-w-7xl items-center px-5 pb-16 pt-28 sm:min-h-[680px] sm:px-6 sm:pb-24 sm:pt-36 lg:px-8"
            >
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Colombo · Sri Lanka</p>
                    <h1
                        class="mt-5 max-w-xl text-4xl font-semibold leading-tight tracking-tight sm:text-7xl"
                    >
                        Stay somewhere worth remembering.
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-stone-200 sm:mt-6 sm:text-lg sm:leading-8">{{ $hotel->description }}</p>
                    <a
                        href="#rooms"
                        class="mt-7 inline-flex items-center rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-stone-950 hover:bg-amber-300 sm:mt-8 sm:px-6 sm:text-base"
                        >Explore the rooms <span class="ml-3">→</span></a
                    >
                </div>
            </div>
        </section>

        <section
            id="search"
            class="relative z-10 -mt-10 bg-stone-50 px-4 pb-6 sm:-mt-16 sm:px-6 sm:pb-8 lg:px-8"
        >
            <div class="mx-auto max-w-6xl">
                @if ($errors->any())
                    <div
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
                    >
                        {{ $errors->first() }}
                    </div>
                @endif
                <form
                    x-data="{ checkIn: '', checkOut: '' }"
                    action="{{ route('rooms.search') }}"
                    method="get"
                    class="grid gap-3 rounded-2xl bg-white p-4 text-stone-900 shadow-2xl sm:p-5 md:grid-cols-5"
                >
                    <label
                        class="text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >Check-in<input
                            required
                            x-model="checkIn"
                            type="date"
                            name="check_in"
                            min="{{ today()->toDateString() }}"
                            class="mt-2 block w-full rounded-lg border-stone-200 px-3 py-2.5 text-sm font-normal text-stone-900"
                    /></label>
                    <label
                        class="text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >Check-out<input
                            required
                            x-model="checkOut"
                            :min="checkIn || '{{ today()->toDateString() }}'"
                            type="date"
                            name="check_out"
                            class="mt-2 block w-full rounded-lg border-stone-200 px-3 py-2.5 text-sm font-normal text-stone-900"
                    /></label>
                    <label
                        class="text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >Adults<input
                            required
                            type="number"
                            name="adults"
                            min="1"
                            value="2"
                            class="mt-2 block w-full rounded-lg border-stone-200 px-3 py-2.5 text-sm font-normal text-stone-900"
                    /></label>
                    <label
                        class="text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >Children<input
                            type="number"
                            name="children"
                            min="0"
                            value="0"
                            class="mt-2 block w-full rounded-lg border-stone-200 px-3 py-2.5 text-sm font-normal text-stone-900"
                    /></label>
                    <button
                        class="self-end rounded-lg bg-stone-900 px-4 py-3 text-sm font-semibold text-white hover:bg-amber-700"
                    >
                        Search availability
                    </button>
                </form>
            </div>
        </section>

        <section id="rooms" class="mx-auto max-w-7xl px-6 pb-20 pt-16 lg:px-8">
            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
            >
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Sleep well</p>
                    <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                        Rooms made for your pace.
                    </h2>
                </div>
                <p class="max-w-sm text-sm leading-6 text-stone-500">Choose a room, search your dates, and let HotelHub take care of the details.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($hotel->roomTypes as $roomType)
                    @php ($roomImage = $roomType->images->first())
                    <article
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div
                            class="h-56 overflow-hidden {{ $loop->even ? 'bg-amber-100' : 'bg-stone-200' }}"
                        >
                            <img
                                src="{{ $roomImage ? Storage::url($roomImage->path) : asset('images/room-default.jpg') }}"
                                alt="{{ $roomType->name }} room"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold">
                                        {{ $roomType->name }}
                                    </h3>
                                    <p class="mt-2 text-sm text-stone-500">{{ $roomType->bed_configuration }} · {{ $roomType->max_occupancy }} guests</p>
                                </div>
                                <p class="text-right text-lg font-semibold">${{ number_format($roomType->base_price, 0) }}<span class="block text-xs font-normal text-stone-500">per night</span></p>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($roomType->amenities->take(3) as $amenity)
                                    <span
                                        class="rounded-full bg-stone-100 px-3 py-1 text-xs text-stone-600"
                                        >{{ $amenity->name }}</span
                                    >
                                @endforeach
                            </div>
                            <a
                                href="#search"
                                class="mt-6 block text-sm font-semibold text-amber-700 hover:text-amber-900"
                                >Check availability
                                <span class="ml-1">→</span></a
                            >
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="border-y border-stone-200 bg-[#f3efe8]">
            <div
                class="mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:px-8"
            >
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">The HotelHub feeling</p>
                    <h2
                        class="mt-4 max-w-xl text-4xl font-semibold leading-tight tracking-tight sm:text-5xl"
                    >
                        A softer way to arrive in the city.
                    </h2>
                    <p class="mt-6 max-w-xl text-base leading-8 text-stone-600">Come back from a full day to cool sheets, warm light, and a team that already knows the details. HotelHub Grand is designed around the small pauses that make a stay feel restorative.</p>
                    <div
                        class="mt-8 grid max-w-lg grid-cols-3 gap-6 border-t border-stone-300 pt-6"
                    >
                        <div>
                            <p class="text-2xl font-semibold">24/7</p>
                            <p class="mt-1 text-xs uppercase tracking-widest text-stone-500">Front desk</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold">9</p>
                            <p class="mt-1 text-xs uppercase tracking-widest text-stone-500">Rooms today</p>
                        </div>
                        <div>
                            <p class="text-2xl font-semibold">4.9</p>
                            <p class="mt-1 text-xs uppercase tracking-widest text-stone-500">Guest feeling</p>
                        </div>
                    </div>
                </div>
                <div
                    class="relative overflow-hidden rounded-2xl bg-stone-900 shadow-xl"
                >
                    <img
                        src="{{ asset('images/hotel-hero.jpg') }}"
                        alt="HotelHub Grand pool terrace"
                        class="h-80 w-full object-cover object-center opacity-90 sm:h-96"
                    />
                    <div
                        class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/80 to-transparent p-6 pt-20 text-white"
                    >
                        <p class="text-sm font-medium">Slow mornings. Easy evenings.</p>
                        <p class="mt-1 text-xs text-stone-300">A stay shaped around your own rhythm.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-stone-200 bg-white">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-[.8fr_1.2fr]">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">A little extra</p>
                        <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                            Make the stay yours.
                        </h2>
                        <p class="mt-5 max-w-sm leading-7 text-stone-500">From breakfast before a busy day to an airport transfer home, add what makes your visit effortless.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ($services as $service)
                            <div class="rounded-xl border border-stone-200 p-5">
                                <div class="text-2xl text-amber-600">✦</div>
                                <h3 class="mt-5 font-semibold">
                                    {{ $service->name }}
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-stone-500">{{ $service->description ?: 'A thoughtful extra for a smoother stay.' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-stone-950 text-white">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div
                    class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-center"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Before you arrive</p>
                        <h2
                            class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl"
                        >
                            Everything is ready when you are.
                        </h2>
                    </div>
                    <div
                        class="grid gap-8 border-t border-stone-700 pt-8 sm:grid-cols-3 lg:border-l lg:border-t-0 lg:pl-10 lg:pt-0"
                    >
                        <div>
                            <p class="text-sm font-semibold">Flexible planning</p>
                            <p class="mt-2 text-sm leading-6 text-stone-400">Search real availability and keep your dates in one place.</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Thoughtful extras</p>
                            <p class="mt-2 text-sm leading-6 text-stone-400">Add breakfast, transfers, and more when you reserve.</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Clear, calm service</p>
                            <p class="mt-2 text-sm leading-6 text-stone-400">Your confirmation, payments, and stay details stay together.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-[.7fr_1.3fr] lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Why guests return</p>
                        <h2
                            class="mt-4 text-4xl font-semibold leading-tight tracking-tight sm:text-5xl"
                        >
                            A stay that feels considered.
                        </h2>
                    </div>
                    <p class="max-w-xl text-lg leading-8 text-stone-500">From the first search to the final checkout, every detail is designed to feel clear, warm, and easy to make your own.</p>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-3">
                    <article
                        class="rounded-2xl border border-stone-200 bg-stone-50 p-6"
                    >
                        <span class="text-3xl text-amber-600">01</span>
                        <h3 class="mt-10 text-xl font-semibold">
                            Arrive without friction
                        </h3>
                        <p class="mt-3 text-sm leading-7 text-stone-500">Real-time availability, clear rates, and a confirmation you can find whenever you need it.</p>
                    </article>
                    <article
                        class="rounded-2xl border border-stone-200 bg-stone-50 p-6"
                    >
                        <span class="text-3xl text-amber-600">02</span>
                        <h3 class="mt-10 text-xl font-semibold">
                            Settle into your rhythm
                        </h3>
                        <p class="mt-3 text-sm leading-7 text-stone-500">Add breakfast, room service, airport transfers, and thoughtful extras to your stay.</p>
                    </article>
                    <article
                        class="rounded-2xl border border-stone-200 bg-stone-50 p-6"
                    >
                        <span class="text-3xl text-amber-600">03</span>
                        <h3 class="mt-10 text-xl font-semibold">
                            Leave feeling looked after
                        </h3>
                        <p class="mt-3 text-sm leading-7 text-stone-500">Transparent folios, attentive support, and a calm checkout from start to finish.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="border-y border-stone-200 bg-[#f3efe8]">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div
                    class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Guest notes</p>
                        <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                            The little things matter.
                        </h2>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-stone-500">A few words from guests who found their pause here.</p>
                </div>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <figure class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="text-amber-500">★★★★★</div>
                        <blockquote
                            class="mt-5 text-lg leading-8 text-stone-700"
                        >
                            “The whole stay felt unhurried. Booking was simple,
                            and the room was exactly what we hoped for.”
                        </blockquote>
                        <figcaption
                            class="mt-6 text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >
                            Maya · Weekend stay
                        </figcaption>
                    </figure>
                    <figure class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="text-amber-500">★★★★★</div>
                        <blockquote
                            class="mt-5 text-lg leading-8 text-stone-700"
                        >
                            “A beautiful base for the city. The breakfast and
                            airport transfer made the trip feel effortless.”
                        </blockquote>
                        <figcaption
                            class="mt-6 text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >
                            Daniel · City break
                        </figcaption>
                    </figure>
                    <figure class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="text-amber-500">★★★★★</div>
                        <blockquote
                            class="mt-5 text-lg leading-8 text-stone-700"
                        >
                            “Quiet when we wanted rest, attentive when we needed
                            help. We would happily come back.”
                        </blockquote>
                        <figcaption
                            class="mt-6 text-xs font-semibold uppercase tracking-widest text-stone-500"
                        >
                            Aisha · Three-night stay
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div
                    class="grid gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-start"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Good to know</p>
                        <h2 class="mt-3 text-4xl font-semibold tracking-tight">
                            Questions, answered.
                        </h2>
                        <p class="mt-5 max-w-sm text-sm leading-7 text-stone-500">Everything you need before you choose your room.</p>
                    </div>
                    <div
                        x-data="{ open: 1 }"
                        class="divide-y divide-stone-200 border-y border-stone-200"
                    >
                        @foreach ([['question' => 'Can I add services after booking?', 'answer' => 'Yes. Breakfast, room service, airport transfers, and other available services can be added to an active reservation.'], ['question' => 'How does room availability work?', 'answer' => 'Availability is checked against your dates, guest count, current reservations, and room operating status.'], ['question' => 'Can I manage my reservation online?', 'answer' => 'Yes. After signing in, you can view your reservation, payment details, special requests, and available actions.'], ['question' => 'How can I reach the hotel team?', 'answer' => 'Our front desk is available around the clock. Visit the Contact page for location details and arrival support.']] as $index => $faq)
                            <div class="py-5">
                                <button
                                    type="button"
                                    @click="open = open === {{ $index }} ? -1 : {{ $index }}"
                                    class="flex w-full items-center justify-between gap-4 text-left text-base font-semibold"
                                >
                                    <span>{{ $faq['question'] }}</span
                                    ><span
                                        class="text-xl font-normal text-amber-600"
                                        x-text="open === {{ $index }} ? '−' : '+'"
                                    ></span>
                                </button>
                                <p
                                    x-show="open === {{ $index }}"
                                    x-collapse
                                    class="mt-3 max-w-2xl text-sm leading-7 text-stone-500"
                                >{{ $faq['answer'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-amber-400">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-14 sm:flex-row sm:items-center sm:justify-between lg:px-8"
            >
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-stone-700">Your next pause is close</p>
                    <h2
                        class="mt-2 text-3xl font-semibold tracking-tight text-stone-950"
                    >
                        Find a room that feels like yours.
                    </h2>
                </div>
                <a
                    href="#search"
                    class="inline-flex w-fit items-center rounded-full bg-stone-950 px-6 py-3 text-sm font-semibold text-white hover:bg-stone-800"
                    >Check availability <span class="ml-3">→</span></a
                >
            </div>
        </section>
    </main>

    <footer class="bg-stone-950 text-stone-400">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-8 text-sm sm:flex-row sm:items-center sm:justify-between lg:px-8"
        >
            <p>© {{ date('Y') }} {{ $hotel->name }}. Powered by HotelHub.</p>
            <p>{{ $hotel->address }} · {{ $hotel->city }}, {{ $hotel->country }}</p>
        </div>
    </footer>
</body>
</html>
