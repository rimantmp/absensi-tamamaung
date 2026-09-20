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

        return view('scan.internal');
    }

    public function public()
    {
        return view('scan.public');
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
                return response()->json($this->payload($existing, 'Absensi hari ini sudah lengkap (masuk & pulang).', false));
            }

            $existing->update(['check_out_time' => $now->format('H:i:s')]);

            return response()->json($this->payload($existing->refresh(), 'Jam pulang berhasil dicatat.', false));
        }

        $setting = AttendanceSetting::first();
        $lateLimit = Carbon::parse(($setting?->late_time_limit ?? '07:15:00'))->setDate($now->year, $now->month, $now->day);
        $status = $now->lte($lateLimit) ? 'Hadir' : 'Terlambat';

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'date' => $today,
            'check_in_time' => $now->format('H:i:s'),
            'status' => $status,
            'input_type' => 'scan',
            'created_by' => Auth::id(),
        ])->load('student.class');

        return response()->json($this->payload($attendance, 'Absensi berhasil disimpan.', true));
    }

    private function payload(Attendance $attendance, string $message, bool $created): array
    {
        return [
            'ok' => true,
            'created' => $created,
            'message' => $message,
            'student' => $attendance->student->name,
            'class' => $attendance->student->class->name,
            'time' => optional($attendance->check_in_time)->format('H:i') ?? substr((string) $attendance->getRawOriginal('check_in_time'), 0, 5),
            'check_out' => optional($attendance->check_out_time)->format('H:i'),
            'status' => $attendance->status,
        ];
    }
}
