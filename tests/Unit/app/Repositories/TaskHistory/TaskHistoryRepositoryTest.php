<?php

namespace Tests\Unit\App\Repositories\TaskHistory;

use App\Data\TaskHistoryData;
use App\Exceptions\TaskHistory\CreateException;
use App\Models\TaskHistory;
use App\Repositories\TaskHistory\TaskHistoryRepository;
use Mockery;
use PHPUnit\Framework\TestCase;

class TaskHistoryRepositoryTest extends TestCase
{
    private array $taskHistoryArray = [
        'task_id' => 1,
        'user_id' => 2,
        'action' => 'created',
        'timestamp' => '2025-06-23 12:00:00'
    ];

    public function test_it_creates_task_history_successfully(): void
    {
        $data = Mockery::mock(TaskHistoryData::class);
        $data->shouldReceive('toArray')
            ->once()
            ->andReturn($this->taskHistoryArray);

        $taskHistoryMock = Mockery::mock(TaskHistory::class);
        $taskHistoryMock->shouldReceive('fill')
            ->with($this->taskHistoryArray)
            ->once()
            ->andReturnSelf();
        $taskHistoryMock->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $repository = new TaskHistoryRepository($taskHistoryMock);

        $result = $repository->create($data);

        $this->assertInstanceOf(TaskHistory::class, $result);
    }

    public function test_it_throws_exception_if_task_history_not_created(): void
    {
        $data = Mockery::mock(TaskHistoryData::class);
        $data->shouldReceive('toArray')
            ->once()
            ->andReturn($this->taskHistoryArray);
        $data->shouldReceive('toJson')
            ->once()
            ->andReturn(json_encode($this->taskHistoryArray));

        $taskHistoryMock = Mockery::mock(TaskHistory::class);
        $taskHistoryMock->shouldReceive('fill')
            ->with($this->taskHistoryArray)
            ->once()
            ->andReturnSelf();
        $taskHistoryMock->shouldReceive('save')
            ->once()
            ->andReturn(false);

        $repository = new TaskHistoryRepository($taskHistoryMock);

        $this->expectException(CreateException::class);
        $this->expectExceptionMessage('An error occured when trying to create the task history. DATA: ' . json_encode($this->taskHistoryArray));

        $repository->create($data);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
