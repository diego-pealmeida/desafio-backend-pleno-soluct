<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements Repository
{
    public function __construct(private User $model) {
        //
    }

    public function exists(int $userId): bool
    {
        return $this->model->whereId($userId)->exists();
    }

    public function findById(int $userId): User
    {
        return $this->model->find($userId);
    }

    public function findByEmail(string $email): User|null
    {
        return $this->model
            ->where('email', 'ilike', "%{$email}%")
            ->first();
    }
}
