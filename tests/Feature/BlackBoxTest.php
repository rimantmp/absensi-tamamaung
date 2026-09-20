<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlackBoxTest extends TestCase
{
    use RefreshDatabase;

    private function makeRoleUser(string $role, array $overrides = []): User
    {
        Role::findOrCreate($role);
        $user = User::create(array_merge([
            'name' => 'User '.$role,
            'username' => strtolower($role).rand(1, 9999),
            'email' => strtolower($role).rand(1, 9999).'@tamamaung.test',
            'password' => Hash::make('password'),
            'status' => 'active',
        ], $overrides));
        $user->assignRole($role);

        return $user;
    }

    private function makeClass(?User $teacher = null, string $name = '1A'): SchoolClass
    {
        return SchoolClass::create([
            'name' => $name,
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
            'barcode_value' => 'SDIT4-BBTEST1',
            'status' => 'active',
        ], $overrides));
    }

    private function makeSetting(): void
    {
        AttendanceSetting::create([
            'school_start_time' => '07:00:00',
            'late_time_limit' => '23:59:00',
            'active_days' => ['monday', 'tuesday'],
        ]);
    }

    /* =============================== AUTENTIKASI =============================== */

    public function test_blackbox_login_sukses_dengan_username(): void
    {
        $user = $this->makeRoleUser('Admin');

        $this->post('/login', ['login' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_blackbox_login_sukses_dengan_email(): void
    {
        $user = $this->makeRoleUser('Admin');

        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_blackbox_login_gagal_password_salah(): void
    {
        $user = $this->makeRoleUser('Admin');

        $this->post('/login', ['login' => $user->username, 'password' => 'salah'])
            ->assertRedirect('/')
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_blackbox_login_gagal_akun_inactive(): void
    {
        $this->makeRoleUser('Admin', ['username' => 'adminmati', 'status' => 'inactive']);

        $this->post('/login', ['login' => 'adminmati', 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_blackbox_login_gagal_input_kosong(): void
    {
        $this->post('/login', ['login' => '', 'password' => ''])
            ->assertSessionHasErrors(['login', 'password']);

        $this->assertGuest();
    }

    public function test_blackbox_logout_menghapus_session(): void
    {
        $user = $this->makeRoleUser('Admin');

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_blackbox_akses_auth_tanpa_login_redirect(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/students')->assertRedirect(route('login'));
        $this->get('/reports')->assertRedirect(route('login'));
    }

    public function test_blackbox_siswa_login_diarahkan_ke_riwayat(): void
    {
        $user = $this->makeRoleUser('Siswa');

        $this->post('/login', ['login' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertRedirect(route('student.history'));
    }

    public function test_blackbox_siswa_tidak_bisa_buka_halaman_admin(): void
    {
        $siswa = $this->makeRoleUser('Siswa');

        $this->actingAs($siswa)->get('/students')->assertForbidden();
        $this->actingAs($siswa)->get('/classes')->assertForbidden();
        $this->actingAs($siswa)->get('/users')->assertForbidden();
        $this->actingAs($siswa)->get('/settings')->assertForbidden();
        $this->actingAs($siswa)->get('/cards')->assertForbidden();
    }

    public function test_riwayat_siswa_menampilkan_persentase_setiap_status_pada_bulan_terpilih(): void
    {
        $user = $this->makeRoleUser('Siswa');
        $class = $this->makeClass();
        $student = $this->makeStudent($class, ['user_id' => $user->id]);

        foreach (['Hadir', 'Hadir', 'Izin', 'Sakit', 'Terlambat'] as $day => $status) {
            Attendance::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'date' => "2026-09-".str_pad((string) ($day + 1), 2, '0', STR_PAD_LEFT),
                'status' => $status,
                'input_type' => 'manual',
            ]);
        }

        Attendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'date' => '2026-08-31',
            'status' => 'Alpa',
            'input_type' => 'manual',
        ]);

        $this->actingAs($user)->get('/student-history?month=2026-09')
            ->assertOk()
            ->assertSeeText('Kehadiran')
            ->assertSeeText('40%')
            ->assertSeeText('Tidak Hadir')
            ->assertSeeText('0%');
    }

    /* =============================== SCAN =============================== */

    public function test_blackbox_scan_barcode_tidak_dikenal(): void
    {
        $this->makeSetting();

        $this->postJson('/scan', ['barcode_value' => 'SDIT4-TIDAKADA'])
            ->assertStatus(404)
            ->assertJson(['ok' => false]);
    }

    public function test_blackbox_scan_siswa_inactive_ditolak(): void
    {
        $class = $this->makeClass();
        $this->makeStudent($class, ['status' => 'inactive', 'barcode_value' => 'SDIT4-BBTEST1']);
        $this->makeSetting();

        $this->postJson('/scan', ['barcode_value' => 'SDIT4-BBTEST1'])
            ->assertStatus(404)
            ->assertJson(['ok' => false]);
    }

    public function test_blackbox_scan_barcode_kosong_ditolak(): void
    {
        $this->postJson('/scan', ['barcode_value' => ''])->assertStatus(302);

        $this->assertSame(0, Attendance::count());
    }

    public function test_blackbox_scan_internal_guru_kelas_lain_tertolak(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $kelasSendiri = $this->makeClass($guru, '1A');
        $kelasLain = $this->makeClass(null, '9Z');
        $this->makeStudent($kelasLain, ['nis' => '250999', 'nisn' => '990999', 'name' => 'Orang Lain', 'barcode_value' => 'SDIT4-KELASLAIN']);

        $this->actingAs($guru)->postJson('/scan', ['barcode_value' => 'SDIT4-KELASLAIN'])
            ->assertForbidden();
    }

    /* =============================== CRUD SISWA =============================== */

    public function test_blackbox_nis_duplikat_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $this->makeStudent($class);

        $this->actingAs($admin)->post('/students', [
            'name' => 'Duplikat',
            'nis' => '250101',
            'nisn' => '990002',
            'gender' => 'Laki-laki',
            'class_id' => $class->id,
            'status' => 'active',
        ])->assertSessionHasErrors('nis');

        $this->assertSame(1, Student::count());
    }

    public function test_blackbox_tambah_siswa_tanpa_kelas_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');

        $this->actingAs($admin)->post('/students', [
            'name' => 'Tanpa Kelas',
            'nis' => '250202',
            'gender' => 'Laki-laki',
            'class_id' => '',
            'status' => 'active',
        ])->assertSessionHasErrors('class_id');
    }

    public function test_blackbox_guru_tidak_bisa_tambah_siswa(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $class = $this->makeClass();

        $this->actingAs($guru)->post('/students', [
            'name' => 'Andi',
            'nis' => '250303',
            'gender' => 'Laki-laki',
            'class_id' => $class->id,
            'status' => 'active',
        ])->assertForbidden();
    }

    /* =============================== KELAS & PENGGUNA =============================== */

    public function test_blackbox_tambah_kelas_tanpa_nama_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');

        $this->actingAs($admin)->post('/classes', [
            'name' => '',
            'homeroom_teacher_id' => '',
            'academic_year' => '2025/2026',
            'status' => 'active',
        ])->assertSessionHasErrors('name');
    }

    public function test_blackbox_username_duplikat_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');
        Role::findOrCreate('Guru');
        $this->makeRoleUser('Guru', ['username' => 'gurusama']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Guru Baru',
            'username' => 'gurusama',
            'email' => 'baru@tamamaung.test',
            'password' => 'rahasia123',
            'role' => 'Guru',
            'status' => 'active',
        ])->assertSessionHasErrors('username');
    }

    public function test_blackbox_password_kurang_enam_karakter_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');
        Role::findOrCreate('Guru');

        $this->actingAs($admin)->post('/users', [
            'name' => 'Guru Baru',
            'username' => 'gurubaru',
            'email' => 'gurubaru@tamamaung.test',
            'password' => 'abc12',
            'role' => 'Guru',
            'status' => 'active',
        ])->assertSessionHasErrors('password');
    }

    public function test_blackbox_role_tidak_valid_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');

        $this->actingAs($admin)->post('/users', [
            'name' => 'Salah Role',
            'username' => 'salahrole',
            'email' => 'salah@tamamaung.test',
            'password' => 'rahasia123',
            'role' => 'Superadmin',
            'status' => 'active',
        ])->assertSessionHasErrors('role');
    }

    /* =============================== ABSENSI MANUAL =============================== */

    public function test_blackbox_absensi_manual_status_tidak_valid_ditolak(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $student = $this->makeStudent($class);

        $this->actingAs($admin)->post('/manual-attendance', [
            'date' => today()->toDateString(),
            'student_id' => $student->id,
            'status' => 'Bolos',
        ])->assertSessionHasErrors('status');
    }

    public function test_blackbox_absensi_manual_guru_siswa_luar_kelas_tertolak(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $kelasLain = $this->makeClass(null, '9Z');
        $student = $this->makeStudent($kelasLain, ['nis' => '250999', 'nisn' => '990999', 'name' => 'Siswa Lain']);

        $this->actingAs($guru)->post('/manual-attendance', [
            'date' => today()->toDateString(),
            'student_id' => $student->id,
            'status' => 'Hadir',
        ])->assertForbidden();
    }

    /* =============================== PENGATURAN =============================== */

    public function test_blackbox_pengaturan_hanya_admin(): void
    {
        $guru = $this->makeRoleUser('Guru');

        $this->actingAs($guru)->get('/settings')->assertForbidden();
        $this->actingAs($guru)->post('/settings', [
            'school_start_time' => '06:00',
            'late_time_limit' => '06:30',
        ])->assertForbidden();
    }

    /* =============================== LAPORAN & EKSPOR =============================== */

    public function test_blackbox_laporan_menampilkan_data_filter(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $student = $this->makeStudent($class);
        Attendance::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'date' => today(),
            'status' => 'Hadir',
            'input_type' => 'scan',
        ]);

        $this->actingAs($admin)->get('/reports')
            ->assertOk()
            ->assertSeeText('No Absen')
            ->assertSee('Andi Pratama');
    }

    public function test_nomor_absen_laporan_dimulai_dari_satu_pada_setiap_kelas(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $classA = $this->makeClass(null, '4A');
        $classB = $this->makeClass(null, '4B');
        $studentA = $this->makeStudent($classA, [
            'nis' => '400001', 'nisn' => '940001', 'name' => 'Siswa Kelas A', 'barcode_value' => 'SDIT4-KELASA',
        ]);
        $studentB = $this->makeStudent($classB, [
            'nis' => '400002', 'nisn' => '940002', 'name' => 'Siswa Kelas B', 'barcode_value' => 'SDIT4-KELASB',
        ]);

        foreach ([$studentA, $studentB] as $student) {
            Attendance::create([
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'date' => today(),
                'status' => 'Hadir',
                'input_type' => 'scan',
            ]);
        }

        $content = $this->actingAs($admin)->get('/reports')->assertOk()->getContent();

        $this->assertStringContainsString('<tr><td>1</td><td>400001</td><td>Siswa Kelas A</td><td>4A</td>', $content);
        $this->assertStringContainsString('<tr><td>1</td><td>400002</td><td>Siswa Kelas B</td><td>4B</td>', $content);
    }

    public function test_blackbox_ekspor_excel_mengunduh_file(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $this->makeStudent($class);

        $this->actingAs($admin)->get('/reports/excel')
            ->assertOk();
    }

    public function test_blackbox_ekspor_pdf_mengunduh_file(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $this->makeStudent($class);

        $this->actingAs($admin)->get('/reports/pdf')
            ->assertOk();
    }

    public function test_blackbox_cetak_laporan_menampilkan_seluruh_data(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $class = $this->makeClass();
        $student = $this->makeStudent($class);
        Attendance::create(['student_id' => $student->id, 'class_id' => $class->id, 'date' => today(), 'status' => 'Hadir', 'input_type' => 'scan']);

        $this->actingAs($admin)->get('/reports/print')
            ->assertOk()
            ->assertSee('Laporan Absensi Siswa')
            ->assertSee('Andi Pratama')
            ->assertSee('window.print()');
    }

    public function test_blackbox_guru_laporan_hanya_kelas_walinya(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $kelasSendiri = $this->makeClass($guru, '1A');
        $kelasLain = $this->makeClass(null, '9Z');
        $siswaSendiri = $this->makeStudent($kelasSendiri, ['nis' => '250101', 'nisn' => '990001', 'name' => 'Siswa Saya', 'barcode_value' => 'SDIT4-BBSENDIRI']);
        $siswaLain = $this->makeStudent($kelasLain, ['nis' => '250999', 'nisn' => '990999', 'name' => 'Siswa Bukan Saya', 'barcode_value' => 'SDIT4-BBLAIN']);
        Attendance::create(['student_id' => $siswaSendiri->id, 'class_id' => $kelasSendiri->id, 'date' => today(), 'status' => 'Hadir', 'input_type' => 'scan']);
        Attendance::create(['student_id' => $siswaLain->id, 'class_id' => $kelasLain->id, 'date' => today(), 'status' => 'Hadir', 'input_type' => 'scan']);

        $response = $this->actingAs($guru)->get('/reports')
            ->assertOk()
            ->assertSee('Siswa Saya');

        $content = $response->getContent();
        $this->assertStringContainsString('>Siswa Saya</td>', $content);
        $this->assertStringNotContainsString('>Siswa Bukan Saya</td>', $content);
    }

    /* =============================== NILAI & KENAIKAN KELAS =============================== */

    private function makeSubject(string $name = 'Matematika'): Subject
    {
        return Subject::create(['name' => $name, 'code' => 'MTK']);
    }

    public function test_blackbox_admin_mengelola_mata_pelajaran(): void
    {
        $admin = $this->makeRoleUser('Admin');

        $this->actingAs($admin)->post('/subjects', ['name' => 'Bahasa Indonesia', 'code' => 'BIND'])
            ->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', ['name' => 'Bahasa Indonesia']);

        $subject = Subject::where('name', 'Bahasa Indonesia')->first();
        $this->actingAs($admin)->put("/subjects/{$subject->id}", ['name' => 'B. Indonesia'])
            ->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', ['name' => 'B. Indonesia']);

        $this->actingAs($admin)->delete("/subjects/{$subject->id}")
            ->assertRedirect(route('subjects.index'));
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_blackbox_guru_tidak_bisa_kelola_mapel(): void
    {
        $guru = $this->makeRoleUser('Guru');

        $this->actingAs($guru)->get('/subjects')->assertForbidden();
        $this->actingAs($guru)->post('/subjects', ['name' => 'IPA'])->assertForbidden();
    }

    public function test_blackbox_wali_kelas_input_dan_lihat_nilai(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $class = $this->makeClass($guru, '1A');
        $student = $this->makeStudent($class);
        $subject = $this->makeSubject();

        $payload = [
            'class_id' => $class->id,
            'school_year' => '2025/2026',
            'semester' => 'Ganjil',
            'scores' => [$student->id => [$subject->id => ['tugas' => '85', 'mid' => '90', 'semester' => '88']]],
        ];

        $this->actingAs($guru)->post('/grades', $payload)->assertRedirect();

        $grade = Grade::first();
        $this->assertEquals(85, $grade->score_tugas);
        $this->assertEquals(90, $grade->score_mid);
        $this->assertEquals(88, $grade->score_semester);

        $this->actingAs($guru)
            ->get("/grades?class_id={$class->id}&school_year=2025/2026&semester=Ganjil")
            ->assertOk()
            ->assertSee('Andi Pratama');
    }

    public function test_blackbox_guru_lain_tidak_bisa_input_nilai(): void
    {
        $guruA = $this->makeRoleUser('Guru');
        $guruB = $this->makeRoleUser('Guru');
        $class = $this->makeClass($guruA, '1A');
        $student = $this->makeStudent($class);
        $subject = $this->makeSubject();

        $this->actingAs($guruB)->post('/grades', [
            'class_id' => $class->id,
            'school_year' => '2025/2026',
            'semester' => 'Ganjil',
            'scores' => [$student->id => [$subject->id => ['tugas' => '80']]],
        ])->assertForbidden();

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_blackbox_cetak_daftar_nilai(): void
    {
        $guru = $this->makeRoleUser('Guru');
        $class = $this->makeClass($guru, '1A');
        $student = $this->makeStudent($class);
        $subject = $this->makeSubject();
        Grade::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'class_id' => $class->id, 'school_year' => '2025/2026', 'semester' => 'Ganjil', 'score_tugas' => 80, 'score_mid' => 90, 'score_semester' => 85]);

        $this->actingAs($guru)->get("/grades/print?class_id={$class->id}&school_year=2025/2026&semester=Ganjil")
            ->assertOk()
            ->assertSee('Daftar Nilai Siswa')
            ->assertSee('Andi Pratama')
            ->assertSee('85.00')
            ->assertSee('window.print()');
    }

    public function test_blackbox_simpan_keputusan_dan_proses_promosi(): void
    {
        $admin = $this->makeRoleUser('Admin');
        $kelasAwal = $this->makeClass(null, '1A');
        $kelasAwal->update(['level' => 1]);
        $kelasTujuan = $this->makeClass(null, '2A');
        $kelasTujuan->update(['level' => 2]);
        $siswaNaik = $this->makeStudent($kelasAwal, ['nis' => '250101', 'nisn' => '990001', 'barcode_value' => 'SDIT4-PROMO1']);
        $siswaLulus = $this->makeStudent($kelasAwal, ['nis' => '250102', 'nisn' => '990002', 'name' => 'Budi Lulus', 'barcode_value' => 'SDIT4-PROMO2']);

        $this->actingAs($admin)->post('/promotion/decisions', [
            'class_id' => $kelasAwal->id,
            'school_year' => '2025/2026',
            'decision' => [$siswaNaik->id => 'Naik', $siswaLulus->id => 'Lulus'],
        ])->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseHas('promotions', ['student_id' => $siswaNaik->id, 'decision' => 'Naik']);
        $this->assertDatabaseHas('promotions', ['student_id' => $siswaLulus->id, 'decision' => 'Lulus']);

        $this->actingAs($admin)->post('/promotion/process', [
            'class_id' => $kelasAwal->id,
            'target_class_id' => $kelasTujuan->id,
            'school_year' => '2025/2026',
        ])->assertRedirect()->assertSessionHas('ok');

        $this->assertDatabaseHas('students', ['id' => $siswaNaik->id, 'class_id' => $kelasTujuan->id]);
        $this->assertDatabaseHas('students', ['id' => $siswaLulus->id, 'status' => 'inactive']);
    }
}
