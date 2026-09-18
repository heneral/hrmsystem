<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RatePlan;
use App\Models\RoomRate;
use App\Models\HotelService;
use App\Models\ServicePrice;
use App\Models\Coupon;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Payment;
use App\Enums\RoomStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = collect([
            ['name' => 'Manage hotel operations', 'slug' => 'hotel.manage'],
            ['name' => 'Manage reservations', 'slug' => 'reservations.manage'],
            ['name' => 'Manage rooms', 'slug' => 'rooms.manage'],
            ['name' => 'Manage payments', 'slug' => 'payments.manage'],
            ['name' => 'Manage housekeeping', 'slug' => 'housekeeping.manage'],
            ['name' => 'Manage maintenance engineering', 'slug' => 'maintenance.manage'],
            ['name' => 'View reports', 'slug' => 'reports.view'],
            ['name' => 'Manage users and permissions', 'slug' => 'users.manage'],
        ])->mapWithKeys(fn (array $permission) => [
            $permission['slug'] => Permission::updateOrCreate(['slug' => $permission['slug']], $permission),
        ]);

        $roleDefinitions = [
            'super-administrator' => ['name' => 'Super Administrator', 'permissions' => $permissions->keys()],
            'hotel-administrator' => ['name' => 'Hotel Manager', 'permissions' => $permissions->except('users.manage')->keys()],
            'front-desk' => ['name' => 'Front Desk Clerk', 'permissions' => ['reservations.manage', 'payments.manage', 'rooms.manage']],
            'housekeeping' => ['name' => 'Housekeeping Staff', 'permissions' => ['housekeeping.manage', 'rooms.manage']],
            'maintenance-staff' => ['name' => 'Maintenance Staff', 'permissions' => ['maintenance.manage', 'rooms.manage']],
            'accountant' => ['name' => 'Accountant', 'permissions' => ['payments.manage', 'reports.view']],
            'customer' => ['name' => 'Guest', 'permissions' => []],
        ];

        $roles = collect($roleDefinitions)->mapWithKeys(function (array $definition, string $slug) use ($permissions) {
            $role = Role::updateOrCreate(['slug' => $slug], ['name' => $definition['name'], 'is_system' => true]);
            $role->permissions()->sync($permissions->only($definition['permissions'])->pluck('id'));

            return [$slug => $role];
        });

        $admin = User::updateOrCreate(['email' => 'admin@hotelhub.test'], ['name' => 'HotelHub Administrator', 'password' => 'password', 'email_verified_at' => now()]);
        $admin->roles()->sync([$roles['super-administrator']->id]);

        $customer = User::updateOrCreate(['email' => 'customer@hotelhub.test'], ['name' => 'Demo Customer', 'password' => 'password', 'email_verified_at' => now()]);
        $customer->roles()->sync([$roles['customer']->id]);

        foreach ([
            ['name' => 'Hotel Administrator', 'email' => 'hotel-admin@hotelhub.test', 'role' => 'hotel-administrator'],
            ['name' => 'Front Desk Agent', 'email' => 'front-desk@hotelhub.test', 'role' => 'front-desk'],
            ['name' => 'Housekeeping Staff', 'email' => 'housekeeping@hotelhub.test', 'role' => 'housekeeping'],
            ['name' => 'Maintenance Staff', 'email' => 'maintenance@hotelhub.test', 'role' => 'maintenance-staff'],
            ['name' => 'Hotel Accountant', 'email' => 'accountant@hotelhub.test', 'role' => 'accountant'],
        ] as $staff) {
            $user = User::updateOrCreate(['email' => $staff['email']], ['name' => $staff['name'], 'password' => 'password', 'email_verified_at' => now()]);
            $user->roles()->sync([$roles[$staff['role']]->id]);
        }

        $hotel = Hotel::updateOrCreate(['slug' => 'hotelhub-grand'], [
            'name' => 'HotelHub Grand',
            'slug' => 'hotelhub-grand',
            'description' => 'A modern city hotel with thoughtful rooms and calm, attentive service.',
            'address' => '1 Hospitality Way',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'timezone' => 'Asia/Colombo',
        ]);

        $amenities = collect(['Wi-Fi', 'Breakfast', 'Air conditioning', 'Workspace', 'Smart TV'])
            ->mapWithKeys(fn (string $name) => [
                $name => Amenity::updateOrCreate(['slug' => str($name)->slug()], ['name' => $name]),
            ]);

        $roomTypes = [
            ['name' => 'Standard Room', 'base_price' => 125, 'max_adults' => 2, 'max_children' => 1, 'max_occupancy' => 3, 'bed_configuration' => '1 queen bed', 'amenities' => ['Wi-Fi', 'Air conditioning', 'Smart TV']],
            ['name' => 'Deluxe Room', 'base_price' => 185, 'max_adults' => 2, 'max_children' => 2, 'max_occupancy' => 4, 'bed_configuration' => '1 king bed', 'amenities' => ['Wi-Fi', 'Breakfast', 'Air conditioning', 'Workspace', 'Smart TV']],
            ['name' => 'Executive Suite', 'base_price' => 320, 'max_adults' => 3, 'max_children' => 2, 'max_occupancy' => 5, 'bed_configuration' => '1 king bed and sofa bed', 'amenities' => ['Wi-Fi', 'Breakfast', 'Air conditioning', 'Workspace', 'Smart TV']],
        ];

        foreach ($roomTypes as $definition) {
            $roomType = RoomType::updateOrCreate(['hotel_id' => $hotel->id, 'slug' => str($definition['name'])->slug()], [
                'hotel_id' => $hotel->id,
                'name' => $definition['name'],
                'slug' => str($definition['name'])->slug(),
                'base_price' => $definition['base_price'],
                'max_adults' => $definition['max_adults'],
                'max_children' => $definition['max_children'],
                'max_occupancy' => $definition['max_occupancy'],
                'bed_configuration' => $definition['bed_configuration'],
                'size_sqm' => 28,
            ]);
            $roomType->amenities()->sync($amenities->only($definition['amenities'])->pluck('id'));

            foreach (range(1, 3) as $roomIndex) {
                Room::updateOrCreate(['room_type_id' => $roomType->id, 'room_number' => ($roomType->id * 100) + $roomIndex], [
                    'room_type_id' => $roomType->id,
                    'room_number' => ($roomType->id * 100) + $roomIndex,
                    'floor' => $roomType->id,
                ]);
            }
        }

        $standardRate = RatePlan::updateOrCreate(['slug' => 'standard-rate'], ['name' => 'Standard Rate', 'description' => 'Flexible daily rate with standard cancellation.', 'cancellation_policy' => 'Cancel up to 48 hours before arrival.', 'minimum_stay' => 1]);
        RoomRate::updateOrCreate(['rate_plan_id' => $standardRate->id, 'room_type_id' => $hotel->roomTypes()->first()->id, 'starts_on' => today()], ['ends_on' => today()->addYear(), 'price' => 125]);

        foreach ([
            ['name' => 'Breakfast', 'category' => 'food_and_beverage', 'pricing_unit' => 'per_guest', 'price' => 18],
            ['name' => 'Room Service', 'category' => 'food_and_beverage', 'pricing_unit' => 'per_item', 'price' => 22],
            ['name' => 'Lunch', 'category' => 'food_and_beverage', 'pricing_unit' => 'per_guest', 'price' => 28],
            ['name' => 'Dinner', 'category' => 'food_and_beverage', 'pricing_unit' => 'per_guest', 'price' => 35],
            ['name' => 'Minibar', 'category' => 'food_and_beverage', 'pricing_unit' => 'per_item', 'price' => 12],
            ['name' => 'Airport Transfer', 'category' => 'transport', 'pricing_unit' => 'per_booking', 'price' => 45],
            ['name' => 'Extra Bed', 'category' => 'room_extra', 'pricing_unit' => 'per_night', 'price' => 30],
        ] as $serviceDefinition) {
            $service = HotelService::updateOrCreate(['name' => $serviceDefinition['name']], ['category' => $serviceDefinition['category'], 'pricing_unit' => $serviceDefinition['pricing_unit']]);
            ServicePrice::updateOrCreate(['service_id' => $service->id, 'starts_on' => today()], ['ends_on' => today()->addYear(), 'price' => $serviceDefinition['price']]);
        }

        Coupon::updateOrCreate(['code' => 'WELCOME10'], ['description' => 'Welcome discount', 'discount_type' => 'percentage', 'discount_value' => 10, 'starts_on' => today()->subDay(), 'ends_on' => today()->addMonths(6), 'minimum_amount' => 100]);

        $demoRoom = Room::query()->first();
        $demoGuest = Guest::updateOrCreate(['email' => 'maya@example.test'], ['first_name' => 'Maya', 'last_name' => 'Perera', 'country' => 'Sri Lanka']);
        $demoReservation = Reservation::updateOrCreate(['reservation_number' => 'HH-DEMO2026'], ['user_id' => $customer->id, 'room_id' => $demoRoom->id, 'check_in' => today()->addDays(14), 'check_out' => today()->addDays(17), 'adults' => 2, 'children' => 0, 'number_of_nights' => 3, 'rate' => $demoRoom->roomType->base_price, 'subtotal' => $demoRoom->roomType->base_price * 3, 'taxes' => $demoRoom->roomType->base_price * 3 * 0.12, 'discounts' => 0, 'total' => $demoRoom->roomType->base_price * 3 * 1.12, 'payment_status' => 'paid', 'status' => 'confirmed']);
        $demoReservation->guests()->syncWithoutDetaching([$demoGuest->id => ['is_primary' => true]]);
        Payment::updateOrCreate(['transaction_id' => 'DEMO-PAYMENT-001'], ['reservation_id' => $demoReservation->id, 'amount' => $demoReservation->total, 'currency' => 'USD', 'payment_method' => 'card', 'gateway' => 'demo', 'status' => 'paid', 'paid_at' => now()]);

        $frontDeskRooms = Room::query()->orderBy('id')->get()->values();
        $frontDeskReservations = [
            ['number' => 'HH-FD-ARRIVAL', 'guest' => ['email' => 'frontdesk-arrival@example.test', 'first_name' => 'Daniel', 'last_name' => 'Silva'], 'room' => $frontDeskRooms->get(1), 'check_in' => today(), 'check_out' => today()->addDays(2), 'status' => 'confirmed', 'payment' => 'paid', 'payment_id' => 'FD-PAYMENT-ARRIVAL'],
            ['number' => 'HH-FD-CURRENT', 'guest' => ['email' => 'frontdesk-current@example.test', 'first_name' => 'Aisha', 'last_name' => 'Fernando'], 'room' => $frontDeskRooms->get(2), 'check_in' => today()->subDay(), 'check_out' => today()->addDay(), 'status' => 'checked_in', 'payment' => 'paid', 'payment_id' => 'FD-PAYMENT-CURRENT'],
            ['number' => 'HH-FD-DEPARTURE', 'guest' => ['email' => 'frontdesk-departure@example.test', 'first_name' => 'Noah', 'last_name' => 'Perera'], 'room' => $frontDeskRooms->get(3), 'check_in' => today()->subDays(2), 'check_out' => today(), 'status' => 'checked_in', 'payment' => 'paid', 'payment_id' => 'FD-PAYMENT-DEPARTURE'],
        ];

        foreach ($frontDeskReservations as $definition) {
            if (! $definition['room']) {
                continue;
            }
            $guest = Guest::updateOrCreate(['email' => $definition['guest']['email']], $definition['guest']);
            $nights = $definition['check_in']->diffInDays($definition['check_out']);
            $subtotal = (float) $definition['room']->roomType->base_price * $nights;
            $total = $subtotal * 1.12;
            $reservation = Reservation::updateOrCreate(['reservation_number' => $definition['number']], [
                'user_id' => $customer->id, 'room_id' => $definition['room']->id, 'check_in' => $definition['check_in'], 'check_out' => $definition['check_out'],
                'adults' => 2, 'children' => 0, 'number_of_nights' => $nights, 'rate' => $definition['room']->roomType->base_price,
                'subtotal' => $subtotal, 'taxes' => $subtotal * 0.12, 'discounts' => 0, 'total' => $total,
                'payment_status' => $definition['payment'], 'status' => $definition['status'],
            ]);
            $reservation->guests()->syncWithoutDetaching([$guest->id => ['is_primary' => true]]);
            Payment::updateOrCreate(['transaction_id' => $definition['payment_id']], ['reservation_id' => $reservation->id, 'amount' => $total, 'currency' => 'USD', 'payment_method' => 'card', 'gateway' => 'demo', 'status' => 'paid', 'paid_at' => now()]);
            $definition['room']->update(['status' => $definition['status'] === 'checked_in' ? RoomStatus::OCCUPIED : RoomStatus::RESERVED]);
        }

        $additionalFrontDeskReservations = [
            ['number' => 'HH-FD-PENDING', 'guest' => ['email' => 'frontdesk-pending@example.test', 'first_name' => 'Liam', 'last_name' => 'Jayasinghe'], 'room' => $frontDeskRooms->get(4), 'check_in' => today()->addDays(3), 'check_out' => today()->addDays(5), 'status' => 'pending', 'payment_status' => 'pending', 'payment_id' => null],
            ['number' => 'HH-FD-CONFIRMED', 'guest' => ['email' => 'frontdesk-confirmed@example.test', 'first_name' => 'Sofia', 'last_name' => 'Wickramasinghe'], 'room' => $frontDeskRooms->get(5), 'check_in' => today()->addDay(), 'check_out' => today()->addDays(3), 'status' => 'confirmed', 'payment_status' => 'paid', 'payment_id' => 'FD-PAYMENT-CONFIRMED'],
            ['number' => 'HH-FD-UNPAID', 'guest' => ['email' => 'frontdesk-unpaid@example.test', 'first_name' => 'Ethan', 'last_name' => 'De Silva'], 'room' => $frontDeskRooms->get(6), 'check_in' => today()->addDay(), 'check_out' => today()->addDays(4), 'status' => 'confirmed', 'payment_status' => 'pending', 'payment_id' => null],
            ['number' => 'HH-FD-HISTORY', 'guest' => ['email' => 'frontdesk-history@example.test', 'first_name' => 'Amara', 'last_name' => 'Fernando'], 'room' => $frontDeskRooms->get(7), 'check_in' => today()->subDays(4), 'check_out' => today()->subDay(), 'status' => 'checked_out', 'payment_status' => 'paid', 'payment_id' => 'FD-PAYMENT-HISTORY'],
        ];

        foreach ($additionalFrontDeskReservations as $definition) {
            if (! $definition['room']) {
                continue;
            }
            $guest = Guest::updateOrCreate(['email' => $definition['guest']['email']], $definition['guest']);
            $nights = $definition['check_in']->diffInDays($definition['check_out']);
            $subtotal = (float) $definition['room']->roomType->base_price * $nights;
            $total = $subtotal * 1.12;
            $reservation = Reservation::updateOrCreate(['reservation_number' => $definition['number']], [
                'user_id' => $customer->id, 'room_id' => $definition['room']->id, 'check_in' => $definition['check_in'], 'check_out' => $definition['check_out'],
                'adults' => 1, 'children' => 0, 'number_of_nights' => $nights, 'rate' => $definition['room']->roomType->base_price,
                'subtotal' => $subtotal, 'taxes' => $subtotal * 0.12, 'discounts' => 0, 'total' => $total,
                'payment_status' => $definition['payment_status'], 'status' => $definition['status'],
            ]);
            $reservation->guests()->syncWithoutDetaching([$guest->id => ['is_primary' => true]]);
            if ($definition['payment_id']) {
                Payment::updateOrCreate(['transaction_id' => $definition['payment_id']], ['reservation_id' => $reservation->id, 'amount' => $total, 'currency' => 'USD', 'payment_method' => 'card', 'gateway' => 'demo', 'status' => 'paid', 'paid_at' => now()]);
            }
            $definition['room']->update(['status' => match ($definition['status']) { 'checked_in' => RoomStatus::OCCUPIED, 'checked_out' => RoomStatus::DIRTY, default => RoomStatus::RESERVED }]);
        }

        $currentReservation = Reservation::where('reservation_number', 'HH-FD-CURRENT')->first();
        $breakfast = HotelService::where('name', 'Breakfast')->first();
        if ($currentReservation && $breakfast) {
            $currentReservation->items()->updateOrCreate(['item_type' => 'service', 'description' => 'Breakfast'], ['quantity' => 2, 'unit_price' => 18, 'total' => 36]);
        }
    }
}
