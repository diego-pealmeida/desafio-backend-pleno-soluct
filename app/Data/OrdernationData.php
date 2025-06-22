<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Data;

class OrdernationData extends Data
{
    /** @param ColumnOrderData[] $columns */
    public function __construct(
        public readonly array $columns = []
    ) {
        //
    }

    public function apply(Builder &$builder): void
    {
        foreach ($this->columns as $column) {
            $builder->orderBy($column->field, $column->order);
        }
    }
}
