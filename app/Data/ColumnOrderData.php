<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ColumnOrderData extends Data
{
    public function __construct(
        public readonly ?string $field,
        public readonly ?string $order = 'asc'
    ) {}
}
