<?php

namespace App\Repositories\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Exceptions\Task\CreateException;
use App\Exceptions\Task\UpdateException;
use App\Models\Task;

class TaskRepository implements Repository
{
    public function __construct(private Task $taskModel) {
        //
    }

    public function list(TaskFiltersData $filters, PaginationData $pagination, OrdernationData $ordernation): ListResponseData
    {
        $taks = $this->taskModel::query();

        $total = $totalFiltered = $taks->count();

        $filters->apply($taks);

        $totalFiltered = $taks->count();

        $ordernation->apply($taks);
        $pagination->apply($taks);

        $result = $taks->get();

        return new ListResponseData($result, $total, $totalFiltered);
    }

    public function exists(int $taskId): bool
    {
        return $this->taskModel->whereId($taskId)->exists();
    }

    public function create(TaskData $data): Task
    {
        $task = new $this->taskModel;
        $task->fill($data->toArray());

        if (!$task->save())
            throw new CreateException('An error occured when trying to create the task!');

        return $task;
    }

    public function find(int $taskId): Task
    {
        return $this->taskModel->findOrFail($taskId);
    }

    public function update(int $taskId, TaskData $data): Task
    {
        $task = $this->find($taskId);

        if ($data->has('title')) $task->title = $data->title;
        if ($data->has('description')) $task->description = $data->description;
        if ($data->has('status')) $task->status = $data->status;
        if ($data->has('due_date')) $task->due_date = $data->due_date;

        if (!$task->save())
            throw new UpdateException('An error occured when trying to update the task!');

        return $task;
    }

    public function delete(int $taskId): void
    {
        $task = $this->find($taskId);

        $task->deleteOrFail();
    }
}
