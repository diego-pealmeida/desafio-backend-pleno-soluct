<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'field_changed',
        'old_value',
        'new_value',
        'changed_at'
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'date:Y-m-d H:i:s'
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
