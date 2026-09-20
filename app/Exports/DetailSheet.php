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

class DetailSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithTitle
{
    public function __construct(private Collection $attendances) {}

    public function title(): string
    {
        return 'Detail Absensi';
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status', 'Input', 'Catatan'];
    }

    public function collection(): Collection
    {
        return $this->attendances->values()->map(fn ($a, $i) => [
            $i + 1,
            $a->date->format('d/m/Y'),
            $a->student->nis,
            $a->student->name,
            $a->student->class?->name,
            $a->check_in_time ? $a->check_in_time->format('H:i') : '-',
            $a->check_out_time ? $a->check_out_time->format('H:i') : '-',
            $a->status,
            $a->input_type,
            $a->note ?? '-',
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:J1')->getFont()->setBold(true);
                $event->sheet->getStyle('A1:J1')->getFont()->getColor()->setARGB('FFFFFFFF');
                $event->sheet->getStyle('A1:J1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1A3A3A');
            },
        ];
    }
}
