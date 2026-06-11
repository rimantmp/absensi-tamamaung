<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendancesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $attendances)
    {
    }

    public function collection(): Collection
    {
        return $this->attendances->map(fn ($attendance) => [
            $attendance->date->format('Y-m-d'),
            $attendance->student->name,
            $attendance->student->nis,
            $attendance->student->class->name,
            $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-',
            $attendance->status,
            $attendance->input_type,
            $attendance->note,
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama', 'NIS', 'Kelas', 'Jam', 'Status', 'Input', 'Catatan'];
    }
}
