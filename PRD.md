# Product Requirements Document (PRD)

## Sistem Absensi Siswa Berbasis Barcode

## 1. Ringkasan Produk

Sistem Absensi Siswa Berbasis Barcode adalah aplikasi website untuk mencatat kehadiran siswa di SD Inpres Tamamaung IV menggunakan barcode/QR code. Sistem ini membantu sekolah mencatat absensi secara lebih cepat, rapi, dan mudah direkap dibandingkan pencatatan manual.

Sistem hanya berfokus pada absensi siswa. Sistem tidak mencakup notifikasi orang tua, WhatsApp gateway, pembayaran, akademik, nilai, atau fitur administrasi sekolah lain di luar kebutuhan absensi.

## 2. Tujuan Produk

- Mempermudah proses pencatatan kehadiran siswa.
- Mengurangi kesalahan pencatatan absensi manual.
- Mempercepat rekap absensi harian, bulanan, dan per kelas.
- Menyediakan laporan absensi yang dapat dicetak atau diekspor.
- Membantu guru, operator, dan kepala sekolah memantau kehadiran siswa.
- Memungkinkan siswa melakukan absensi mandiri tanpa login menggunakan barcode/QR.
- Memungkinkan siswa mencetak ulang kartu barcode/QR miliknya sendiri setelah login.

## 3. Target Pengguna

### 3.1 Admin / Operator Sekolah

Pengguna yang bertugas mengelola data utama dan konfigurasi sistem.

Tanggung jawab:

- Mengelola data siswa.
- Mengelola data kelas.
- Mengelola akun pengguna.
- Membuat dan mencetak barcode/QR siswa.
- Mencetak barcode/QR siswa per siswa atau per kelas.
- Mengatur jam masuk dan batas keterlambatan.
- Mengelola data absensi.
- Melihat dan mencetak laporan seluruh kelas.

### 3.2 Guru / Wali Kelas

Pengguna yang bertugas melakukan absensi dan memantau kehadiran siswa di kelasnya.

Tanggung jawab:

- Melakukan scan barcode siswa.
- Menginput absensi manual jika diperlukan.
- Melihat rekap absensi kelasnya.
- Mencetak laporan absensi kelasnya.

### 3.3 Kepala Sekolah

Pengguna yang bertugas memantau data kehadiran siswa secara keseluruhan.

Tanggung jawab:

- Melihat dashboard kehadiran.
- Melihat laporan semua kelas.
- Mencetak atau mengekspor laporan.
- Tidak mengubah data siswa, kelas, atau absensi.

### 3.4 Siswa

Pengguna yang melakukan absensi mandiri menggunakan barcode/QR miliknya sendiri tanpa login. Siswa hanya perlu login jika ingin melihat riwayat absensi pribadinya.

Tanggung jawab:

- Melakukan absensi mandiri menggunakan barcode/QR tanpa login.
- Login ke sistem hanya jika ingin melihat riwayat absensi pribadi.
- Mencetak kartu barcode/QR miliknya sendiri setelah login.
- Melihat status absensi hari ini miliknya sendiri.
- Melihat riwayat absensi miliknya sendiri.
- Tidak dapat melihat atau mengubah data siswa lain.

## 4. Ruang Lingkup Produk

### 4.1 Termasuk Dalam Sistem

- Login pengguna.
- Role pengguna: Admin/Operator, Guru/Wali Kelas, Kepala Sekolah, Siswa.
- Manajemen data siswa.
- Manajemen data kelas.
- Generate barcode/QR code siswa.
- Cetak kartu barcode/QR siswa.
- Cetak ulang kartu barcode/QR oleh siswa setelah login.
- Scan absensi menggunakan kamera atau barcode scanner.
- Absensi mandiri oleh siswa tanpa login.
- Login siswa untuk melihat riwayat absensi pribadi.
- Absensi manual.
- Pengaturan jam masuk dan batas terlambat.
- Dashboard absensi.
- Rekap absensi harian, bulanan, per kelas, dan per siswa.
- Export laporan ke Excel.
- Cetak/export laporan ke PDF.

