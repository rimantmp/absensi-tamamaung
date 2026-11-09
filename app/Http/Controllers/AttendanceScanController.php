<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceScanController extends Controller
{
    public function internal()
    {
        abort_unless(Auth::user()->hasAnyRole(['Admin', 'Guru']), 403);

        $setting = AttendanceSetting::first();

        return view('scan.internal', compact('setting'));
    }

    public function public()
    {
        $setting = AttendanceSetting::first();

        return view('scan.public', compact('setting'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['barcode_value' => ['required', 'string']]);
        $student = Student::with('class')->where('barcode_value', $data['barcode_value'])->where('status', 'active')->first();

        if (! $student) {
            return response()->json(['ok' => false, 'message' => 'Barcode/QR tidak ditemukan.'], 404);
        }

        if (Auth::check() && Auth::user()->hasRole('Guru')) {
            abort_if($student->class?->homeroom_teacher_id !== Auth::id(), 403);
        }

        $today = today();
        $now = now();
        $existing = Attendance::with(['student.class'])->where('student_id', $student->id)->whereDate('date', $today)->first();

        if ($existing) {
            if ($existing->check_out_time) {
                return response()->json($this->payload($existing, 'Absensi hari ini sudah lengkap (masuk & pulang).', false, 'Selesai (Sudah Masuk & Pulang)'));
            }

            $existing->update(['check_out_time' => $now->format('H:i:s')]);

            return response()->json($this->payload($existing->refresh(), 'Jam pulang berhasil dicatat.', false, 'Pulang'));
        }

        $setting = AttendanceSetting::first();
        $lateLimit = Carbon::parse(($setting?->late_time_limit ?? '07:15:00'))->setDate($now->year, $now->month, $now->day);
        $status = $now->lte($lateLimit) ? 'Hadir' : 'Terlambat';
        $keterangan = $status === 'Hadir' ? 'Hadir Tepat Waktu' : 'Terlambat';

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'date' => $today,
            'check_in_time' => $now->format('H:i:s'),
            'status' => $status,
            'input_type' => 'scan',
            'created_by' => Auth::id(),
        ])->load('student.class');

        return response()->json($this->payload($attendance, 'Absensi berhasil disimpan.', true, $keterangan));
    }

    private function payload(Attendance $attendance, string $message, bool $created, ?string $keterangan = null): array
    {
        $date = Carbon::parse($attendance->date)->locale('id');
        $checkIn = optional($attendance->check_in_time)->format('H:i') ?? substr((string) $attendance->getRawOriginal('check_in_time'), 0, 5);
        $checkOut = optional($attendance->check_out_time)->format('H:i');
        if (! $checkOut && $attendance->getRawOriginal('check_out_time')) {
            $checkOut = substr((string) $attendance->getRawOriginal('check_out_time'), 0, 5);
        }

        if (! $keterangan) {
            if ($checkOut) {
                $keterangan = 'Pulang';
            } elseif ($attendance->status === 'Hadir') {
                $keterangan = 'Hadir Tepat Waktu';
            } else {
                $keterangan = $attendance->status;
            }
        }

        return [
            'ok' => true,
            'created' => $created,
            'message' => $message,
            'student' => $attendance->student->name,
            'nisn' => $attendance->student->nisn ?? $attendance->student->nis ?? '-',
            'class' => $attendance->student->class->name,
            'day' => $date->translatedFormat('l'),
            'date' => $date->translatedFormat('d F Y'),
            'time' => $checkIn,
            'check_out' => $checkOut,
            'status' => $attendance->status,
            'keterangan' => $keterangan,
            'scan_time' => now()->format('H:i:s'),
        ];
    }
}
