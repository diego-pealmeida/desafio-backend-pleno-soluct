<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'created_at'    => serializeDate($this->created_at),
            'updated_at'    => serializeDate($this->updated_at)
        ];
    }
}
