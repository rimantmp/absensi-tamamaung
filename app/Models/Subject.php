<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code'])]
class Subject extends Model
{
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
