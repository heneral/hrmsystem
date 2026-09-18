<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_front_desk_clerk_has_only_front_desk_panel_access(): void
    {
        $this->seed();
        $clerk = User::where('email', 'front-desk@hotelhub.test')->firstOrFail();

        $this->actingAs($clerk)->get('/front-desk')->assertOk();
        $this->actingAs($clerk)->get('/admin')->assertForbidden();
        $this->actingAs($clerk)->get('/admin/catalog')->assertForbidden();
    }

    public function test_customer_cannot_open_front_desk_panel(): void
    {
        $this->seed();
        $customer = User::where('email', 'customer@hotelhub.test')->firstOrFail();

        $this->actingAs($customer)->get('/front-desk')->assertForbidden();
    }

    public function test_unprivileged_user_cannot_open_admin_inventory(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/inventory/rooms')->assertForbidden();
    }

    public function test_unprivileged_token_cannot_open_staff_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/calendar')->assertForbidden();
    }
}
