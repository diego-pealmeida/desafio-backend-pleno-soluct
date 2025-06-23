<?php

namespace App\Repositories\TaskHistory;

use App\Data\TaskHistoryData;
use App\Models\TaskHistory;

interface Repository
{
    public function create(TaskHistoryData $data): TaskHistory;
}
