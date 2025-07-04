<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'access_token'  => $this->access_token,
            'expires_at'    => serializeDate($this->expires_at)
        ];
    }
}
