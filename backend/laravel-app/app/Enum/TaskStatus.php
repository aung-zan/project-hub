<?php

namespace App\Enum;

enum TaskStatus: string
{
    case Todo = 'todo';
    case Progress = 'progress';
    case Review = 'review';
    case Complete = 'complete';
}
