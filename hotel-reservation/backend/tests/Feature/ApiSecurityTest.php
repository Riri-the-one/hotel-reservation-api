<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_token_is_required_issued_and_revoked(): void
    {
        User::factory()->create(['email' => 'api@example.test']);
        $this->getJson('/api/reservations')->assertUnauthorized();
        $this->postJson('/api/login', ['email' => 'api@example.test', 'password' => 'wrong'])->assertUnprocessable();
        $response = $this->postJson('/api/login', ['email' => 'api@example.test', 'password' => 'password'])->assertOk();
        $this->withToken($response->json('access_token'))->getJson('/api/reservations')->assertOk();
        $this->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_every_management_resource_rejects_regular_users(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['rooms', 'room-types', 'tariffs'] as $resource) {
            $this->getJson('/api/'.$resource)->assertForbidden();
            $this->postJson('/api/'.$resource, [])->assertForbidden();
        }
    }

    public function test_token_login_has_a_rate_limit(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', ['email' => 'nobody@example.test', 'password' => 'wrong'])->assertUnprocessable();
        }
        $this->postJson('/api/login', ['email' => 'nobody@example.test', 'password' => 'wrong'])->assertStatus(429);
    }
}
