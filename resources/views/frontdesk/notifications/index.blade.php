<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Front desk</p>
            <h2 class="mt-1 text-2xl font-semibold text-stone-900">
                Notifications
            </h2>
        </div>
    </x-slot>
    <div class="bg-stone-50 py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800"
                >
                    {{ session('status') }}
                </div>
            @endif
            <section
                class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"
            >
                <div class="border-b border-stone-200 p-5">
                    <h3 class="font-semibold">Front Desk notifications</h3>
                    <p class="mt-1 text-sm text-stone-500">Reservation confirmations, payment events, and operational updates for your account.</p>
                </div>
                <div class="divide-y divide-stone-100">
                    @forelse ($notifications as $notification)
                        <div
                            class="flex items-start justify-between gap-4 p-5 {{ $notification->read_at ? '' : 'bg-amber-50/50' }}"
                        >
                            <div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="h-2 w-2 rounded-full {{ $notification->read_at ? 'bg-stone-300' : 'bg-amber-500' }}"
                                    ></span>
                                    <p class="font-semibold">{{ class_basename($notification->type) }}</p>
                                </div>
                                <p class="mt-2 text-sm text-stone-600">{{ data_get($notification->data, 'message', data_get($notification->data, 'reservation_number', 'A new HotelHub notification is available.')) }}</p>
                                <p class="mt-2 text-xs text-stone-400">{{ $notification->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @if (!$notification->read_at)
                                <form
                                    method="post"
                                    action="{{ route('frontdesk.notifications.read', $notification->id) }}"
                                >
                                    @csrf
                                    <button
                                        class="text-xs font-semibold text-amber-700"
                                    >
                                        Mark read
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-sm text-stone-500">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-stone-200 p-5">
                    {{ $notifications->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
