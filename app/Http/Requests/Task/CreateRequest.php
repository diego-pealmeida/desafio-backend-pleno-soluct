<?php

namespace App\Http\Requests\Task;

use App\Data\TaskData;
use App\Enums\TaskStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title'         => 'required|string|min:5|max:100',
            'description'   => 'nullable|string',
            'status'        => ['required', Rule::enum(TaskStatus::class)],
            'due_date'      => 'nullable|date_format:Y-m-d'
        ];
    }

    public function toData(): TaskData
    {
        return new TaskData(
            $this->user()->id,
            $this->input('title'),
            $this->input('description'),
            TaskStatus::from($this->input('status')),
            $this->input('due_date')
        );
    }
}
