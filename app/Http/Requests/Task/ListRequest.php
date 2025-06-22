<?php

namespace App\Http\Requests\Task;

use App\Data\TaskFiltersData;
use App\Enums\TaskStatus;
use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['columns_order.*.field'] = 'string|in:title,status,due_date,created_at,updated_at';

        return array_merge([
            'title'                     => 'nullable|string',
            'status'                    => ['nullable', Rule::enum(TaskStatus::class)],
            'created_at_start'          => 'nullable|date_format:Y-m-d|before_or_equal:created_at_end',
            'created_at_end'            => 'nullable|date_format:Y-m-d|after_or_equal:created_at_start',
            'due_date_start'            => 'nullable|date_format:Y-m-d|before_or_equal:due_date_end',
            'due_date_end'              => 'nullable|date_format:Y-m-d|after_or_equal:due_date_start'
        ], $rules);
    }

    public function toData(): TaskFiltersData
    {
        $status = $this->input('status');

        return new TaskFiltersData(
            $this->input('title'),
            !empty($status) ? TaskStatus::from($status) : null,
            $this->input('created_at_start'),
            $this->input('created_at_end'),
            $this->input('due_date_start'),
            $this->input('due_date_end')
        );
    }
}
