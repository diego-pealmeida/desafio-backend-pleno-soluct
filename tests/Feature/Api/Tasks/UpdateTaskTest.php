<?php

namespace Tests\Feature\Api\Tasks;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_task_successfully()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Old Title',
            'status' => TaskStatus::PENDING,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'title'       => 'New Task Title',
            'description' => 'Updated description',
            'status'      => TaskStatus::IN_PROGRESS->value,
            'due_date'    => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $payload);

        $response->assertOk();
        $response->assertJsonFragment([
            'title'       => 'New Task Title',
            'description' => 'Updated description',
            'status'      => TaskStatus::IN_PROGRESS->value,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'title'       => 'New Task Title',
            'description' => 'Updated description',
            'status'      => TaskStatus::IN_PROGRESS,
        ]);
    }

    public function test_it_returns_404_if_task_not_found()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'title'    => 'Updated',
            'status'   => TaskStatus::COMPLETED->value,
        ];

        $response = $this->putJson('/api/v1/tasks/999', $payload);

        $response->assertNotFound();
        $response->assertJson([
            'errors' => ['A tarefa informada é inválida'],
        ]);
    }

    public function test_it_requires_authentication()
    {
        $task = Task::factory()->create();

        $payload = [
            'title'    => 'Unauthorized update',
            'status'   => TaskStatus::CANCELED->value,
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $payload);

        $response->assertUnauthorized();
    }
}
