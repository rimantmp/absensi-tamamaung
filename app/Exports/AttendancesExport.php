<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendancesExport implements WithMultipleSheets
{
    public function __construct(private Collection $attendances, private string $sort = 'attendance_number') {}

    public function sheets(): array
    {
        return [
            new DetailSheet($this->attendances),
            new RecapSheet($this->attendances, $this->sort),
        ];
    }
}
