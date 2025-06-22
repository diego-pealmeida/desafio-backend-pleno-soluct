<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AccessTokenData extends Data
{
    public function __construct(
        public readonly string $access_token,
        public readonly ?\DateTime $expires_at = null
    ) {}
}
