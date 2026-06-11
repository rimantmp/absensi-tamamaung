<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['student_id', 'class_id', 'date', 'check_in_time', 'status', 'input_type', 'note', 'created_by'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_time' => 'datetime:H:i:s',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
