<?php

namespace Tests\Feature\Api\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_task_successfully()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'status',
            'user' => [
                'id',
                'name',
                'email'
            ],
            'due_date',
            'created_at',
            'updated_at',
        ]);

        $response->assertJsonFragment([
            'id'    => $task->id,
            'title' => $task->title,
        ]);
    }

    public function test_it_returns_404_if_task_not_found()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tasks/999");

        $response->assertNotFound();
        $response->assertJson([
            'errors' => ['A tarefa informada é inválida']
        ]);
    }

    public function test_it_requires_authentication()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertUnauthorized();
    }
}
