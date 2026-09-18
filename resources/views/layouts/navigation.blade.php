<aside
    x-data="{ open: false, hash: '' }"
    x-init="
        hash = location.hash;
        window.addEventListener('hashchange', () => (hash = location.hash));
    "
    class="z-40 lg:fixed lg:inset-y-0 lg:left-0 lg:w-72"
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-stone-950/40 lg:hidden"
        @click="open = false"
    ></div>

    <div
        :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 flex w-72 flex-col border-r border-stone-800 bg-stone-950 text-stone-300 transition-transform duration-200 lg:translate-x-0"
    >
        <div class="flex h-20 items-center border-b border-stone-800 px-7">
            <a
                href="{{ route('dashboard') }}"
                class="text-xl font-semibold tracking-tight text-white"
                >Hotel<span class="text-amber-400">Hub</span></a
            >
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
            <p class="px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">{{ auth()->user()->hasRole('front-desk') ? 'Front desk' : 'Platform' }}</p>
            <nav class="mt-3 space-y-1">
                @if (auth()->user()->hasRole('front-desk'))
                    <a
                        href="{{ route('frontdesk.dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.dashboard') && ! request()->has('section') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>⌂</span> Dashboard</a
                    >
                    <p class="mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Front desk</p>
                    <a
                        href="{{ route('frontdesk.reservations.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.reservations.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▣</span> Reservations</a
                    >
                    <a
                        href="{{ route('admin.calendar') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-stone-300 transition hover:bg-stone-800 hover:text-white"
                        ><span>□</span> Calendar</a
                    >
                    <a
                        href="{{ route('frontdesk.guests.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.guests.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>◎</span> Guests</a
                    >
                    <a
                        href="{{ route('frontdesk.rooms.board') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.rooms.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▥</span> Room board</a
                    >
                    <a
                        href="{{ route('frontdesk.checkin.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.checkin.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>✓</span> Check-in</a
                    >
                    <a
                        href="{{ route('frontdesk.checkout.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.checkout.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>↗</span> Check-out</a
                    >
                    <a
                        href="{{ route('frontdesk.payments.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.payments.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>$</span> Payments</a
                    >
                    <a
                        href="{{ route('frontdesk.services.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.services.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>+</span> Services</a
                    >
                    <a
                        href="{{ route('frontdesk.notifications.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.notifications.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>◌</span> Notifications</a
                    >
                    <p class="mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Reports</p>
                    <a
                        href="{{ route('frontdesk.reports.daily') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('frontdesk.reports.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▤</span> Daily report</a
                    >
                @elseif (auth()->user()->hasPermission('reservations.manage'))
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>⌂</span> Dashboard</a
                    >
                @elseif (! auth()->user()->hasPermission('housekeeping.manage') && ! auth()->user()->hasPermission('maintenance.manage'))
                    <a
                        href="{{ route('dashboard') }}"
                        @click="hash = ''"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                        :class="hash === ''
                            ? 'bg-amber-400 text-stone-950'
                            : 'text-stone-300 hover:bg-stone-800 hover:text-white'"
                        ><span>⌂</span> Dashboard</a
                    >
                @endif
                @if (! auth()->user()->hasPermission('reservations.manage') && (auth()->user()->hasPermission('housekeeping.manage') || auth()->user()->hasPermission('maintenance.manage')))
                    <a
                        href="{{ route('admin.operations') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.operations') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▦</span> Operations</a
                    >
                @endif
                @if (auth()->user()->hasPermission('reservations.manage') && ! auth()->user()->hasRole('front-desk'))
                    <a
                        href="{{ route('admin.operations') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.operations') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▦</span> Operations</a
                    >
                    <a
                        href="{{ route('admin.calendar') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.calendar') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>□</span> Calendar</a
                    >
                    <p class="mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Hotel management</p>
                    @if (auth()->user()->hasPermission('hotel.manage'))
                        <a
                            href="{{ route('admin.catalog.index') }}"
                            class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.catalog.*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                            ><span>◇</span> Catalog</a
                        >
                    @endif
                    @if (auth()->user()->hasPermission('rooms.manage'))
                        <a
                            href="{{ route('admin.inventory.rooms') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.inventory.rooms*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                            ><span>▥</span> Rooms</a
                        >
                        <a
                            href="{{ route('admin.inventory.room-types') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.inventory.room-types*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                            ><span>▧</span> Room types</a
                        >
                    @endif
                    <p class="mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Front office</p>
                    <a
                        href="{{ route('admin.reports.reservations') }}"
                        class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.reports.reservations*') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>▤</span> Reservation reports</a
                    >
                    <p class="mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Finance</p>
                    <a
                        href="{{ route('admin.reports.financial') }}"
                        class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.reports.financial') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>$</span> Finance</a
                    >
                    <a
                        href="{{ route('admin.reports.occupancy') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.reports.occupancy') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                        ><span>◫</span> Occupancy</a
                    >
                    @if (auth()->user()->hasPermission('users.manage'))
                        <a
                            href="{{ route('admin.audit-logs') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.audit-logs') ? 'bg-amber-400 text-stone-950' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}"
                            ><span>◌</span> Audit logs</a
                        >
                    @endif
                @elseif (! auth()->user()->hasRole('front-desk') && ! auth()->user()->hasPermission('housekeeping.manage') && ! auth()->user()->hasPermission('maintenance.manage'))
                    <a
                        href="{{ route('dashboard') }}#search"
                        @click="hash = '#search'"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                        :class="hash === '#search'
                            ? 'bg-amber-400 text-stone-950'
                            : 'text-stone-300 hover:bg-stone-800 hover:text-white'"
                        ><span>⌕</span> Find a room</a
                    >
                    <a
                        href="{{ route('dashboard') }}#reservations"
                        @click="hash = '#reservations'"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                        :class="hash === '#reservations'
                            ? 'bg-amber-400 text-stone-950'
                            : 'text-stone-300 hover:bg-stone-800 hover:text-white'"
                        ><span>▣</span> My reservations</a
                    >
                @endif
            </nav>

            <p class="mt-9 px-3 text-[10px] font-semibold uppercase tracking-[0.22em] text-stone-500">Account</p>
            <nav class="mt-3 space-y-1">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-stone-300 transition hover:bg-stone-800 hover:text-white"
                    ><span>◎</span> Profile</a
                >
            </nav>
        </div>

        <div class="border-t border-stone-800 p-4">
            <div class="mb-3 px-3">
                <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-stone-500">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-stone-400 transition hover:bg-stone-800 hover:text-white"
                >
                    Sign out
                </button>
            </form>
        </div>
    </div>

    <button
        type="button"
        @click="open = !open"
        class="fixed left-4 top-4 z-50 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-stone-950 text-white shadow-lg lg:hidden"
        aria-label="Toggle navigation menu"
    >
        <span x-show="!open">☰</span><span x-show="open">×</span>
    </button>
</aside>
