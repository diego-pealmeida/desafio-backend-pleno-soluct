<?php

namespace App\Services\Auth;

use App\Data\AccessTokenData;
use App\Data\LoginData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\Auth\Repository;
use App\Repositories\User\Repository as UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService implements Service
{
    public function __construct(
        private Repository $authRepository,
        private UserRepository $userRepository
    ) {
        //
    }

    public function login(LoginData $data): AccessTokenData
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password))
            throw new InvalidCredentialsException("email or password is invalid");

        $expiredAt = $data->remember_me ? null : now()->addHours(4);

        $personalAccessToken = $user->createToken(User::API_TOKEN_NAME, ['*'], $expiredAt);

        return new AccessTokenData($personalAccessToken->plainTextToken, $expiredAt);
    }

    public function revokeToken(): void
    {
        if (!$this->authRepository->exists(User::API_TOKEN_NAME))
            return;

        $this->authRepository->revokeToken(User::API_TOKEN_NAME);
    }
}
