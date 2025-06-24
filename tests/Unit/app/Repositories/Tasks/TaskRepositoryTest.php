<?php

namespace Tests\Unit\app\Repositories\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Exceptions\Task\CreateException;
use App\Exceptions\Task\UpdateException;
use App\Models\Task;
use App\Repositories\Tasks\TaskRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    public function test_it_list_tasks(): void
    {
        $queryMock = Mockery::mock(Builder::class);
        $filters = Mockery::mock(TaskFiltersData::class);
        $pagination = Mockery::mock(PaginationData::class);
        $ordernation = Mockery::mock(OrdernationData::class);
        $tasks = new Collection([
            new Task(['title' => 'Task 1']),
            new Task(['title' => 'Task 2']),
        ]);

        $queryMock->shouldReceive('count')
            ->once()
            ->andReturn(10);

        $filters->shouldReceive('apply')
            ->once()
            ->with($queryMock);

        $queryMock->shouldReceive('count')
            ->once()
            ->andReturn(5);

        $ordernation->shouldReceive('apply')
            ->once()
            ->with($queryMock);
        $pagination->shouldReceive('apply')
            ->once()
            ->with($queryMock);

        $queryMock->shouldReceive('get')
            ->once()
            ->andReturn($tasks);

        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('query')
            ->once()
            ->andReturn($queryMock);

        $repository = new TaskRepository($taskMock);
        $response = $repository->list($filters, $pagination, $ordernation);

        $this->assertInstanceOf(ListResponseData::class, $response);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(5, $response->total_filtered);
        $this->assertCount(2, $response->data);
    }

    public function test_it_checks_if_task_exists(): void
    {
        $mock = Mockery::mock(Task::class);
        $mock->shouldReceive('whereId')->with(1)->once()->andReturnSelf();
        $mock->shouldReceive('exists')->once()->andReturnTrue();

        $repository = new TaskRepository($mock);
        $this->assertTrue($repository->exists(1));
    }

    public function test_it_creates_a_task_successfully(): void
    {
        $data = Mockery::mock(TaskData::class);
        $data->shouldReceive('toArray')->once()->andReturn([
            'title' => 'New Task',
            'description' => 'Some desc',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('fill')->once()->andReturnSelf();
        $taskMock->shouldReceive('save')->once()->andReturnTrue();

        $repository = new TaskRepository($taskMock);
        $task = $repository->create($data);

        $this->assertInstanceOf(Task::class, $task);
    }

    public function test_it_throws_exception_if_task_creation_fails(): void
    {
        $data = Mockery::mock(TaskData::class);
        $data->shouldReceive('toArray')->once()->andReturn([
            'title' => 'fail',
        ]);

        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('fill')->once()->andReturnSelf();
        $taskMock->shouldReceive('save')->once()->andReturnFalse();

        $repository = new TaskRepository($taskMock);

        $this->expectException(CreateException::class);
        $this->expectExceptionMessage('An error occured when trying to create the task!');

        $repository->create($data);
    }

    public function test_it_updates_task_successfully(): void
    {
        $data = Mockery::mock(TaskData::class, [null, 'Updated', 'Updated dec', null, null]);

        $data->shouldReceive('has')->with('title')->andReturnTrue();
        $data->shouldReceive('has')->with('description')->andReturnTrue();
        $data->shouldReceive('has')->with('status')->andReturnFalse();
        $data->shouldReceive('has')->with('due_date')->andReturnFalse();

        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('save')->once()->andReturnTrue();

        $modelMock = Mockery::mock(Task::class);
        $modelMock->shouldReceive('findOrFail')->with(5)->once()->andReturn($taskMock);

        $taskMock->shouldReceive('setAttribute')->with('title', $data->title);
        $taskMock->shouldReceive('setAttribute')->with('description', $data->description);

        $repository = new TaskRepository($modelMock);
        $task = $repository->update(5, $data);

        $this->assertInstanceOf(Task::class, $task);
    }

    public function test_it_throws_exception_if_update_fails(): void
    {
        $data = Mockery::mock(TaskData::class);
        $data->shouldReceive('has')->andReturnFalse();

        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('save')->once()->andReturnFalse();

        $modelMock = Mockery::mock(Task::class);
        $modelMock->shouldReceive('findOrFail')->with(2)->once()->andReturn($taskMock);

        $repository = new TaskRepository($modelMock);

        $this->expectException(UpdateException::class);
        $this->expectExceptionMessage('An error occured when trying to update the task!');

        $repository->update(2, $data);
    }

    public function test_it_finds_a_task(): void
    {
        $model = Mockery::mock(Task::class);
        $model->shouldReceive('findOrFail')->with(9)->once()->andReturn(new Task(['title' => 'X']));

        $repository = new TaskRepository($model);
        $task = $repository->find(9);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('X', $task->title);
    }

    public function test_it_deletes_a_task(): void
    {
        $taskMock = Mockery::mock(Task::class);
        $taskMock->shouldReceive('deleteOrFail')->once();

        $modelMock = Mockery::mock(Task::class);
        $modelMock->shouldReceive('findOrFail')->with(8)->once()->andReturn($taskMock);

        $repository = new TaskRepository($modelMock);
        $repository->delete(8);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
