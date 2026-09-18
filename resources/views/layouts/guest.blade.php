<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-stone-50 px-4 py-10">
            <div class="mb-8"><x-hotelhub-mark /></div>

            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-stone-200 bg-white px-7 py-8 shadow-xl shadow-stone-200/60 sm:px-9">
                {{ $slot }}
            </div>
            <p class="mt-6 text-xs text-stone-500">Secure guest access · HotelHub Grand</p>
        </div>
    </body>
</html>
