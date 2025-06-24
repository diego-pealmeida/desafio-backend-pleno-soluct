<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'status'        => $this->status,
            'user'          => new UserResource($this->user),
            'due_date'      => $this->due_date,
            'created_at'    => serializeDate($this->created_at),
            'updated_at'    => serializeDate($this->updated_at)
        ];
    }
}
