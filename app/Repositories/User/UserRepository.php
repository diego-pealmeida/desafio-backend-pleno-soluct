<?php

namespace App\Repositories\User;

use App\Data\UserData;
use App\Exceptions\User\CreateException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements Repository
{
    public function __construct(private User $model) {
        //
    }

    public function findByEmail(string $email): User|null
    {
        return $this->model
            ->whereRaw('LOWER(email) = ?', mb_strtolower($email, 'UTF-8'))
            ->first();
    }

    public function create(UserData $data): User
    {
        $user = $this->model;
        $user->fill($data->toArray());

        $user->password = Hash::make($user->password);

        if (!$user->save())
            throw new CreateException('An error occured when trying to create the user');

        return $user;
    }
}
