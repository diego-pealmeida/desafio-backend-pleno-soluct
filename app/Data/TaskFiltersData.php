<?php

namespace App\Data;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Data;

class TaskFiltersData extends Data
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?TaskStatus $status = null,
        public readonly ?string $createdAtStart = null,
        public readonly ?string $createdAtEnd = null,
        public readonly ?string $dueDateStart = null,
        public readonly ?string $dueDateAtEnd = null
    ) {}

    public function apply(Builder &$builder): void
    {
        $builder->when($this->title, function ($cond) {
            $cond->where('title', 'ilike', "%{$this->title}%");
        })
        ->when($this->status, function ($cond) {
            $cond->where('status', $this->status);
        })
        ->when($this->createdAtStart, function ($cond) {
            $cond->whereDate('created_at', '>=', $this->createdAtStart);
        })
        ->when($this->createdAtEnd, function ($cond) {
            $cond->whereDate('created_at', '<=', $this->createdAtEnd);
        })
        ->when($this->dueDateStart, function ($cond) {
            $cond->where('due_date', '>=', $this->dueDateStart);
        })
        ->when($this->dueDateAtEnd, function ($cond) {
            $cond->where('due_date', '<=', $this->dueDateAtEnd);
        });
    }
}
