<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">Welcome back</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">
            Sign in to HotelHub
        </h1>
        <p class="mt-2 text-sm text-stone-500">Manage reservations, payments, and your next stay.</p>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        @if (request('redirect'))
            <input
                type="hidden"
                name="redirect"
                value="{{ request('redirect') }}"
            />
        @endif

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                />
                <span
                    class="ms-2 text-sm text-gray-600"
                    >{{ __('Remember me') }}</span
                >
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div
        class="my-6 flex items-center gap-3 text-xs uppercase tracking-widest text-stone-400"
    >
        <span class="h-px flex-1 bg-stone-200"></span>or continue with<span
            class="h-px flex-1 bg-stone-200"
        ></span>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (['google' => 'G  Google', 'facebook' => 'f  Facebook'] as $provider => $label)
            @if (config("services.{$provider}.client_id"))
                <a
                    href="{{ route('social.redirect', $provider) }}"
                    class="flex items-center justify-center gap-2 rounded-lg border border-stone-300 px-4 py-3 text-sm font-semibold hover:border-stone-900"
                    >{{ $label }}</a
                >
            @else
                <span
                    title="Add {{ strtoupper($provider) }} OAuth credentials in .env"
                    class="flex cursor-not-allowed items-center justify-center gap-2 rounded-lg border border-stone-200 px-4 py-3 text-sm font-semibold text-stone-400"
                    >{{ $label }}</span
                >
            @endif
        @endforeach
    </div>
    @if (! config('services.google.client_id') && ! config('services.facebook.client_id'))
        <p class="mt-3 text-center text-xs text-stone-400">Social login needs Google or Facebook OAuth credentials in your local `.env` file.</p>
    @endif
</x-guest-layout>
