<?php

namespace App\Http\Controllers;

use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AppController extends Controller
{
    public function dashboard()
    {
        $today = today();
        $attendances = Attendance::whereDate('date', $today);

        if (Auth::user()->hasRole('Guru')) {
            $classIds = SchoolClass::where('homeroom_teacher_id', Auth::id())->pluck('id');
            $attendances->whereIn('class_id', $classIds);
        }

        if (Auth::user()->hasRole('Siswa')) {
            return redirect()->route('student.history');
        }

        $base = clone $attendances;
        $classSummary = SchoolClass::withCount(['students as total_students' => fn ($q) => $q->where('status', 'active')])
            ->withCount(['students as attended_today' => fn ($q) => $q->whereHas('attendances', fn ($a) => $a->whereDate('date', $today))])
            ->get();

        return view('dashboard', [
            'totalStudents' => Student::where('status', 'active')->count(),
            'counts' => [
                'Hadir' => (clone $base)->where('status', 'Hadir')->count(),
                'Terlambat' => (clone $base)->where('status', 'Terlambat')->count(),
                'Izin' => (clone $base)->where('status', 'Izin')->count(),
                'Sakit' => (clone $base)->where('status', 'Sakit')->count(),
                'Alpa' => (clone $base)->where('status', 'Alpa')->count(),
            ],
            'notYet' => Student::where('status', 'active')->whereDoesntHave('attendances', fn ($q) => $q->whereDate('date', $today))->with('class')->limit(12)->get(),
            'classSummary' => $classSummary,
        ]);
    }

    public function students(Request $request)
    {
        $this->adminOnly();
        $students = Student::with(['class', 'user'])
            ->when($request->q, fn ($q, $term) => $q->where(fn ($s) => $s->where('name', 'like', "%{$term}%")->orWhere('nis', 'like', "%{$term}%")->orWhere('nisn', 'like', "%{$term}%")))
            ->when($request->class_id, fn ($q, $id) => $q->where('class_id', $id))
            ->latest()
            ->paginate(15);

        return view('students.index', ['students' => $students, 'classes' => SchoolClass::where('status', 'active')->get()]);
    }

    public function studentForm(?Student $student = null)
    {
        $this->adminOnly();

        return view('students.form', ['student' => $student, 'classes' => SchoolClass::all(), 'users' => User::role('Siswa')->get()]);
    }

    public function saveStudent(Request $request, ?Student $student = null)
    {
        $this->adminOnly();
        $student ??= new Student();
        $data = $request->validate([
            'name' => ['required'],
            'nis' => ['required', Rule::unique('students')->ignore($student)],
            'nisn' => ['nullable', Rule::unique('students')->ignore($student)],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'class_id' => ['required', 'exists:classes,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['barcode_value'] = $student->barcode_value ?: 'SDIT4-'.strtoupper(str()->random(12));
        $student->fill($data)->save();

        return redirect()->route('students.index')->with('ok', 'Data siswa tersimpan.');
    }

    public function classes()
    {
        $this->adminOnly();

        return view('classes.index', ['classes' => SchoolClass::with('homeroomTeacher')->withCount('students')->get(), 'teachers' => User::role('Guru')->get()]);
    }

    public function saveClass(Request $request, ?SchoolClass $class = null)
    {
        $this->adminOnly();
        $class ??= new SchoolClass();
        $class->fill($request->validate([
            'name' => ['required'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'academic_year' => ['required'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]))->save();

        return back()->with('ok', 'Data kelas tersimpan.');
    }

    public function users()
    {
        $this->adminOnly();

        return view('users.index', ['users' => User::with('roles')->latest()->get()]);
    }

    public function saveUser(Request $request)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => ['required'],
            'username' => ['required', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', Rule::in(['Admin', 'Guru', 'Kepala Sekolah', 'Siswa'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = User::create([...$data, 'password' => Hash::make($data['password'])]);
        $user->assignRole($data['role']);

        return back()->with('ok', 'Akun pengguna dibuat.');
    }

    public function cards(Request $request)
    {
        $this->adminOnly();
        $students = Student::with('class')->when($request->class_id, fn ($q, $id) => $q->where('class_id', $id))->get();

        return view('cards.index', ['students' => $students, 'classes' => SchoolClass::all()]);
    }

    public function myCard()
    {
        $student = Auth::user()->student;
        abort_unless($student, 404);

        return view('cards.mine', ['student' => $student->load('class')]);
    }

    public function history(Request $request)
    {
        $student = Auth::user()->student;
        abort_unless($student, 404);
        $month = $request->month ?: now()->format('Y-m');

        return view('student.history', [
            'month' => $month,
            'attendances' => $student->attendances()->where('date', 'like', "{$month}%")->latest('date')->get(),
        ]);
    }

    public function manual()
    {
        abort_unless(Auth::user()->hasAnyRole(['Admin', 'Guru']), 403);
        $classes = Auth::user()->hasRole('Guru') ? SchoolClass::where('homeroom_teacher_id', Auth::id())->get() : SchoolClass::all();

        return view('attendance.manual', ['classes' => $classes, 'students' => Student::with('class')->whereIn('class_id', $classes->pluck('id'))->get()]);
    }

    public function saveManual(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['Admin', 'Guru']), 403);
        $data = $request->validate([
            'date' => ['required', 'date'],
            'student_id' => ['required', 'exists:students,id'],
            'status' => ['required', Rule::in(['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'])],
            'note' => ['nullable'],
        ]);
        $student = Student::findOrFail($data['student_id']);
        if (Auth::user()->hasRole('Guru')) {
            abort_if($student->class?->homeroom_teacher_id !== Auth::id(), 403);
        }

        Attendance::updateOrCreate(['student_id' => $student->id, 'date' => $data['date']], [
            'class_id' => $student->class_id,
            'check_in_time' => in_array($data['status'], ['Hadir', 'Terlambat']) ? now()->format('H:i:s') : null,
            'status' => $data['status'],
            'input_type' => 'manual',
            'note' => $data['note'],
            'created_by' => Auth::id(),
        ]);

        return back()->with('ok', 'Absensi manual tersimpan.');
    }

    public function reports(Request $request)
    {
        $query = $this->reportQuery($request);
        $attendances = $query->latest('date')->paginate(20)->withQueryString();

        return view('reports.index', ['attendances' => $attendances, 'classes' => SchoolClass::all(), 'students' => Student::all()]);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new AttendancesExport($this->reportQuery($request)->get()), 'laporan-absensi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $attendances = $this->reportQuery($request)->get();

        return Pdf::loadView('reports.pdf', compact('attendances'))->download('laporan-absensi.pdf');
    }

    public function settings()
    {
        $this->adminOnly();

        return view('settings.index', ['setting' => AttendanceSetting::firstOrCreate([])]);
    }

    public function saveSettings(Request $request)
    {
        $this->adminOnly();
        AttendanceSetting::firstOrCreate([])->update($request->validate([
            'school_start_time' => ['required'],
            'late_time_limit' => ['required'],
            'active_days' => ['nullable', 'array'],
        ]));

        return back()->with('ok', 'Pengaturan jam masuk tersimpan.');
    }

    private function reportQuery(Request $request)
    {
        $query = Attendance::with(['student.class']);
        if (Auth::user()->hasRole('Guru')) {
            $query->whereIn('class_id', SchoolClass::where('homeroom_teacher_id', Auth::id())->pluck('id'));
        }
        $query->when($request->date, fn ($q, $date) => $q->whereDate('date', $date))
            ->when($request->month, fn ($q, $month) => $q->where('date', 'like', "{$month}%"))
            ->when($request->class_id, fn ($q, $id) => $q->where('class_id', $id))
            ->when($request->student_id, fn ($q, $id) => $q->where('student_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status));

        return $query;
    }

    private function adminOnly(): void
    {
        abort_unless(Auth::user()->hasRole('Admin'), 403);
    }
}
