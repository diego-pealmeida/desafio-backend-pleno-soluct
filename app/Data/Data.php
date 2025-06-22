<?php

namespace App\Data;

use Spatie\LaravelData\Data as LaravelDataData;

class Data extends LaravelDataData
{
    public function __construct(
        //
    ) {}

    public function has(string $field): bool
    {
        return request()->has($field);
    }
}
