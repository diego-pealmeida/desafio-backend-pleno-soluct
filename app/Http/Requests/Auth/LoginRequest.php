<?php

namespace App\Http\Requests\Auth;

use App\Data\LoginData;
use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email'         => 'required|email',
            'password'      => 'required',
            'remember_me'   => 'nullable|boolean'
        ];
    }

    public function toData(): LoginData
    {
        return new LoginData(
            $this->input('email'),
            $this->input('password'),
            $this->input('remember_me', false)
        );
    }
}
