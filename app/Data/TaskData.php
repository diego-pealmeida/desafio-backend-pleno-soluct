<?php

namespace App\Data;

use App\Enums\TaskStatus;

class TaskData extends Data
{
    public function __construct(
        public readonly ?int $user_id = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?TaskStatus $status = null,
        public readonly ?string $due_date = null
    ) {}
}