### 4.2 Tidak Termasuk Dalam Sistem

- Notifikasi orang tua.
- WhatsApp gateway.
- SMS gateway.
- Pembayaran sekolah.
- Input nilai siswa.
- Jadwal pelajaran.
- E-learning.
- Presensi guru/staf.
- Sistem akademik lengkap.

## 5. Fitur Utama

## 5.1 Autentikasi dan Hak Akses

### Deskripsi

Sistem menyediakan login untuk membatasi akses berdasarkan role pengguna.

### Kebutuhan

- Pengguna dapat login menggunakan email/username dan password.
- Pengguna dapat logout.
- Sistem membatasi menu dan aksi berdasarkan role.
- Setelah login, seluruh area internal menggunakan sidebar/sidenav sebagai navigasi utama, bukan top navigation.
- Menu sidebar/sidenav harus menyesuaikan role pengguna sehingga hanya fitur yang berwenang yang tampil.
- Admin dapat membuat, mengubah, dan menonaktifkan akun pengguna.

### Hak Akses

| Fitur | Admin/Operator | Guru/Wali Kelas | Kepala Sekolah |
| --- | --- | --- | --- |
| Kelola siswa | Ya | Tidak | Tidak |
| Kelola kelas | Ya | Tidak | Tidak |
| Kelola pengguna | Ya | Tidak | Tidak |
| Generate barcode | Ya | Tidak | Tidak |
| Scan absensi | Ya | Ya | Tidak |
| Absensi manual | Ya | Ya, kelas sendiri | Tidak |
| Ubah data absensi | Ya | Terbatas | Tidak |
| Lihat laporan | Semua | Kelas sendiri | Semua |
| Export laporan | Ya | Kelas sendiri | Ya |

| Fitur | Siswa |
| --- | --- |
| Login | Ya, hanya untuk melihat riwayat absensi |
| Absensi mandiri | Ya, tanpa login menggunakan barcode/QR |
| Lihat status absensi hari ini setelah scan | Ya |
| Lihat riwayat absensi | Ya, setelah login dan hanya milik sendiri |
| Cetak barcode/QR sendiri | Ya, setelah login dan hanya milik sendiri |
| Kelola data siswa/kelas/pengguna | Tidak |
| Absensi manual | Tidak |
| Lihat laporan sekolah/kelas | Tidak |
| Export laporan | Tidak |

## 5.2 Manajemen Data Siswa

### Deskripsi

Admin mengelola data siswa yang akan digunakan untuk proses absensi.

### Data Siswa

- Nama siswa.
- NIS/NISN.
- Jenis kelamin.
- Kelas.
- Akun pengguna siswa.
- Status siswa: aktif/nonaktif.
- Kode barcode/QR unik.

### Kebutuhan

- Admin dapat menambah data siswa.
- Admin dapat mengubah data siswa.
- Admin dapat menonaktifkan data siswa.
- Admin dapat mencari siswa berdasarkan nama, NIS/NISN, atau kelas.
- Admin dapat melihat detail siswa.
- Admin dapat menghubungkan data siswa dengan akun pengguna siswa.

## 5.3 Manajemen Data Kelas

### Deskripsi

Admin mengelola data kelas dan wali kelas.

### Data Kelas

- Nama kelas.
- Wali kelas.
- Tahun ajaran.
- Status aktif/nonaktif.

### Kebutuhan

- Admin dapat menambah kelas.
- Admin dapat mengubah kelas.
- Admin dapat menonaktifkan kelas.
- Admin dapat melihat daftar siswa dalam kelas.

## 5.4 Generate dan Cetak Barcode/QR

### Deskripsi

Setiap siswa memiliki barcode/QR unik yang digunakan untuk melakukan absensi.

### Kebutuhan

