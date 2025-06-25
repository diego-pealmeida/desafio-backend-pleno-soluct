<?php

namespace Tests\Feature\Api\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_tasks_list()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Task::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/v1/tasks');

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'description',
                    'status',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'due_date',
                    'created_at',
                    'updated_at',
                ],
            ],
            'total',
            'total_filtered'
        ]);

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('total'));
        $this->assertEquals(5, $response->json('total_filtered'));
    }

    public function test_it_requires_authentication()
    {
        $response = $this->getJson('/api/v1/tasks');

        $response->assertUnauthorized();
    }
}
