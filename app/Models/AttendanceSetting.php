<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['school_start_time', 'late_time_limit', 'active_days'])]
class AttendanceSetting extends Model
{
    protected function casts(): array
    {
        return ['active_days' => 'array'];
    }
}
