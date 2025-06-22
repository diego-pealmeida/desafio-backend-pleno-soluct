<?php

namespace App\Services\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Exceptions\Task\NotFoundException;
use App\Models\Task;
use App\Repositories\Tasks\Repository;

class TaskService implements Service
{
    public function __construct(private Repository $taskRepository) {
        //
    }

    public function listTasks(TaskFiltersData $filters, PaginationData $pagination, OrdernationData $ordernation): ListResponseData
    {
        return $this->taskRepository->list($filters, $pagination, $ordernation);
    }

    public function createTask(TaskData $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function getTask(int $taskId): Task
    {
        if (!$this->taskRepository->exists($taskId))
            throw new NotFoundException('task not found');

        return $this->taskRepository->find($taskId);
    }

    public function updateTask(int $taskId, TaskData $data): Task
    {
        if (!$this->taskRepository->exists($taskId))
            throw new NotFoundException('task not found');

        return $this->taskRepository->update($taskId, $data);
    }

    public function deleteTask(int $taskId): void
    {
        if (!$this->taskRepository->exists($taskId))
            throw new NotFoundException('task not found');

        $this->taskRepository->delete($taskId);
    }
}
