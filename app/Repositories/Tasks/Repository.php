<?php

namespace App\Repositories\Tasks;

use App\Data\ListResponseData;
use App\Data\OrdernationData;
use App\Data\PaginationData;
use App\Data\TaskData;
use App\Data\TaskFiltersData;
use App\Models\Task;

interface Repository
{
    public function list(TaskFiltersData $filters, PaginationData $pagination, OrdernationData $ordernation): ListResponseData;
    public function exists(int $taskId): bool;
    public function create(TaskData $data): Task;
    public function find(int $taskId): Task;
    public function update(int $taskId, TaskData $data): Task;
    public function delete(int $taskId): void;
}
