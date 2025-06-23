<?php

namespace App\Repositories\TaskHistory;

use App\Data\TaskHistoryData;
use App\Exceptions\TaskHistory\CreateException;
use App\Models\TaskHistory;

class TaskHistoryRepository implements Repository
{
    public function __construct(private TaskHistory $model) {
        //
    }

    public function create(TaskHistoryData $data): TaskHistory
    {
        $history = new $this->model;
        $history->fill($data->toArray());

        if (!$history->save())
            throw new CreateException('An error occured when trying to create the task history. DATA: ' . $data->toJson());

        return $history;
    }
}