- Setiap siswa wajib memiliki satu barcode/QR unik.
- Barcode/QR digunakan sebagai identitas absensi siswa.
- Sistem menghasilkan kode unik untuk setiap siswa.
- Kode barcode/QR tidak boleh sama antar siswa.
- Admin dapat mencetak kartu barcode/QR per siswa.
- Admin dapat mencetak kartu barcode/QR per kelas.
- Siswa dapat mencetak ulang kartu barcode/QR miliknya sendiri setelah login.
- Siswa tidak dapat melihat atau mencetak barcode/QR siswa lain.
- Kartu minimal memuat nama siswa, kelas, NIS/NISN, dan barcode/QR.

### Alur Penggunaan Barcode/QR

1. Siswa datang ke sekolah membawa kartu barcode/QR.
2. Siswa menunjukkan barcode/QR ke kamera atau barcode scanner.
3. Sistem membaca barcode/QR.
4. Sistem mencari data siswa berdasarkan barcode/QR.
5. Sistem mencatat absensi siswa pada tanggal berjalan.
6. Sistem menentukan status Hadir atau Terlambat berdasarkan pengaturan jam masuk.

## 5.5 Scan Absensi

### Deskripsi

Guru, admin, atau siswa melakukan absensi dengan memindai barcode/QR. Guru dan admin dapat melakukan scan untuk siswa melalui halaman internal, sedangkan siswa melakukan absensi mandiri melalui halaman scan publik tanpa login.

### Kebutuhan

- Scan dapat dilakukan menggunakan kamera laptop/HP.
- Scan dapat dilakukan menggunakan barcode scanner USB.
- Setelah barcode valid, sistem mencatat absensi siswa secara otomatis.
- Sistem mencatat tanggal, jam masuk, siswa, kelas, dan status.
- Sistem menampilkan informasi siswa setelah berhasil discan.
- Sistem mencegah absensi ganda pada hari yang sama.
- Jika siswa sudah absen, sistem menampilkan data absensi yang sudah tercatat.
- Pada halaman absensi mandiri publik, sistem hanya menampilkan informasi terbatas setelah scan, seperti nama siswa, kelas, waktu scan, dan status absensi.
- Halaman absensi mandiri publik tidak menampilkan riwayat absensi siswa.

### Aturan Status

- Jika siswa scan sebelum atau tepat pada batas terlambat, status menjadi Hadir.
- Jika siswa scan setelah batas terlambat, status menjadi Terlambat.

## 5.6 Absensi Mandiri Siswa

### Deskripsi

Absensi mandiri siswa adalah fitur yang memungkinkan siswa mencatat kehadirannya sendiri tanpa login menggunakan barcode/QR miliknya.

### Kebutuhan

- Siswa dapat membuka halaman Absensi Mandiri tanpa login.
- Siswa dapat melakukan scan barcode/QR miliknya sendiri menggunakan kamera perangkat.
- Siswa dapat menggunakan input barcode scanner USB jika tersedia.
- Sistem memvalidasi barcode/QR siswa.
- Sistem mencatat tanggal, jam masuk, kelas, dan status absensi.
- Sistem menentukan status Hadir atau Terlambat berdasarkan pengaturan jam masuk.
- Sistem mencegah siswa melakukan absensi lebih dari satu kali pada tanggal yang sama.
- Siswa dapat melihat status absensi hari ini setelah berhasil melakukan absensi.
- Siswa harus login jika ingin melihat riwayat absensi miliknya sendiri.
- Siswa tidak dapat menginput Izin, Sakit, atau Alpa secara mandiri.
- Status Izin, Sakit, dan Alpa hanya dapat diinput oleh admin atau guru melalui absensi manual.

## 5.7 Absensi Manual

### Deskripsi

Absensi manual digunakan jika barcode/QR hilang, rusak, atau siswa tidak dapat discan.

### Status Absensi

- Hadir.
- Terlambat.
- Izin.
- Sakit.
- Alpa.

### Kebutuhan

