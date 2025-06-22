<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
