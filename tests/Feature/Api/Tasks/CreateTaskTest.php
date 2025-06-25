<?php

namespace Tests\Feature\Api\Tasks;

use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_task_successfully()
    {
        Log::shouldReceive('error')->never(); // Garante que nenhum erro é logado

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'title'       => 'Nova tarefa importante',
            'description' => 'Descrição qualquer',
            'status'      => TaskStatus::PENDING,
            'due_date'    => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/v1/tasks', $payload);

        $response->assertCreated();

        $response->assertJson([
            'id' => 1,
            'title' => $payload['title'],
            'description' => $payload['description'],
            'status' => TaskStatus::PENDING->value,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'due_date' => $payload['due_date'],
            'created_at' => now()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d H:i:s'),
            'updated_at' => now()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('tasks', [
            'title'       => $payload['title'],
            'description' => $payload['description'],
            'status'      => $payload['status'],
            'user_id'     => $user->id,
        ]);
    }

    public function test_it_requires_authentication()
    {
        $payload = [
            'title'       => 'Tarefa sem login',
            'description' => 'Descrição',
            'status'      => TaskStatus::PENDING,
        ];

        $response = $this->postJson('/api/v1/tasks', $payload);

        $response->assertUnauthorized();
    }

    public function test_it_validates_required_fields()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [];

        $response = $this->postJson('/api/v1/tasks', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'O campo title é obrigatório.',
                'O campo status é obrigatório.'
            ]
        ]);
    }
}