- Admin dapat menginput absensi manual untuk semua siswa.
- Guru dapat menginput absensi manual untuk siswa di kelasnya.
- Siswa tidak dapat menginput absensi manual.
- Pengguna dapat menambahkan catatan absensi.
- Sistem menyimpan siapa pengguna yang melakukan input manual.
- Sistem mencegah duplikasi absensi pada tanggal yang sama.

## 5.8 Pengaturan Jam Masuk

### Deskripsi

Admin mengatur jam masuk sekolah dan batas keterlambatan.

### Kebutuhan

- Admin dapat mengatur jam masuk.
- Admin dapat mengatur batas terlambat.
- Admin dapat mengatur hari aktif sekolah.
- Pengaturan digunakan untuk menentukan status Hadir atau Terlambat saat scan.

## 5.9 Dashboard Absensi

### Deskripsi

Dashboard menampilkan ringkasan kondisi absensi pada hari berjalan.

### Informasi Dashboard

- Total siswa aktif.
- Jumlah siswa hadir hari ini.
- Jumlah siswa terlambat hari ini.
- Jumlah siswa izin hari ini.
- Jumlah siswa sakit hari ini.
- Jumlah siswa alpa hari ini.
- Daftar siswa yang belum absen.
- Rekap absensi per kelas.

## 5.10 Laporan Absensi

### Deskripsi

Sistem menyediakan laporan absensi untuk kebutuhan dokumentasi sekolah.

### Jenis Laporan

- Laporan harian.
- Laporan bulanan.
- Laporan per kelas.
- Laporan per siswa.

### Filter Laporan

- Tanggal.
- Bulan.
- Kelas.
- Siswa.
- Status absensi.

### Kebutuhan

- Pengguna dapat melihat laporan sesuai hak akses.
- Siswa hanya dapat melihat riwayat absensi miliknya sendiri.
- Laporan dapat diekspor ke Excel.
- Laporan dapat dicetak atau diekspor ke PDF.

## 5.11 Riwayat Absensi Siswa

### Deskripsi

Riwayat absensi siswa adalah halaman khusus bagi siswa untuk melihat catatan absensi pribadinya setelah login.

### Kebutuhan

- Siswa dapat login menggunakan akun siswa.
- Siswa hanya dapat melihat riwayat absensi miliknya sendiri.
- Riwayat menampilkan tanggal, jam masuk, status, dan catatan jika ada.
- Siswa dapat memfilter riwayat berdasarkan bulan.
- Siswa dapat membuka dan mencetak kartu barcode/QR miliknya sendiri.
- Siswa tidak dapat mengubah, menghapus, atau mengekspor data absensi.
- Siswa tidak dapat melihat data siswa lain.

## 6. Alur Utama Sistem

## 6.1 Alur Scan Absensi

1. Guru atau admin login ke sistem.
2. Pengguna membuka menu Scan Absensi.
3. Pengguna mengaktifkan kamera atau barcode scanner.
4. Siswa menunjukkan kartu barcode/QR.
5. Sistem membaca kode siswa.
6. Sistem memvalidasi data siswa.
7. Sistem mengecek apakah siswa sudah absen pada hari tersebut.
8. Jika belum absen, sistem menyimpan data absensi.
9. Sistem menentukan status Hadir atau Terlambat berdasarkan jam masuk.
10. Sistem menampilkan pesan berhasil.

## 6.2 Alur Absensi Mandiri Siswa

1. Siswa membuka halaman Absensi Mandiri tanpa login.
2. Siswa mengaktifkan kamera atau menggunakan barcode scanner USB.
3. Siswa memindai barcode/QR miliknya sendiri.
4. Sistem memvalidasi barcode/QR siswa.
5. Sistem mengecek apakah siswa sudah absen pada hari tersebut.
6. Jika barcode/QR valid dan belum absen hari ini, sistem menyimpan absensi.
7. Sistem menentukan status Hadir atau Terlambat.
8. Sistem menampilkan status absensi hari ini secara terbatas.
9. Jika siswa sudah absen, sistem menampilkan data absensi hari ini yang sudah tercatat.
10. Sistem tidak menampilkan riwayat absensi pada halaman absensi mandiri.

