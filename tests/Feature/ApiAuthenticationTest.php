<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_can_be_issued_and_used(): void
    {
        $user = User::factory()->create(['email' => 'api@example.test', 'password' => 'password']);

        $response = $this->postJson('/api/auth/token', ['email' => $user->email, 'password' => 'password', 'device_name' => 'test-device']);
        $response->assertOk()->assertJsonStructure(['token', 'user']);

        $token = $response->json('token');
        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/calendar')->assertForbidden();
    }

    public function test_invalid_api_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'api@example.test', 'password' => 'password']);

        $this->postJson('/api/auth/token', ['email' => 'api@example.test', 'password' => 'wrong', 'device_name' => 'test-device'])->assertUnauthorized();
    }
}
