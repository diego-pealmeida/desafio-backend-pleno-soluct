<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Data;

class PaginationData extends Data
{
    public function __construct(
        public readonly int $page = 0,
        public readonly int $limit = 50
    ) {}

    public function getOffset(): int
    {
        return $this->page * $this->limit;
    }

    public function apply(Builder &$builder): void
    {
        $builder
            ->limit($this->limit)
            ->offset($this->getOffset());
    }
}
