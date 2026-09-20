Anda adalah senior full-stack engineer dan UI engineer. Bangun sebuah sistem kehadiran siswa berbasis website untuk SD Inpres Tamamaung IV menggunakan barcode/QR code.

Ikuti sepenuhnya dokumen berikut:
- PRD.md sebagai sumber kebutuhan produk, fitur, role, alur, data model, batasan, dan MVP.
- desain.md sebagai sumber gaya visual, warna, tipografi, spacing, radius, komponen, dan prinsip desain.

Tujuan aplikasi:
Membuat sistem absensi siswa yang memungkinkan pencatatan kehadiran menggunakan barcode/QR. Admin/operator dan guru dapat melakukan scan absensi melalui area internal, sedangkan siswa dapat melakukan absensi mandiri tanpa login melalui halaman scan publik. Siswa hanya perlu login untuk melihat riwayat absensi pribadi dan mencetak ulang barcode/QR miliknya sendiri.

Ruang lingkup wajib:
- Login pengguna untuk area internal dan fitur pribadi siswa.
- Role: Admin/Operator, Guru/Wali Kelas, Kepala Sekolah, Siswa.
- Manajemen data siswa.
- Manajemen data kelas.
- Manajemen data pengguna.
- Generate barcode/QR unik untuk setiap siswa.
- Cetak kartu barcode/QR siswa oleh admin per siswa atau per kelas.
- Cetak ulang kartu barcode/QR oleh siswa setelah login.
- Scan absensi internal oleh admin/guru menggunakan kamera browser dan/atau barcode scanner USB.
- Absensi mandiri siswa tanpa login menggunakan barcode/QR.
- Riwayat absensi siswa setelah login.
- Absensi manual oleh admin/guru.
- Pengaturan jam masuk dan batas terlambat.
- Dashboard absensi hari ini.
- Rekap absensi harian, bulanan, per kelas, dan per siswa.
- Export laporan Excel.
- Cetak/export laporan PDF.

Batasan penting:
- Jangan membuat fitur notifikasi orang tua.
- Jangan membuat WhatsApp gateway.
- Jangan membuat SMS gateway.
- Jangan membuat pembayaran, nilai, jadwal pelajaran, e-learning, atau sistem akademik lain.
- Fokus hanya pada absensi siswa.
- Siswa tidak perlu login untuk melakukan absensi.
- Siswa hanya login untuk melihat riwayat absensi pribadi dan mencetak ulang barcode/QR miliknya sendiri.

Tech stack yang digunakan:
- Backend: Laravel.
- Frontend: Blade + Livewire.
- Styling: Tailwind CSS.
- Database: MySQL/MariaDB.
- Role permission: Spatie Laravel Permission.
- QR/barcode scanner: html5-qrcode.
- Export Excel: Laravel Excel.
- PDF: DomPDF.
- QR/barcode generator: gunakan package Laravel yang sesuai.

Role dan hak akses:

1. Admin/Operator:
   - Kelola siswa.
   - Kelola kelas.
   - Kelola pengguna.
   - Generate dan cetak barcode/QR siswa.
   - Cetak barcode/QR per siswa atau per kelas.
   - Scan absensi.
   - Input/ubah absensi manual semua siswa.
   - Lihat dan export semua laporan.
   - Kelola pengaturan jam masuk.

2. Guru/Wali Kelas:
   - Scan absensi.
   - Input absensi manual untuk kelasnya sendiri.
   - Melihat rekap dan laporan kelasnya sendiri.
   - Export/cetak laporan kelasnya sendiri.

3. Kepala Sekolah:
   - Melihat dashboard.
   - Melihat laporan semua kelas.
   - Export/cetak laporan.
   - Tidak boleh mengubah data siswa, kelas, pengguna, atau absensi.

4. Siswa:
   - Melakukan absensi mandiri tanpa login menggunakan barcode/QR.
   - Login hanya untuk melihat riwayat absensi pribadi.
   - Login untuk mencetak ulang kartu barcode/QR miliknya sendiri.
   - Tidak dapat melihat data siswa lain.
   - Tidak dapat menginput absensi manual.
   - Tidak dapat melihat laporan sekolah/kelas.
   - Tidak dapat export laporan.

