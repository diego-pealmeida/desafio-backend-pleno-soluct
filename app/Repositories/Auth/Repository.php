<?php

namespace App\Repositories\Auth;

use Laravel\Sanctum\PersonalAccessToken;

interface Repository
{
    public function exists(string $name): bool;
    public function getToken(string $name): PersonalAccessToken;
    public function revokeToken(string $name): void;
}
