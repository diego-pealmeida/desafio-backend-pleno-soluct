<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_logs_out_successfully()
    {
        $user = User::factory()->create();

        $token = $user->createToken(User::API_TOKEN_NAME, ['*']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token->plainTextToken}"
        ])->postJson('/api/v1/auth/logout');
        $response->assertNoContent();

        $this->assertNotNull(
            $user->tokens()->first()->expires_at,
            'Token não foi revogado corretamente.'
        );

        $this->assertTrue(
            $user->tokens()->first()->expires_at->lte(now()),
            'Token não expirou como esperado.'
        );
    }

    public function test_it_handles_exceptions_gracefully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $user->tokens()->delete();

        $response = $this->postJson('/api/v1/auth/logout');
        $response->assertNoContent();
    }
}
