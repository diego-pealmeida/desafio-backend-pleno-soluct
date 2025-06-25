<?php

namespace Tests\Unit\App\Services\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Exceptions\Task\NotFoundException;
use App\Models\Task;
use App\Repositories\Tasks\Repository as TaskRepositoryInterface;
use App\Services\Tasks\TaskService;
use Mockery;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    public function test_it_lists_tasks()
    {
        $filters = Mockery::mock(TaskFiltersData::class);
        $pagination = Mockery::mock(PaginationData::class);
        $ordernation = Mockery::mock(OrdernationData::class);

        $listResponse = Mockery::mock(ListResponseData::class);

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('list')
            ->with($filters, $pagination, $ordernation)
            ->once()
            ->andReturn($listResponse);

        $service = new TaskService($repositoryMock);

        $result = $service->listTasks($filters, $pagination, $ordernation);

        $this->assertSame($listResponse, $result);
    }

    public function test_it_creates_task()
    {
        $taskData = Mockery::mock(TaskData::class);
        $task = Mockery::mock(Task::class);

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('create')
            ->with($taskData)
            ->once()
            ->andReturn($task);

        $service = new TaskService($repositoryMock);

        $result = $service->createTask($taskData);

        $this->assertSame($task, $result);
    }

    public function test_it_gets_task_successfully()
    {
        $taskId = 123;
        $task = Mockery::mock(Task::class);

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(true);
        $repositoryMock->shouldReceive('find')
            ->with($taskId)
            ->once()
            ->andReturn($task);

        $service = new TaskService($repositoryMock);

        $result = $service->getTask($taskId);

        $this->assertSame($task, $result);
    }

    public function test_it_throws_not_found_exception_if_task_does_not_exist_on_get()
    {
        $taskId = 456;

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(false);

        $service = new TaskService($repositoryMock);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('task not found');

        $service->getTask($taskId);
    }

    public function test_it_updates_task_successfully()
    {
        $taskId = 789;
        $taskData = Mockery::mock(TaskData::class);
        $task = Mockery::mock(Task::class);

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(true);
        $repositoryMock->shouldReceive('update')
            ->with($taskId, $taskData)
            ->once()
            ->andReturn($task);

        $service = new TaskService($repositoryMock);

        $result = $service->updateTask($taskId, $taskData);

        $this->assertSame($task, $result);
    }

    public function test_it_throws_not_found_exception_if_task_does_not_exist_on_update()
    {
        $taskId = 111;
        $taskData = Mockery::mock(TaskData::class);

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(false);

        $service = new TaskService($repositoryMock);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('task not found');

        $service->updateTask($taskId, $taskData);
    }

    public function test_it_deletes_task_successfully()
    {
        $taskId = 222;

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(true);
        $repositoryMock->shouldReceive('delete')
            ->with($taskId)
            ->once();

        $service = new TaskService($repositoryMock);
        $service->deleteTask($taskId);

        $this->assertTrue(true);
    }

    public function test_it_throws_not_found_exception_if_task_does_not_exist_on_delete()
    {
        $taskId = 333;

        $repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $repositoryMock->shouldReceive('exists')
            ->with($taskId)
            ->once()
            ->andReturn(false);

        $service = new TaskService($repositoryMock);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('task not found');

        $service->deleteTask($taskId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
