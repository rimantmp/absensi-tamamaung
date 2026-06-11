<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Guru', 'Kepala Sekolah', 'Siswa'];
        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }

        $admin = $this->user('Admin Operator', 'admin', 'admin@tamamaung.test', 'Admin');
        $teacher = $this->user('Ibu Wali Kelas 1A', 'guru', 'guru@tamamaung.test', 'Guru');
        $headmaster = $this->user('Kepala Sekolah', 'kepsek', 'kepsek@tamamaung.test', 'Kepala Sekolah');

        $classA = SchoolClass::firstOrCreate([
            'name' => '1A',
            'academic_year' => '2025/2026',
        ], [
            'homeroom_teacher_id' => $teacher->id,
            'status' => 'active',
        ]);

        $classB = SchoolClass::firstOrCreate([
            'name' => '2A',
            'academic_year' => '2025/2026',
        ], [
            'homeroom_teacher_id' => null,
            'status' => 'active',
        ]);

        $students = [
            ['Andi Pratama', '250101', '990001', 'Laki-laki', $classA],
            ['Siti Nurhaliza', '250102', '990002', 'Perempuan', $classA],
            ['Budi Santoso', '250201', '990003', 'Laki-laki', $classB],
        ];

        foreach ($students as [$name, $nis, $nisn, $gender, $class]) {
            $account = $this->user($name, strtolower(str_replace(' ', '.', $name)), strtolower(str_replace(' ', '.', $name)).'@siswa.test', 'Siswa');
            Student::firstOrCreate(['nis' => $nis], [
                'user_id' => $account->id,
                'nisn' => $nisn,
                'name' => $name,
                'gender' => $gender,
                'class_id' => $class->id,
                'barcode_value' => 'SDIT4-'.$nis,
                'status' => 'active',
            ]);
        }

        AttendanceSetting::firstOrCreate([], [
            'school_start_time' => '07:00:00',
            'late_time_limit' => '07:15:00',
            'active_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
        ]);
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
