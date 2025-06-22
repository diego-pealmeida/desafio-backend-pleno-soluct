<?php

namespace App\Services\Auth;

use App\Data\AccessTokenData;
use App\Data\LoginData;

interface Service
{
    public function login(LoginData $data): AccessTokenData;
    public function revokeToken(): void;
}
