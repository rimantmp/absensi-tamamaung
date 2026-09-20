<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    use WithoutModelEvents;

    private const STUDENTS_PER_CLASS = 30;

    private const ATTENDANCE_DAYS = 40;

    private array $maleNames = [
        'Andi', 'Budi', 'Candra', 'Dimas', 'Eko', 'Fajar', 'Gilang', 'Hendra',
        'Irwan', 'Joko', 'Rizky', 'Satria', 'Teguh', 'Yoga', 'Zaki', 'Farhan',
    ];

    private array $femaleNames = [
        'Siti', 'Dewi', 'Fitri', 'Gita', 'Indah', 'Kartika', 'Maya', 'Nia',
        'Putri', 'Ratna', 'Sari', 'Winda', 'Zahra', 'Aisyah', 'Bella', 'Dinda',
    ];

    private array $surnames = [
        'Pratama', 'Santoso', 'Wijaya', 'Saputra', 'Nugroho', 'Hidayat', 'Ramadhan',
        'Kusuma', 'Utami', 'Lestari', 'Permata', 'Rahman', 'Setiawan', 'Wulandari',
        'Yulianti', 'Mahardika',
    ];

    private int $nisCounter = 1;

    public function run(): void
    {
        $this->seedRoles();
        $this->user('Admin Operator', 'admin', 'admin@tamamaung.test', 'Admin');
        $this->user('Kepala Sekolah', 'kepsek', 'kepsek@tamamaung.test', 'Kepala Sekolah');

        $classes = $this->seedClasses();
        $students = $this->seedStudents($classes);

        AttendanceSetting::updateOrCreate(['id' => 1], [
            'school_start_time' => '07:00:00',
            'late_time_limit' => '07:15:00',
            'active_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
        ]);

        $admin = User::where('email', 'admin@tamamaung.test')->firstOrFail();
        $this->seedAttendances($admin, $classes, $students);
        $this->seedSubjectsAndGrades($admin, $classes, $students);
    }

    private function seedSubjectsAndGrades(User $admin, $classes, $students): void
    {
        $subjects = collect([
            ['Pendidikan Agama', 'PABP'],
            ['Pendidikan Pancasila', 'PP'],
            ['Bahasa Indonesia', 'BIND'],
            ['Matematika', 'MTK'],
            ['Ilmu Pengetahuan Alam', 'IPA'],
            ['Ilmu Pengetahuan Sosial', 'IPS'],
            ['Bahasa Inggris', 'BING'],
            ['Seni Budaya', 'SB'],
        ])->mapWithKeys(function (array $data) {
            $subject = Subject::firstOrCreate(['name' => $data[0]], ['code' => $data[1]]);

            return [$subject->id => $subject];
        });

        $academicYear = now()->month >= 7
            ? now()->year.'/'.(now()->year + 1)
            : (now()->year - 1).'/'.now()->year;

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $seed = crc32("grade-{$student->id}-{$subject->id}");
                Grade::updateOrCreate([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'school_year' => $academicYear,
                    'semester' => 'Ganjil',
                ], [
                    'class_id' => $student->class_id,
                    'score_tugas' => rand(70, 95) + ($seed % 5) * 0.5,
                    'score_mid' => rand(65, 98) + ($seed % 3) * 0.5,
                    'score_semester' => rand(68, 97) + ($seed % 4) * 0.5,
                    'created_by' => $admin->id,
                ]);
            }
        }
    }

    private function seedRoles(): void
    {
        foreach (['Admin', 'Guru', 'Kepala Sekolah', 'Siswa'] as $role) {
            Role::findOrCreate($role);
        }
    }

    private function seedClasses(): Collection
    {
        $academicYear = now()->year.'/'.(now()->year + 1);
        $classes = collect();

        foreach (['4A', '4B'] as $name) {
            $teacher = $name === '4A'
                ? $this->user('Ibu Wali Kelas 4A', 'guru', 'guru@tamamaung.test', 'Guru')
                : $this->user('Pak Wali Kelas 4B', 'guru.4b', 'guru.4b@tamamaung.test', 'Guru');

            $classes->push(SchoolClass::firstOrCreate([
                'name' => $name,
                'academic_year' => $academicYear,
            ], [
                'level' => 4,
                'homeroom_teacher_id' => $teacher->id,
                'status' => 'active',
            ]));
        }

        return $classes;
    }

    private function seedStudents($classes): Collection
    {
        $students = collect();

        foreach ($classes as $class) {
            for ($i = 0; $i < self::STUDENTS_PER_CLASS; $i++) {
                $name = $this->studentName();
                $nis = str_pad((string) $this->nisCounter, 6, '0', STR_PAD_LEFT);
                $username = strtolower(str_replace(' ', '.', $name));
                $account = $this->user($name, $username, "{$username}@siswa.test", 'Siswa');

                $students->push(Student::firstOrCreate(['nis' => $nis], [
                    'user_id' => $account->id,
                    'nisn' => (string) (9900000000 + $this->nisCounter),
                    'name' => $name,
                    'gender' => $this->nisCounter % 2 === 0 ? 'Perempuan' : 'Laki-laki',
                    'class_id' => $class->id,
                    'barcode_value' => 'SDIT4-'.$nis,
                    'status' => 'active',
                ]));

                $this->nisCounter++;
            }
        }

        return $students;
    }

    private function seedAttendances(User $admin, $classes, $students): void
    {
        $dates = [];
        $date = now();
        while (count($dates) < self::ATTENDANCE_DAYS) {
            if (! $date->isSunday()) {
                $dates[] = $date->format('Y-m-d');
            }
            $date = $date->subDay();
        }

        $classTeachers = $classes->mapWithKeys(fn ($class) => [$class->id => $class->homeroom_teacher_id]);

        foreach ($students as $student) {
            foreach ($dates as $dateStr) {
                $seed = crc32($student->id.'-'.$dateStr);
                $status = $this->statusFor($seed);
                $checkIn = $this->checkInTime($status, $seed);
                $checkOut = $this->checkOutTime($status, $seed);
                $inputType = $seed % 100 < 85 ? 'scan' : 'manual';
                $createdBy = $inputType === 'manual'
                    ? ($classTeachers[$student->class_id] ?? $admin->id)
                    : $admin->id;

                Attendance::updateOrCreate(['student_id' => $student->id, 'date' => $dateStr], [
                    'class_id' => $student->class_id,
                    'check_in_time' => $checkIn,
                    'check_out_time' => $checkOut,
                    'status' => $status,
                    'input_type' => $inputType,
                    'note' => $this->noteFor($status),
                    'created_by' => $createdBy,
                ]);
            }
        }
    }

    private function studentName(): string
    {
        $index = $this->nisCounter - 1;
        $first = $index % 2 === 0
            ? $this->maleNames[$index % count($this->maleNames)]
            : $this->femaleNames[$index % count($this->femaleNames)];
        $surname = $this->surnames[($index + intdiv($index, count($this->maleNames))) % count($this->surnames)];

        return "{$first} {$surname}";
    }

    private function statusFor(int $seed): string
    {
        $roll = $seed % 100;

        if ($roll < 80) {
            return 'Hadir';
        }
        if ($roll < 90) {
            return 'Terlambat';
        }
        if ($roll < 94) {
            return 'Izin';
        }
        if ($roll < 98) {
            return 'Sakit';
        }

        return 'Alpa';
    }

    private function checkInTime(string $status, int $seed): ?string
    {
        if (! in_array($status, ['Hadir', 'Terlambat'])) {
            return null;
        }

        $baseMinutes = $status === 'Hadir' ? 400 : 436; // 06:40 / 07:16
        $range = $status === 'Hadir' ? 35 : 44;
        $minutes = $baseMinutes + ($seed % $range);

        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }

    private function checkOutTime(string $status, int $seed): ?string
    {
        if (! in_array($status, ['Hadir', 'Terlambat'])) {
            return null;
        }

        $minutes = 780 + ($seed % 40); // 13:00 - 13:39

        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }

    private function noteFor(string $status): ?string
    {
        return match ($status) {
            'Izin' => 'Izin keluarga',
            'Sakit' => 'Sakit',
            'Alpa' => 'Tanpa keterangan',
            default => null,
        };
    }

    private function user(string $name, string $username, string $email, string $role): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'username' => $username,
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $user->syncRoles([$role]);

        return $user;
    }
}