## 6.3 Alur Absensi Manual

1. Guru atau admin login ke sistem.
2. Pengguna membuka menu Absensi Manual.
3. Pengguna memilih tanggal dan kelas.
4. Pengguna memilih siswa.
5. Pengguna memilih status absensi.
6. Pengguna mengisi catatan jika diperlukan.
7. Sistem menyimpan data absensi.

## 6.4 Alur Cetak Kartu Barcode

1. Admin login ke sistem.
2. Admin membuka menu Data Siswa atau Barcode Siswa.
3. Admin memilih siswa atau kelas.
4. Sistem menampilkan kartu barcode/QR.
5. Admin mencetak kartu.

## 6.5 Alur Cetak Barcode Mandiri Oleh Siswa

1. Siswa login ke sistem.
2. Siswa membuka menu Kartu Barcode Saya.
3. Sistem menampilkan kartu barcode/QR milik siswa tersebut.
4. Siswa mencetak kartu barcode/QR.
5. Sistem tidak menampilkan barcode/QR siswa lain.

## 6.6 Alur Melihat Riwayat Absensi Siswa

1. Siswa login ke sistem.
2. Siswa membuka menu Riwayat Absensi.
3. Sistem menampilkan riwayat absensi milik siswa tersebut.
4. Siswa dapat memfilter riwayat berdasarkan bulan.
5. Siswa logout setelah selesai.

## 7. Kebutuhan Non-Fungsional

## 7.1 Keamanan

- Setiap pengguna wajib login untuk mengakses area internal sesuai role.
- Halaman Absensi Mandiri dapat diakses tanpa login khusus untuk pencatatan kehadiran.
- Password disimpan dalam bentuk hash.
- Hak akses dibatasi berdasarkan role.
- Data absensi tidak dapat diakses oleh pengguna yang tidak berwenang.
- Siswa hanya dapat mengakses data absensi miliknya sendiri.
- Halaman Absensi Mandiri publik hanya menampilkan data terbatas setelah scan.
- Riwayat absensi siswa hanya dapat diakses setelah siswa login.

## 7.2 Performa

- Proses scan dan pencatatan absensi harus berjalan cepat.
- Pencarian data siswa dan laporan harus responsif.
- Sistem mampu menangani data seluruh siswa SD Inpres Tamamaung IV.

## 7.3 Kemudahan Penggunaan

- Tampilan sederhana dan mudah dipahami.
- Menu utama jelas dan ditempatkan pada sidebar/sidenav untuk semua halaman internal setelah login.
- Halaman internal tidak menggunakan top nav sebagai navigasi utama.
- Halaman Absensi Mandiri publik untuk siswa menjadi pengecualian: halaman ini tidak memakai sidebar/sidenav internal dan hanya menampilkan header/action minimal serta area scan.
- Proses scan absensi dapat dilakukan dengan sedikit langkah.
- Halaman absensi mandiri siswa harus sederhana dan mudah digunakan.
- Laporan mudah difilter dan dicetak.

## 7.4 Kompatibilitas

- Sistem dapat diakses melalui browser desktop.
- Sistem dapat diakses melalui browser mobile.
- Kamera browser dapat digunakan untuk scan barcode/QR.
- Barcode scanner USB dapat digunakan sebagai input alternatif.

## 8. Rekomendasi Tech Stack

## 8.1 Backend

- Laravel.

## 8.2 Frontend

- Blade.
- Livewire.
- Tailwind CSS.

## 8.3 Database

- MySQL atau MariaDB.

## 8.4 Library dan Package

- html5-qrcode untuk scan menggunakan kamera.
- Laravel Excel untuk export laporan Excel.
- DomPDF untuk export/cetak PDF.
- Spatie Laravel Permission untuk role dan hak akses.
- Library QR/barcode Laravel untuk generate kode siswa.

