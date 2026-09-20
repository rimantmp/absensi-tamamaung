<?php

namespace App\Http\Controllers;

use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Grade;
use App\Models\Promotion;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

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
            ->paginate(15)
            ->withQueryString();

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
        $student ??= new Student;
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

        return view('classes.index', ['classes' => SchoolClass::with('homeroomTeacher')->withCount('students')->latest()->get()]);
    }

    public function classForm(?SchoolClass $class = null)
    {
        $this->adminOnly();

        return view('classes.form', ['class' => $class, 'teachers' => User::role('Guru')->get()]);
    }

    public function saveClass(Request $request, ?SchoolClass $class = null)
    {
        $this->adminOnly();
        $class ??= new SchoolClass;
        $class->fill($request->validate([
            'name' => ['required'],
            'level' => ['nullable', 'integer', 'min:1', 'max:6'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'academic_year' => ['required'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]))->save();

        return redirect()->route('classes.index')->with('ok', 'Data kelas tersimpan.');
    }

    public function destroyClass(SchoolClass $class)
    {
        $this->adminOnly();
        $class->delete();

        return redirect()->route('classes.index')->with('ok', 'Data kelas beserta seluruh siswanya telah dihapus.');
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
        $attendances = $student->attendances()
            ->where('date', 'like', "{$month}%")
            ->latest('date')
            ->get();
        $totalAttendances = $attendances->count();
        $percentages = collect(['Hadir', 'Izin', 'Sakit', 'Terlambat', 'Alpa'])
            ->mapWithKeys(fn ($status) => [
                $status => $totalAttendances
                    ? round(($attendances->where('status', $status)->count() / $totalAttendances) * 100, 1)
                    : 0,
            ])
            ->all();

        return view('student.history', [
            'month' => $month,
            'attendances' => $attendances,
            'percentages' => $percentages,
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
        $base = $this->reportQuery($request);
        $attendances = (clone $base)->latest('date')->paginate(20)->withQueryString();

        return view('reports.index', array_merge($this->reportContext($request, $base->get()), [
            'attendances' => $attendances,
            'classes' => SchoolClass::all(),
            'students' => Student::all(),
        ]));
    }

    public function printReport(Request $request)
    {
        $rows = $this->reportQuery($request)->latest('date')->get();
        $payload = array_merge($this->reportContext($request, $rows), [
            'user' => auth()->user()->name,
            'attendances' => $rows,
            'autoPrint' => true,
        ]);

        return response()->view('reports.pdf', $payload);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new AttendancesExport($this->reportQuery($request)->get()), 'laporan-absensi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        @ini_set('memory_limit', '768M');

        $rows = $this->reportQuery($request)->latest('date')->get();
        $payload = array_merge($this->reportContext($request, $rows), [
            'user' => auth()->user()->name,
            'attendances' => $rows,
        ]);

        if ($chrome = $this->chromeBinary()) {
            try {
                return $this->pdfResponse($this->chromePdf($chrome, view('reports.pdf', $payload)->render()));
            } catch (\Throwable $e) {
                // lanjut ke fallback dompdf
            }
        }

        $documents = [];
        $first = true;

        foreach ($rows->chunk(400) as $chunk) {
            $data = $payload;
            $data['attendances'] = $chunk;

            $document = ($first
                ? Pdf::loadView('reports.pdf', $data)
                : Pdf::loadView('reports.pdf-detail', $data))->setPaper('a4', 'landscape');

            $documents[] = $document->output();
            $first = false;
            unset($data, $document);
            gc_collect_cycles();
        }

        $bytes = count($documents) === 1
            ? $documents[0]
            : $this->mergePdfChunks($documents);

        return $this->pdfResponse($bytes);
    }

    private function pdfResponse(string $bytes): Response
    {
        return response($bytes)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan-absensi.pdf"');
    }

    private function chromeBinary(): ?string
    {
        $env = env('CHROME_PATH');
        if ($env && is_file($env)) {
            return $env;
        }

        foreach ([
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
            'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/opt/google/chrome/chrome',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function chromePdf(string $binary, string $html): string
    {
        $tmpHtml = storage_path('app/report-render.html');
        $tmpPdf = storage_path('app/report-render.pdf');
        file_put_contents($tmpHtml, $html);

        try {
            $proc = proc_open([
                $binary,
                '--headless=new',
                '--disable-gpu',
                '--no-sandbox',
                '--no-pdf-header-footer',
                '--print-to-pdf='.$tmpPdf,
                'file:///'.str_replace('\\', '/', $tmpHtml),
            ], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exit = proc_close($proc);

            if ($exit !== 0 || ! is_file($tmpPdf)) {
                throw new \RuntimeException('Cetak PDF gagal: '.$stderr);
            }

            return file_get_contents($tmpPdf);
        } finally {
            @unlink($tmpHtml);
            @unlink($tmpPdf);
        }
    }

    private function mergePdfChunks(array $documents): string
    {
        $paths = [];

        try {
            foreach ($documents as $index => $bytes) {
                $path = storage_path("app/report-part-{$index}.pdf");
                file_put_contents($path, $bytes);
                $paths[] = $path;
            }

            $merged = new Fpdi('P', 'mm');

            foreach ($paths as $path) {
                $pageCount = $merged->setSourceFile($path);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $template = $merged->importPage($pageNo);
                    $size = $merged->getTemplateSize($template);
                    $merged->addPage($size['orientation'] === 'L' ? 'L' : 'P', [$size['width'], $size['height']]);
                    $merged->useTemplate($template);
                }
            }

            return $merged->output('S');
        } finally {
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }
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
        if ($request->date) {
            $query->whereDate('date', $request->date);
        } elseif ($request->week) {
            $start = Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY);
            $query->whereBetween('date', [$start->toDateString(), $start->copy()->endOfWeek(Carbon::SUNDAY)->toDateString()]);
        } elseif ($request->month) {
            $query->where('date', 'like', "{$request->month}%");
        }
        $query->when($request->class_id, fn ($q, $id) => $q->where('class_id', $id))
            ->when($request->student_id, fn ($q, $id) => $q->where('student_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status));

        return $query;
    }

    private function reportContext(Request $request, $rows): array
    {
        $class = $request->class_id ? SchoolClass::find($request->class_id) : null;
        $student = $request->student_id ? Student::find($request->student_id) : null;

        return [
            'period' => $this->periodLabel($request),
            'class' => $class?->name,
            'student' => $student?->name,
            'status' => $request->status ?: null,
            'summary' => $this->statusSummary($rows),
            'recapByClass' => $this->recapByClass($rows),
            'recapByStudent' => $request->student_id ? collect() : $this->recapByStudent($rows),
        ];
    }

    private function periodLabel(Request $request): string
    {
        if ($request->date) {
            return 'Tanggal '.Carbon::parse($request->date)->format('d/m/Y');
        }
        if ($request->week) {
            $start = Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY);

            return 'Minggu '.$start->format('d/m/Y').' s.d. '.$start->copy()->endOfWeek(Carbon::SUNDAY)->format('d/m/Y');
        }
        if ($request->month) {
            return 'Bulan '.Carbon::parse($request->month.'-01')->format('m/Y');
        }

        return 'Seluruh periode';
    }

    private function statusSummary($rows): array
    {
        $counts = array_fill_keys(['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'], 0);

        foreach ($rows as $row) {
            if (array_key_exists($row->status, $counts)) {
                $counts[$row->status]++;
            }
        }

        $counts['total'] = $rows->count();
        $counts['rate'] = $counts['total']
            ? round((($counts['Hadir'] + $counts['Terlambat']) / $counts['total']) * 100, 1)
            : 0;

        return $counts;
    }

    private function recapByClass($rows): Collection
    {
        return $rows->groupBy('class_id')->map(function ($group) {
            return $this->recapRow([
                'name' => $group->first()->class?->name ?? 'Tanpa kelas',
                ...$this->statusCounts($group),
            ]);
        })->values()->sortBy('name')->values();
    }

    private function recapByStudent($rows): Collection
    {
        $recap = $rows->groupBy('student_id')->filter()->map(function ($group) {
            $student = $group->first()->student;

            return $this->recapRow([
                'class_id' => $student->class_id,
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->class?->name,
                ...$this->statusCounts($group),
            ]);
        })->values()->sortBy(fn ($row) => ($row['class'] ?? '').'|'.$row['name'])->values();

        $classNumbers = [];

        return $recap->map(function ($row) use (&$classNumbers) {
            $classKey = $row['class_id'] ?? 'no-class';
            $classNumbers[$classKey] = ($classNumbers[$classKey] ?? 0) + 1;
            $row['attendance_number'] = $classNumbers[$classKey];

            return $row;
        });
    }

    private function statusCounts($group): array
    {
        $counts = $group->groupBy('status')->map->count();

        return [
            'Hadir' => $counts['Hadir'] ?? 0,
            'Terlambat' => $counts['Terlambat'] ?? 0,
            'Izin' => $counts['Izin'] ?? 0,
            'Sakit' => $counts['Sakit'] ?? 0,
            'Alpa' => $counts['Alpa'] ?? 0,
            'total' => $group->count(),
        ];
    }

    private function recapRow(array $row): array
    {
        $row['rate'] = $row['total']
            ? round((($row['Hadir'] + $row['Terlambat']) / $row['total']) * 100, 1)
            : 0;

        return $row;
    }

    public function subjects()
    {
        $this->adminOnly();

        return view('subjects.index', ['subjects' => Subject::withCount('grades')->orderBy('name')->get()]);
    }

    public function saveSubject(Request $request, ?Subject $subject = null)
    {
        $this->adminOnly();
        $subject ??= new Subject;
        $subject->fill($request->validate([
            'name' => ['required', Rule::unique('subjects')->ignore($subject)],
            'code' => ['nullable', 'max:16'],
        ]))->save();

        return redirect()->route('subjects.index')->with('ok', 'Mata pelajaran tersimpan.');
    }

    public function destroySubject(Subject $subject)
    {
        $this->adminOnly();
        $subject->delete();

        return redirect()->route('subjects.index')->with('ok', 'Mata pelajaran beserta seluruh nilainya telah dihapus.');
    }

    public function grades(Request $request)
    {
        $classes = $this->managedClasses();
        $class = $classes->firstWhere('id', $request->class_id) ?? ($request->class_id ? SchoolClass::find($request->class_id) : null);
        if ($request->class_id) {
            $this->authorizeClass($class);
        }
        $schoolYear = $request->school_year ?: $this->currentSchoolYear();
        $semester = in_array($request->semester, ['Ganjil', 'Genap']) ? $request->semester : $this->currentSemester();

        $students = collect();
        $grades = collect();

        if ($class) {
            $students = $class->students()->where('status', 'active')->orderBy('name')->get();
            $grades = Grade::where('class_id', $class->id)->where('school_year', $schoolYear)->where('semester', $semester)->get()->keyBy(fn ($g) => "{$g->student_id}:{$g->subject_id}");
        }

        return view('grades.index', [
            'classes' => $classes,
            'class' => $class,
            'subjects' => Subject::orderBy('name')->get(),
            'students' => $students,
            'grades' => $grades,
            'schoolYear' => $schoolYear,
            'semester' => $semester,
        ]);
    }

    public function saveGrades(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'school_year' => ['required', 'max:16'],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
            'scores' => ['nullable', 'array'],
            'scores.*.*.tugas' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.*.mid' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.*.semester' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->authorizeClass(SchoolClass::find($data['class_id']));

        $saved = 0;
        foreach (($data['scores'] ?? []) as $studentId => $perSubject) {
            foreach ($perSubject as $subjectId => $types) {
                $values = array_map(
                    fn ($v) => ($v === null || $v === '') ? null : round((float) $v, 2),
                    [$types['tugas'] ?? null, $types['mid'] ?? null, $types['semester'] ?? null]
                );

                if (! array_filter($values, fn ($v) => $v !== null)) {
                    Grade::where(['student_id' => $studentId, 'subject_id' => $subjectId, 'school_year' => $data['school_year'], 'semester' => $data['semester']])->delete();

                    continue;
                }

                Grade::updateOrCreate([
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'school_year' => $data['school_year'],
                    'semester' => $data['semester'],
                ], [
                    'class_id' => $data['class_id'],
                    'score_tugas' => $values[0],
                    'score_mid' => $values[1],
                    'score_semester' => $values[2],
                    'created_by' => Auth::id(),
                ]);
                $saved++;
            }
        }

        return back()->with('ok', "Nilai tersimpan ({$saved} entri).");
    }

    public function printGrades(Request $request)
    {
        $class = SchoolClass::findOrFail($request->class_id);
        $this->authorizeClass($class);
        $schoolYear = $request->school_year ?: $this->currentSchoolYear();
        $semester = in_array($request->semester, ['Ganjil', 'Genap']) ? $request->semester : $this->currentSemester();
        $students = $class->students()->where('status', 'active')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $grades = Grade::where('class_id', $class->id)->where('school_year', $schoolYear)->where('semester', $semester)->get()->keyBy(fn ($g) => "{$g->student_id}:{$g->subject_id}");

        return response()->view('grades.print', [
            'class' => $class,
            'subjects' => $subjects,
            'students' => $students,
            'grades' => $grades,
            'schoolYear' => $schoolYear,
            'semester' => $semester,
            'autoPrint' => true,
        ]);
    }

    public function promotion(Request $request)
    {
        $classes = $this->managedClasses();
        abort_if($classes->isEmpty(), 403, 'Anda bukan wali kelas.');
        $class = $classes->firstWhere('id', $request->class_id);
        $schoolYear = $request->school_year ?: $this->currentSchoolYear();
        $semester = in_array($request->semester, ['Ganjil', 'Genap']) ? $request->semester : $this->currentSemester();

        $students = collect();
        $averages = collect();
        $decisions = collect();

        if ($class) {
            $this->authorizeClass($class);
            $students = $class->students()->where('status', 'active')->orderBy('name')->get();
            $decisions = Promotion::whereIn('student_id', $students->pluck('id'))->where('school_year', $schoolYear)->get()->keyBy('student_id');
            $gradeRows = Grade::whereIn('student_id', $students->pluck('id'))->where('school_year', $schoolYear)->where('semester', $semester)->get()->groupBy('student_id');
            $averages = $students->mapWithKeys(function ($s) use ($gradeRows) {
                $finals = $gradeRows->get($s->id, collect())->map->finalScore()->filter();

                return [$s->id => $finals->isNotEmpty() ? round($finals->avg(), 2) : null];
            });
        }

        return view('promotion.index', [
            'classes' => $classes,
            'class' => $class,
            'students' => $students,
            'averages' => $averages,
            'decisions' => $decisions,
            'targets' => SchoolClass::where('status', 'active')->where('level', '>', $class?->level ?? 0)->orderBy('level')->orderBy('name')->get(),
            'schoolYear' => $schoolYear,
            'semester' => $semester,
        ]);
    }

    public function savePromotionDecisions(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'school_year' => ['required', 'max:16'],
            'average' => ['nullable', 'array'],
            'decision' => ['required', 'array'],
            'decision.*' => ['nullable', Rule::in(['Naik', 'Tinggal', 'Lulus'])],
        ]);

        $this->authorizeClass(SchoolClass::find($data['class_id']));

        $count = 0;
        foreach ($data['decision'] as $studentId => $decision) {
            if (! $decision) {
                continue;
            }
            $student = Student::find($studentId);
            if (! $student || $student->class_id !== (int) $data['class_id']) {
                continue;
            }
            Promotion::updateOrCreate([
                'student_id' => $studentId,
                'school_year' => $data['school_year'],
            ], [
                'from_class_id' => $student->class_id,
                'to_class_id' => null,
                'decision' => $decision,
                'average' => isset($data['average'][$studentId]) && $data['average'][$studentId] !== '' ? round((float) $data['average'][$studentId], 2) : null,
                'decided_by' => Auth::id(),
            ]);
            $count++;
        }

        return back()->with('ok', "Keputusan kenaikan disimpan untuk {$count} siswa.");
    }

    public function processPromotion(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'target_class_id' => ['required', 'exists:classes,id'],
            'school_year' => ['required', 'max:16'],
        ]);

        $fromClass = SchoolClass::find($data['class_id']);
        $this->authorizeClass($fromClass);
        abort_if($fromClass->id === (int) $data['target_class_id'], 422, 'Kelas tujuan tidak boleh sama.');

        $promotions = Promotion::where('from_class_id', $fromClass->id)->where('school_year', $data['school_year'])->whereNull('to_class_id')->with('student')->get();
        $moved = $graduated = 0;

        foreach ($promotions as $promo) {
            $student = $promo->student;
            if (! $student || $student->class_id !== $fromClass->id) {
                continue;
            }
            if ($promo->decision === 'Lulus') {
                $student->update(['status' => 'inactive']);
                $graduated++;
            } elseif ($promo->decision === 'Naik') {
                $student->update(['class_id' => $data['target_class_id']]);
                $moved++;
            } else {
                continue;
            }
            $promo->update(['to_class_id' => $promo->decision === 'Naik' ? $data['target_class_id'] : $promo->from_class_id]);
        }

        return back()->with('ok', "Promosi selesai: {$moved} siswa naik kelas, {$graduated} siswa lulus.");
    }

    private function adminOnly(): void
    {
        abort_unless(Auth::user()->hasRole('Admin'), 403);
    }

    private function managedClasses(): Collection
    {
        if (Auth::user()->hasRole('Admin')) {
            return SchoolClass::where('status', 'active')->orderBy('level')->orderBy('name')->get();
        }

        return SchoolClass::where('homeroom_teacher_id', Auth::id())->where('status', 'active')->orderBy('name')->get();
    }

    private function authorizeClass(?SchoolClass $class): void
    {
        abort_unless($class && (Auth::user()->hasRole('Admin') || $class->homeroom_teacher_id === Auth::id()), 403);
    }

    private function currentSchoolYear(): string
    {
        $y = (int) now()->format('Y');
        $m = (int) now()->format('n');

        return $m >= 7 ? "{$y}/".($y + 1) : ($y - 1)."/{$y}";
    }

    private function currentSemester(): string
    {
        return (int) now()->format('n') >= 7 ? 'Ganjil' : 'Genap';
    }
}
