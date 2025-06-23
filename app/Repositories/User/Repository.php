<?php

namespace App\Repositories\User;

use App\Data\UserData;
use App\Models\User;

interface Repository
{
    public function exists(int $userId): bool;
    public function findById(int $userId): User;
    public function findByEmail(string $email): User|null;
    public function create(UserData $data): User;
}
