<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Available rooms | HotelHub</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8"
        >
            <a
                href="{{ route('home') }}"
                class="text-xl font-semibold tracking-tight"
                >Hotel<span class="text-amber-500">Hub</span></a
            >
            <div class="flex items-center gap-3 text-sm font-semibold">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-full border border-stone-300 px-4 py-2 hover:border-stone-900"
                        >Dashboard</a
                    >
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-3 py-2 text-stone-600 hover:text-stone-950"
                        >Log in</a
                    >
                    <a
                        href="{{ route('register') }}"
                        class="rounded-full bg-stone-900 px-4 py-2 text-white hover:bg-amber-700"
                        >Create account</a
                    >
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        @if ($errors->any())
            <div
                class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
            >
                <p class="font-semibold">Please check the booking details.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @php
            $checkIn = \Carbon\Carbon::parse(request('check_in'));
            $checkOut = \Carbon\Carbon::parse(request('check_out'));
            $nights = $checkIn->diffInDays($checkOut);
        @endphp

        <div
            class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"
        >
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">HotelHub availability</p>
                <h1
                    class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl"
                >
                    Choose your room.
                </h1>
                <p class="mt-3 text-stone-600">{{ $checkIn->format('D, M d') }} – {{ $checkOut->format('D, M d, Y') }} · {{ $nights }} {{ str('night')->plural($nights) }} · {{ request('adults') }} adults, {{ request('children', 0) }} children</p>
            </div>
            <a
                href="{{ route('home') }}#search"
                class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:border-stone-900"
                >Change search</a
            >
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
            <section class="space-y-5">
                @forelse ($rooms as $room)
                    @php ($roomImage = $room->roomType->images->first())
                    <article
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md"
                    >
                        <div
                            class="grid md:grid-cols-[240px_minmax(0,1fr)_190px]"
                        >
                            <div
                                class="h-52 overflow-hidden bg-amber-100 md:h-full"
                            >
                                <img
                                    src="{{ $roomImage ? Storage::url($roomImage->path) : asset('images/room-default.jpg') }}"
                                    alt="{{ $room->roomType->name }}"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="p-6">
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Room {{ $room->room_number }}</p>
                                        <h2 class="mt-2 text-2xl font-semibold">
                                            {{ $room->roomType->name }}
                                        </h2>
                                    </div>
                                    <span
                                        class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                                        >Available</span
                                    >
                                </div>
                                <p class="mt-3 text-sm text-stone-600">{{ $room->roomType->bed_configuration }} · Up to {{ $room->roomType->max_occupancy }} guests · {{ $room->roomType->size_sqm ? $room->roomType->size_sqm.' m²' : 'Spacious room' }}</p>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach ($room->roomType->amenities->take(4) as $amenity)
                                        <span
                                            class="rounded-full bg-stone-100 px-3 py-1 text-xs text-stone-600"
                                            >{{ $amenity->name }}</span
                                        >
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="border-t border-stone-100 p-6 md:border-l md:border-t-0"
                            >
                                <p class="text-xs font-semibold uppercase tracking-widest text-stone-500">From</p>
                                <p class="mt-2 text-2xl font-semibold">${{ number_format($room->roomType->base_price, 0) }}</p>
                                <p class="text-sm text-stone-500">per night</p>
                                @auth
                                    @php ($guestName = preg_split('/\s+/', trim(auth()->user()->name), 2))
                                    <form
                                        method="post"
                                        action="{{ route('reservations.store') }}"
                                        class="mt-5"
                                    >
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="room_id"
                                            value="{{ $room->id }}"
                                        /><input
                                            type="hidden"
                                            name="check_in"
                                            value="{{ request('check_in') }}"
                                        /><input
                                            type="hidden"
                                            name="check_out"
                                            value="{{ request('check_out') }}"
                                        /><input
                                            type="hidden"
                                            name="adults"
                                            value="{{ request('adults') }}"
                                        /><input
                                            type="hidden"
                                            name="children"
                                            value="{{ request('children', 0) }}"
                                        /><input
                                            type="hidden"
                                            name="first_name"
                                            value="{{ $guestName[0] ?? auth()->user()->name }}"
                                        /><input
                                            type="hidden"
                                            name="last_name"
                                            value="{{ $guestName[1] ?? 'Guest' }}"
                                        /><input
                                            type="hidden"
                                            name="email"
                                            value="{{ auth()->user()->email }}"
                                        />
                                        <details class="mb-3 text-left">
                                            <summary
                                                class="cursor-pointer text-xs font-semibold text-stone-600"
                                            >
                                                Add extras or coupon
                                            </summary>
                                            <div class="mt-2 grid gap-2">
                                                <select
                                                    name="services[]"
                                                    multiple
                                                    class="w-full rounded-lg border-stone-300 text-xs"
                                                >
                                                    @foreach ($services as $service)
                                                        <option
                                                            value="{{ $service->id }}"
                                                        >
                                                            {{ $service->name }}
                                                        </option>
                                                    @endforeach</select
                                                ><input
                                                    name="coupon_code"
                                                    class="w-full rounded-lg border-stone-300 text-xs"
                                                    placeholder="Coupon code"
                                                />
                                            </div>
                                        </details>
                                        <button
                                            class="w-full rounded-lg bg-stone-900 px-3 py-3 text-sm font-semibold text-white hover:bg-amber-700"
                                        >
                                            Reserve room
                                        </button>
                                    </form>
                                @else
                                    <a
                                        href="{{ route('login', ['redirect' => url()->full()]) }}"
                                        class="mt-5 block rounded-lg bg-stone-900 px-3 py-3 text-center text-sm font-semibold text-white hover:bg-amber-700"
                                        >Sign in to reserve</a
                                    >
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div
                        class="rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center"
                    >
                        <h2 class="text-xl font-semibold">
                            No rooms available
                        </h2>
                        <p class="mt-2 text-stone-500">Try different dates or adjust the number of guests.</p>
                        <a
                            href="{{ route('home') }}#search"
                            class="mt-6 inline-block rounded-full bg-stone-900 px-5 py-3 text-sm font-semibold text-white"
                            >Search again</a
                        >
                    </div>
                @endforelse
            </section>

            <aside
                class="h-fit rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:sticky lg:top-6"
            >
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Your search</p>
                <dl class="mt-5 space-y-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-stone-500">Check-in</dt>
                        <dd class="font-semibold">
                            {{ $checkIn->format('M d, Y') }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-stone-500">Check-out</dt>
                        <dd class="font-semibold">
                            {{ $checkOut->format('M d, Y') }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-stone-500">Guests</dt>
                        <dd class="font-semibold">
                            {{ request('adults') }} adults · {{ request('children', 0) }} children
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-t border-stone-100 pt-4"
                    >
                        <dt class="text-stone-500">Matching rooms</dt>
                        <dd class="font-semibold">{{ $rooms->count() }}</dd>
                    </div>
                </dl>
                <p class="mt-6 rounded-lg bg-stone-50 p-4 text-xs leading-5 text-stone-500">Prices shown are nightly starting rates. Your final total is recalculated securely when you reserve.</p>
            </aside>
        </div>
    </main>
</body>
</html>
