<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;

class ListResponseData extends Data
{
    public function __construct(
        public readonly Collection $data,
        public readonly int $total,
        public readonly int $total_filtered
    ) {}
}
