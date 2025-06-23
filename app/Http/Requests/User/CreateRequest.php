<?php

namespace App\Http\Requests\User;

use App\Data\UserData;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'      => 'required|string|min:3|max:60',
            'email'     => 'required|email|max:100',
            'password'  => [
                'required',
                Password::min(8)
                ->max(20)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
            ]
        ];
    }

    public function toData(): UserData
    {
        return new UserData(
            $this->input('name'),
            $this->input('email'),
            $this->input('password')
        );
    }
}
