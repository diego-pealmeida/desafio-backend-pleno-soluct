<?php

namespace App\Services\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Models\Task;

interface Service
{
    public function listTasks(TaskFiltersData $filters, PaginationData $pagination, OrdernationData $ordernation): ListResponseData;
    public function createTask(TaskData $data): Task;
    public function getTask(int $taskId): Task;
    public function updateTask(int $taskId, TaskData $data): Task;
    public function deleteTask(int $taskId): void;
}
