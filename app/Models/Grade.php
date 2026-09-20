<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['student_id', 'subject_id', 'class_id', 'school_year', 'semester', 'score_tugas', 'score_mid', 'score_semester', 'created_by'])]
class Grade extends Model
{
    protected function casts(): array
    {
        return [
            'score_tugas' => 'float',
            'score_mid' => 'float',
            'score_semester' => 'float',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function finalScore(): ?float
    {
        $parts = array_filter([$this->score_tugas, $this->score_mid, $this->score_semester], fn ($v) => $v !== null);

        return $parts ? round(array_sum($parts) / count($parts), 2) : null;
    }
}