Aturan barcode/QR:
- Setiap siswa wajib memiliki satu barcode/QR unik.
- Barcode/QR digunakan sebagai identitas absensi siswa.
- Kode barcode/QR tidak boleh sama antar siswa.
- Admin dapat mencetak barcode/QR per siswa atau per kelas.
- Siswa dapat mencetak ulang barcode/QR miliknya sendiri setelah login.
- Siswa tidak dapat melihat atau mencetak barcode/QR siswa lain.
- Kartu barcode/QR minimal memuat nama siswa, kelas, NIS/NISN, dan barcode/QR.

Aturan absensi:
- Satu siswa hanya boleh memiliki satu data absensi per tanggal.
- Jika scan sebelum atau tepat pada batas terlambat, status menjadi "Hadir".
- Jika scan setelah batas terlambat, status menjadi "Terlambat".
- Status absensi manual: Hadir, Terlambat, Izin, Sakit, Alpa.
- Siswa tidak dapat menginput status Izin, Sakit, atau Alpa secara mandiri.
- Status Izin, Sakit, dan Alpa hanya dapat diinput oleh admin atau guru melalui absensi manual.
- Setiap absensi manual harus menyimpan user pembuat dan catatan opsional.
- Jika barcode/QR sudah pernah discan pada tanggal yang sama, tampilkan data absensi hari itu yang sudah tercatat, jangan membuat duplikat.
- Halaman absensi mandiri publik hanya menampilkan informasi terbatas setelah scan: nama siswa, kelas, waktu scan, dan status absensi.
- Halaman absensi mandiri publik tidak boleh menampilkan riwayat absensi atau data siswa lain.

Desain UI:
Gunakan gaya dari desain.md:
- Canvas utama cream `#fffaf0`.
- Teks utama near-black `#0a0a0a`.
- CTA utama near-black dengan teks putih.
- Gunakan kartu berwarna dari palet Clay: pink, teal, lavender, peach, ochre, dan cream.
- Gunakan radius 12px untuk button/input, 16px untuk card biasa, 24px untuk feature/stat card.
- Gunakan Inter sebagai font utama.
- Tampilan harus terasa hangat, ramah untuk sekolah dasar, tetapi tetap rapi sebagai dashboard operasional.
- Semua halaman internal setelah login wajib menggunakan layout dashboard dengan sidebar/sidenav di sisi kiri sebagai navigasi utama.
- Jangan menggunakan top nav/topbar sebagai navigasi utama pada halaman internal admin/operator, guru, kepala sekolah, atau halaman pribadi siswa.
- Sidebar/sidenav harus menampilkan menu sesuai role pengguna.
- Halaman Absensi Mandiri publik siswa adalah pengecualian: halaman ini tidak perlu sidebar/sidenav internal dan boleh memakai header/action minimal agar area scan langsung terlihat.
- Jangan membuat landing page marketing. Layar pertama setelah login harus berupa dashboard aplikasi sesuai role.
- Halaman Absensi Mandiri publik harus langsung menampilkan area scan yang jelas.
- Jangan membuat dekorasi berlebihan yang mengganggu fungsi absensi.
- Pastikan responsive untuk desktop dan mobile.
- Gunakan layout dashboard yang jelas: sidebar/sidenav, ringkasan statistik, tabel, filter, dan area scan.

Halaman/modul yang harus dibuat:

1. Dashboard
   - Total siswa aktif.
   - Hadir hari ini.
   - Terlambat hari ini.
   - Izin.
   - Sakit.
   - Alpa.
   - Siswa belum absen.
   - Rekap singkat per kelas.

2. Data Siswa
   - Tabel siswa.
   - Tambah/edit/nonaktifkan siswa.
   - Hubungkan siswa dengan akun pengguna siswa.
   - Filter nama, NIS/NISN, kelas, status.
   - Detail siswa.
   - Barcode/QR unik siswa.

3. Data Kelas
   - Tabel kelas.
   - Tambah/edit/nonaktifkan kelas.
   - Wali kelas.
   - Tahun ajaran.
   - Daftar siswa per kelas.

4. Data Pengguna
   - Admin dapat mengelola akun.
   - Role Admin, Guru, Kepala Sekolah, Siswa.
   - Status aktif/nonaktif.

5. Barcode/QR Siswa
   - Generate kode unik.
   - Preview kartu siswa.
   - Cetak kartu per siswa.
   - Cetak kartu per kelas.

