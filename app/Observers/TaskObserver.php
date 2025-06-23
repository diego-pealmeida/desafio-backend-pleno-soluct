<?php

namespace App\Observers;

use App\Data\TaskData;
use App\Jobs\CreateTaskHistories;
use App\Models\Task;

class TaskObserver
{
    public function updated(Task $task)
    {
        $current = new TaskData(
            $task->user_id,
            $task->title,
            $task->description,
            $task->status,
            $task->due_date
        );

        $original = new TaskData(
            $task->getOriginal('user_id'),
            $task->getOriginal('title'),
            $task->getOriginal('description'),
            $task->getOriginal('status'),
            $task->getOriginal('due_date')
        );

        CreateTaskHistories::dispatch($task->id, $current, $original, $task->updated_at)
            ->onQueue('high');
    }
}
