<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class TaskHistoryData extends Data
{
    public function __construct(
        public readonly int $task_id,
        public readonly int $user_id,
        public readonly string $field_changed,
        public readonly string|null $old_value,
        public readonly string|null $new_value,
        public readonly \DateTime $changed_at
    ) {}
}
