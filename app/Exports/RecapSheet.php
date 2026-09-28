<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RecapSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    public function __construct(private Collection $attendances, private string $sort = 'attendance_number') {}

    public function title(): string
    {
        return 'Rekapitulasi Siswa';
    }

    public function headings(): array
    {
        return ['No Absen', 'NIS', 'NISN', 'Nama Siswa', 'Kelas', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa', 'Total Hari', '% Kehadiran'];
    }

    public function collection(): Collection
    {
        $recap = $this->attendances
            ->groupBy('student_id')
            ->filter()
            ->values()
            ->map(function ($group) {
                $student = $group->first()->student;
                $counts = $group->groupBy('status')->map->count();
                $total = $group->count();
                $hadir = $counts['Hadir'] ?? 0;
                $terlambat = $counts['Terlambat'] ?? 0;

                return [
                    'class_id' => $student->class_id,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->class?->name,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $counts['Izin'] ?? 0,
                    'sakit' => $counts['Sakit'] ?? 0,
                    'alpa' => $counts['Alpa'] ?? 0,
                    'total' => $total,
                    'rate' => $total ? round((($hadir + $terlambat) / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy(fn ($row) => ($row['class'] ?? '').'|'.$row['name'])
            ->values();

        $classNumbers = [];

        return $recap->map(function ($row) use (&$classNumbers) {
            $classKey = $row['class_id'] ?? 'no-class';
            $classNumbers[$classKey] = ($classNumbers[$classKey] ?? 0) + 1;

            return ['attendance_number' => $classNumbers[$classKey], ...$row];
        })->sortBy(function ($row) {
            $class = mb_strtolower($row['class'] ?? '');
            $value = match ($this->sort) {
                'name' => mb_strtolower($row['name']),
                'nisn' => (string) ($row['nisn'] ?? ''),
                default => str_pad((string) $row['attendance_number'], 10, '0', STR_PAD_LEFT),
            };

            return $class.'|'.$value;
        })->values()->map(function ($row) {

            return [
                $row['attendance_number'],
                $row['nis'],
                $row['nisn'],
                $row['name'],
                $row['class'],
                $row['hadir'],
                $row['terlambat'],
                $row['izin'],
                $row['sakit'],
                $row['alpa'],
                $row['total'],
                $row['rate'],
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:L1')->getFont()->setBold(true);
                $event->sheet->getStyle('A1:L1')->getFont()->getColor()->setARGB('FFFFFFFF');
                $event->sheet->getStyle('A1:L1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1A3A3A');
            },
        ];
    }
}
