<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private function makeRoleUser(string $role): User
    {
        Role::findOrCreate($role);
        $user = User::create([
            'name' => 'User '.$role,
            'username' => strtolower($role),
            'email' => strtolower($role).'@tamamaung.test',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeClass(?User $teacher = null): SchoolClass
    {
        return SchoolClass::create([
            'name' => '1A',
            'homeroom_teacher_id' => $teacher?->id,
            'academic_year' => '2025/2026',
            'status' => 'active',
        ]);
    }

    private function makeStudent(SchoolClass $class, array $overrides = []): Student
    {
        return Student::create(array_merge([
            'nis' => '250101',
            'nisn' => '990001',
            'name' => 'Andi Pratama',
            'gender' => 'Laki-laki',
            'class_id' => $class->id,
            'barcode_value' => 'SDIT4-TEST1',
            'status' => 'active',
        ], $overrides));
    }

    public function test_login_berhasil_menyimpan_session(): void
    {
        $user = $this->makeRoleUser('Admin');

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_data_siswa_berhasil_disimpan(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();

        $response = $this->actingAs($admin)->post('/students', [
            'name' => 'Budi Santoso',
            'nis' => '250202',
            'nisn' => '990202',
            'gender' => 'Laki-laki',
            'class_id' => $class->id,
            'user_id' => '',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['nis' => '250202', 'name' => 'Budi Santoso', 'class_id' => $class->id]);

        $student = Student::where('nis', '250202')->first();
        $this->assertNotNull($student->barcode_value);
        $this->assertStringStartsWith('SDIT4-', $student->barcode_value);
    }

    public function test_data_siswa_berhasil_diperbarui_dan_barcode_tidak_berubah(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $student = $this->makeStudent($class, ['barcode_value' => 'SDIT4-ORIGINAL']);

        $response = $this->actingAs($admin)->put('/students/'.$student->id, [
            'name' => 'Andi Pratama Baru',
            'nis' => '250101',
            'nisn' => '990001',
            'gender' => 'Perempuan',
            'class_id' => $class->id,
            'user_id' => '',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Andi Pratama Baru', 'status' => 'inactive']);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'barcode_value' => 'SDIT4-ORIGINAL']);
    }

    public function test_data_kelas_berhasil_disimpan(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $teacher = $this->makeRoleUser('Guru');

        $response = $this->actingAs($admin)->post('/classes', [
            'name' => '3A',
            'homeroom_teacher_id' => $teacher->id,
            'academic_year' => '2025/2026',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('classes', ['name' => '3A', 'homeroom_teacher_id' => $teacher->id]);
    }

    public function test_data_pengguna_berhasil_disimpan_dengan_role(): void
    {
        $admin = $this->makeRoleUser('Admin');
        Role::findOrCreate('Guru');

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Guru Baru',
            'username' => 'gurubaru',
            'email' => 'gurubaru@tamamaung.test',
            'password' => 'rahasia123',
            'role' => 'Guru',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['username' => 'gurubaru']);

        $user = User::where('username', 'gurubaru')->first();
        $this->assertTrue($user->hasRole('Guru'));
        $this->assertNotEquals('rahasia123', $user->password);
    }

    public function test_absensi_scan_berhasil_disimpan(): void
    {
        $class = $this->makeClass();
        $this->makeStudent($class, ['barcode_value' => 'SDIT4-TEST1']);
        AttendanceSetting::create([
            'school_start_time' => '07:00:00',
            'late_time_limit' => '23:59:00',
        ]);

        $response = $this->postJson('/scan', ['barcode_value' => 'SDIT4-TEST1']);

        $response->assertOk()->assertJson(['ok' => true, 'student' => 'Andi Pratama']);
        $this->assertDatabaseHas('attendances', [
            'student_id' => Student::where('barcode_value', 'SDIT4-TEST1')->first()->id,
            'status' => 'Hadir',
            'input_type' => 'scan',
        ]);
        $this->assertSame(1, Attendance::count());
    }

    public function test_absensi_scan_berulang_tidak_membuat_data_duplikat(): void
    {
        $class = $this->makeClass();
        $this->makeStudent($class, ['barcode_value' => 'SDIT4-TEST1']);

        $this->postJson('/scan', ['barcode_value' => 'SDIT4-TEST1']);
        $response = $this->postJson('/scan', ['barcode_value' => 'SDIT4-TEST1']);

        $response->assertOk()->assertJson(['ok' => true, 'created' => false]);
        $this->assertSame(1, Attendance::count());
    }

    public function test_absensi_manual_berhasil_disimpan(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $student = $this->makeStudent($class);

        $response = $this->actingAs($admin)->post('/manual-attendance', [
            'date' => today()->toDateString(),
            'student_id' => $student->id,
            'status' => 'Izin',
            'note' => 'Demam',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'date' => today()->toDateString(),
            'status' => 'Izin',
            'input_type' => 'manual',
            'note' => 'Demam',
        ]);
    }

    public function test_pengaturan_jam_berhasil_disimpan(): void
    {
        $admin = $this->makeRoleUser('Admin');

        $response = $this->actingAs($admin)->post('/settings', [
            'school_start_time' => '06:45',
            'late_time_limit' => '07:05',
            'active_days' => ['monday', 'tuesday'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_settings', [
            'school_start_time' => '06:45:00',
            'late_time_limit' => '07:05:00',
        ]);
    }
}
