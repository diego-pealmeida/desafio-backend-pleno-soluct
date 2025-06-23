<?php

namespace App\Jobs;

use App\Data\TaskData;
use App\Data\TaskHistoryData;
use App\Repositories\TaskHistory\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CreateTaskHistories implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $taskId,
        private TaskData $current,
        private TaskData $original,
        private ?\DateTime $changedAt
    )
    {
        //
    }

    public function handle(Repository $repository): void
    {
        foreach ($this->original as $field => $value) {
            if ($value != $this->current->$field)
                $this->createHistory($field, $repository);
        }
    }

    public function createHistory(string $field, Repository $repository): void
    {
        $oldValue = $this->original->$field;
        $newValue = $this->current->$field;

        if ($field == 'status') {
            $oldValue = $oldValue->value;
            $newValue = $newValue->value;
        }

        $data = new TaskHistoryData(
            $this->taskId,
            $this->current->user_id,
            $field,
            $oldValue,
            $newValue,
            $this->changedAt
        );

        try {
            $repository->create($data);
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
