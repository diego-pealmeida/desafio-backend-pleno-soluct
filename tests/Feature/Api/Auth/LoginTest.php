<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => 'jane@example.com',
            'password'    => 'password123',
            'remember_me' => false,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'expires_at',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => User::API_TOKEN_NAME,
        ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email'    => 'john@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
        $response->assertJson([
            'errors' => ['O e-mail e/ou senha fornecidos são inválidos!'],
        ]);
    }

    public function test_login_requires_email_and_password()
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'O campo email é obrigatório.',
                'O campo password é obrigatório.'
            ]
        ]);
    }
}
