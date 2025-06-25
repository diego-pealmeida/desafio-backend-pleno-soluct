<?php

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $payload = [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'password' => 'Str0ng@Pass1'
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertCreated();
        $response->assertJson([
            'id' => 1,
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'created_at' => now()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d H:i:s'),
            'updated_at' => now()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'name' => 'Maria Souza',
        ]);

        $user = User::where('email', 'maria@example.com')->first();
        $this->assertTrue(Hash::check('Str0ng@Pass1', $user->password));
    }

    public function test_registration_fails_with_invalid_password()
    {
        $payload = [
            'name' => 'Usuário Fraco',
            'email' => 'fraco@example.com',
            'password' => '123'
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                "O campo password deve ter pelo menos 8 caracteres.",
                "O campo password deve conter pelo menos uma letra maiúscula e uma minúscula.",
                "O campo password deve conter pelo menos uma letra.",
                "O campo password deve conter pelo menos um símbolo."
            ]
        ]);
    }

    public function test_registration_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/v1/register', []);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                "O campo name é obrigatório.",
                "O campo email é obrigatório.",
                "O campo password é obrigatório."
            ]
        ]);
    }

    public function test_registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'jaexiste@example.com']);

        $payload = [
            'name' => 'Duplicado',
            'email' => 'jaexiste@example.com',
            'password' => 'Valid@123'
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                "O campo email deve conter um valor único."
            ]
        ]);
    }
}
