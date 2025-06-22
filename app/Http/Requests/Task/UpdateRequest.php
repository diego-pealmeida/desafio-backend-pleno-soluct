<?php

namespace App\Http\Requests\Task;

use App\Data\TaskData;
use App\Enums\TaskStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title'         => 'filled|string|min:5|max:100',
            'description'   => 'nullable|string',
            'status'        => ['filled', Rule::enum(TaskStatus::class)],
            'due_date'      => 'nullable|date_format:Y-m-d'
        ];
    }

    public function toData(): TaskData
    {
        $status = $this->input('status');

        return new TaskData(
            null,
            $this->input('title'),
            $this->input('description'),
            !empty($status) ? TaskStatus::from($status) : null,
            $this->input('due_date')
        );
    }
}