6. Kartu Barcode Saya
   - Hanya dapat diakses siswa setelah login.
   - Menampilkan barcode/QR milik siswa yang sedang login.
   - Siswa dapat mencetak barcode/QR miliknya sendiri.
   - Tidak menampilkan barcode/QR siswa lain.

7. Scan Absensi Internal
   - Hanya untuk admin/guru.
   - Area kamera untuk scan barcode/QR.
   - Input alternatif untuk barcode scanner USB.
   - Tampilkan data siswa setelah scan.
   - Tampilkan status Hadir/Terlambat otomatis.
   - Tampilkan pesan jika sudah absen.

8. Absensi Mandiri Siswa
   - Dapat diakses tanpa login.
   - Area kamera untuk scan barcode/QR.
   - Input alternatif untuk barcode scanner USB.
   - Setelah scan, tampilkan informasi terbatas: nama, kelas, waktu, status.
   - Jangan tampilkan riwayat absensi.
   - Jangan tampilkan data siswa lain.

9. Riwayat Absensi Siswa
   - Hanya dapat diakses siswa setelah login.
   - Menampilkan riwayat absensi milik siswa yang sedang login.
   - Filter berdasarkan bulan.
   - Siswa tidak dapat mengubah, menghapus, atau mengekspor data absensi.

10. Absensi Manual
   - Pilih tanggal.
   - Pilih kelas.
   - Pilih siswa.
   - Pilih status.
   - Catatan opsional.
   - Simpan sesuai hak akses role.

11. Laporan Absensi
   - Laporan harian.
   - Laporan bulanan.
   - Laporan per kelas.
   - Laporan per siswa.
   - Filter tanggal, bulan, kelas, siswa, status.
   - Export Excel.
   - Export PDF/cetak.

12. Pengaturan Jam Masuk
   - Jam masuk sekolah.
   - Batas terlambat.
   - Hari aktif sekolah.

Data model minimal:
- users: id, name, username/email, password, role, status.
- roles/permissions menggunakan Spatie.
- students: id, user_id, nis, nisn, name, gender, class_id, barcode_value, status.
- classes: id, name, homeroom_teacher_id, academic_year, status.
- attendances: id, student_id, class_id, date, check_in_time, status, input_type, note, created_by.
- attendance_settings: id, school_start_time, late_time_limit, active_days.

Instruksi implementasi:
1. Baca PRD.md dan desain.md sebelum membuat keputusan.
2. Buat struktur aplikasi yang rapi sesuai pola Laravel.
3. Gunakan migration, model, relationship, seeder, policy/middleware/permission, Livewire components, dan Blade views.
4. Buat validasi form yang jelas.
5. Pastikan pembatasan role berjalan benar.
6. Pastikan halaman Absensi Mandiri dapat diakses tanpa login, tetapi hanya untuk mencatat absensi.
7. Pastikan riwayat absensi siswa dan Kartu Barcode Saya hanya dapat diakses setelah siswa login.
8. Pastikan siswa hanya dapat melihat riwayat dan barcode/QR miliknya sendiri.
9. Pastikan absensi tidak bisa duplikat untuk siswa dan tanggal yang sama.
10. Buat seed data contoh untuk role, pengguna, kelas, siswa, barcode/QR, dan pengaturan jam masuk.
11. Gunakan UI yang konsisten dengan desain.md.
12. Setelah implementasi, jalankan migration, seeder, dan test/manual verification.
13. Berikan ringkasan file yang dibuat/diubah dan cara menjalankan sistem.

Output yang saya inginkan:
- Implementasi aplikasi, bukan hanya penjelasan.
- Jika belum bisa membuat semua fitur sekaligus, prioritaskan MVP:
  1. Login dan role.
  2. Data siswa.
  3. Data kelas.
  4. Generate QR/barcode.
  5. Cetak kartu barcode/QR.
  6. Absensi mandiri siswa tanpa login.
  7. Scan absensi internal admin/guru.
  8. Riwayat absensi siswa setelah login.
  9. Kartu Barcode Saya.
  10. Absensi manual.
  11. Dashboard.
  12. Laporan harian/bulanan.
- Jelaskan asumsi teknis yang digunakan.
- Jangan menambahkan fitur di luar PRD.
