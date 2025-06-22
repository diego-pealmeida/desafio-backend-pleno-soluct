<?php

namespace App\Repositories\Auth;

use App\Exceptions\Auth\RevokeTokenException;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthRepository implements Repository
{
    public function exists(string $name): bool
    {
        return Auth::user()
            ->tokens()
            ->whereName($name)
            ->exists();
    }

    public function getToken(string $name): PersonalAccessToken
    {
        return Auth::user()
            ->tokens()
            ->whereName($name)
            ->first();
    }

    public function revokeToken(string $name): void
    {
        $token = $this->getToken($name);

        if ($token->expires_at && $token->expires_at <= now())
            return;

        $token->expires_at = now();

        if (!$token->save())
            throw new RevokeTokenException(
                "An error occured when trying to revoke the $name token. USERID: " . Auth::user()->id
            );
    }
}