## 9. Struktur Modul

Seluruh modul internal setelah login ditampilkan dalam layout dashboard dengan sidebar/sidenav role-based, bukan top nav. Halaman Absensi Mandiri Siswa tanpa login berada di layout publik terpisah yang minimal dan fokus pada area scan.

- Dashboard.
- Data Siswa.
- Data Kelas.
- Data Pengguna.
- Barcode/QR Siswa.
- Scan Absensi.
- Absensi Mandiri Siswa tanpa login.
- Riwayat Absensi Siswa.
- Kartu Barcode Saya.
- Absensi Manual.
- Rekap Absensi.
- Laporan Absensi.
- Pengaturan Jam Masuk.

## 10. Data Model Awal

## 10.1 Users

- id.
- name.
- username/email.
- password.
- role.
- status.

## 10.2 Students

- id.
- user_id.
- nis.
- nisn.
- name.
- gender.
- class_id.
- barcode_value.
- status.

## 10.3 Classes

- id.
- name.
- homeroom_teacher_id.
- academic_year.
- status.

## 10.4 Attendances

- id.
- student_id.
- class_id.
- date.
- check_in_time.
- status.
- input_type: scan/manual.
- note.
- created_by.

## 10.5 Attendance Settings

- id.
- school_start_time.
- late_time_limit.
- active_days.

## 11. Status Absensi

| Status | Keterangan |
| --- | --- |
| Hadir | Siswa hadir tepat waktu |
| Terlambat | Siswa hadir melewati batas terlambat |
| Izin | Siswa tidak hadir dengan izin |
| Sakit | Siswa tidak hadir karena sakit |
| Alpa | Siswa tidak hadir tanpa keterangan |

## 12. MVP

Versi awal sistem minimal memiliki fitur:

- Login pengguna.
- Role Admin, Guru, Kepala Sekolah, dan Siswa.
- Data siswa.
- Data kelas.
- Generate barcode/QR siswa.
- Cetak kartu barcode/QR siswa.
- Cetak ulang barcode/QR oleh siswa setelah login.
- Scan absensi.
- Absensi mandiri siswa tanpa login.
- Login siswa untuk melihat riwayat absensi.
- Absensi manual.
- Dashboard absensi hari ini.
- Laporan harian.
- Laporan bulanan.
- Export Excel.
- Cetak/export PDF.

## 13. Kriteria Keberhasilan

- Guru dapat melakukan absensi siswa dengan scan barcode/QR.
- Siswa dapat melakukan absensi mandiri tanpa login menggunakan barcode/QR.
- Siswa dapat login untuk melihat riwayat absensi miliknya sendiri.
- Siswa dapat login untuk mencetak barcode/QR miliknya sendiri.
- Sistem dapat mencatat status Hadir dan Terlambat secara otomatis.
- Admin dapat mengelola data siswa, kelas, dan pengguna.
- Kepala sekolah dapat melihat laporan seluruh kelas.
- Laporan absensi dapat difilter dan dicetak.
- Sistem tidak mencatat absensi ganda untuk siswa yang sama pada tanggal yang sama.
- Halaman absensi mandiri tidak menampilkan riwayat absensi atau data siswa lain.
- Sistem memastikan barcode/QR setiap siswa unik.

## 14. Batasan

- Sistem tidak mengirim notifikasi kepada orang tua.
- Sistem tidak menggunakan WhatsApp gateway.
- Sistem hanya digunakan untuk absensi siswa.
- Sistem membutuhkan perangkat dengan browser modern.
- Scan menggunakan kamera membutuhkan izin akses kamera dari browser.
- Siswa tidak perlu login untuk melakukan absensi.
- Siswa hanya perlu login untuk melihat riwayat absensi miliknya sendiri.
- Siswa perlu login jika ingin mencetak ulang barcode/QR miliknya sendiri.
