<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? $hotel->name.' | HotelHub' }}</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-stone-200 bg-stone-950 text-white">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-6 lg:px-8"
        >
            <a href="{{ route('home') }}" class="text-xl font-semibold"
                >Hotel<span class="text-amber-400">Hub</span></a
            >
            <nav class="flex items-center gap-4 text-sm">
                <a
                    href="{{ route('public.about') }}"
                    class="{{ request()->routeIs('public.about') ? 'text-amber-300' : 'hover:text-amber-300' }}"
                    >About</a
                >
                <a
                    href="{{ route('public.contact') }}"
                    class="{{ request()->routeIs('public.contact') ? 'text-amber-300' : 'hover:text-amber-300' }}"
                    >Contact</a
                >
                <a
                    href="{{ route('home') }}#search"
                    class="rounded-full bg-amber-400 px-4 py-2 font-semibold text-stone-950"
                    >Book a stay</a
                >
            </nav>
        </div>
    </header>

    <main>{{ $slot }}</main>

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
