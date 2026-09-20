# Sistem Absensi Siswa Berbasis Barcode — SD Inpres Tamamaung IV

Aplikasi web untuk mencatat kehadiran siswa SD Inpres Tamamaung IV menggunakan **barcode/QR code**, dibangun dengan **Laravel 13 (PHP 8.3)**.

Fitur utama:

- Scan barcode/QR (mandiri publik & scan internal) untuk absensi otomatis.
- Absensi manual oleh guru/operator.
- Manajemen data siswa, kelas, dan akun pengguna (CRUD).
- Cetak kartu barcode/QR siswa (per kelas atau perorangan).
- Cetak ulang kartu oleh siswa setelah login.
- Rekap absensi harian, bulanan, per kelas, dan per siswa.
- Laporan lengkap (ringkasan, rekap, detail) dengan ekspor **Excel** dan **PDF**.
- Dashboard rekap per kelas & daftar siswa yang belum absen.

---

## Persyaratan

- PHP **^8.3** (wajib ekstensi: `pdo_mysql`/`pdo_sqlite`, `mbstring`, `gd`, `openssl`, `xml`, `curl`, `zip`)
- [Composer](https://getcomposer.org)
- MySQL (rekomendasi) atau SQLite
- Chrome / Microsoft Edge (opsional, untuk ekspor PDF yang cepat — otomatis terdeteksi; jika tidak ada, fallback ke dompdf)

> NPM/Node **tidak wajib** — tampilan aplikasi memakai CSS inline tanpa Vite.

---

## Instalasi

### Auto installer Windows/Laragon

1. Pastikan Apache dan MySQL Laragon sudah dijalankan.
2. Klik dua kali **`install.bat`** dari folder project.
3. Ikuti pertanyaan konfigurasi database dan pilihan data demo.
4. Setelah selesai, klik **Reload** pada Laragon lalu buka project dari menu Laragon.

Installer akan memeriksa PHP 8.3+, ekstensi PHP, dan Composer; memasang dependency; membuat `.env` jika belum ada; membuat database tanpa menghapus database lama; menjalankan migration; serta menyiapkan cache Laravel. File `.env` yang sudah ada dipertahankan secara default dan data demo hanya dimasukkan setelah mendapat persetujuan.

Untuk hanya memeriksa kesiapan komputer tanpa mengubah file atau database, jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File .\install.ps1 -CheckOnly
```

### Instalasi manual

1. **Clone / salin project** ke perangkat:

   ```bash
   git clone <url-repo> absensi-tamamaung
   cd absensi-tamamaung
   ```

   Jika tidak menggunakan git, cukup salin folder project ini.

2. **Install dependency PHP:**

   ```bash
   composer install
   ```

3. **Buat file `.env` dari contoh:**

   ```bash
   copy .env.example .env        # Windows (CMD)
   # atau
   cp .env.example .env          # Linux / macOS / Git Bash
   ```

4. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi database** — buka `.env`, sesuaikan bagian berikut:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi_tamamaung
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Buat database `absensi_tamamaung` di MySQL (mis. via phpMyAdmin atau:

   ```bash
   mysql -u root -p -e "CREATE DATABASE absensi_tamamaung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```

   ). Untuk SQLite, ganti `DB_CONNECTION=sqlite` dan kosongkan baris `DB_*` lainnya.

6. **Jalankan migrasi + seeder (data dummy):**

   ```bash
   php artisan migrate --seed
   ```

   Seeder membuat: role, kelas **4A & 4B** (masing-masing 30 siswa = 60 siswa), akun pengguna demo, dan ±2400 data absensi 40 hari terakhir.

7. **Jalankan server (opsional casing Windows/Linux):**

   ```bash
   php artisan serve
   ```

   Lalu buka **http://127.0.0.1:8000**.

   > Jika memakai URL berbeda saat deploy (subfolder/domain), set `APP_URL` di `.env`. Supaya cache ikut baru, jalankan
   > `php artisan config:cache` / `php artisan optimize:clear` setelah mengubah `.env`.

---

## Akun Demo

Semua kata sandi default: `password`

| Peran          | Username  | Halaman yang bisa diakses                                  |
|----------------|-----------|------------------------------------------------------------|
| Admin          | `admin`   | Semua menu (siswa, kelas, pengguna, kartu, laporan, dsb.)  |
| Guru/Wali Kelas| `guru`    | Wali kelas 4A (kelas walinya saja)                         |
| Guru/Wali Kelas| `guru.4b` | Wali kelas 4B                                              |
| Kepala Sekolah | `kepsek`  | Dashboard & laporan                                        |
| Siswa contoh   | `andi.pratama` | Riwayat absensi & kartu sendiri                        |

> Daftar akun siswa bisa dilihat dari halaman **Data Siswa** (login sebagai admin).

---

## Ekspor PDF (opsional — Chrome/Edge headless)

Aplikasi otomatis memakai Chrome/Edge yang terinstall untuk render PDF agar cepat dan tidak error pada data banyak. Jika Chrome/Edge ditempatkan di lokasi non-standar, tentukan path-nya di `.env`:

```env
CHROME_PATH=C:\Program Files\Google\Chrome\Application\chrome.exe
```

Bila tidak tersedia, aplikasi otomatis jatuh ke dompdf (lebih lambat untuk data sangat banyak).

---

## Troubleshooting

- **`No application encryption key`** → jalankan `php artisan key:generate`.
- **Koneksi database ditolak** → pastikan MySQL aktif dan kredensial di `.env` benar.
- **Error permission `storage/` atau `bootstrap/cache/`** (Linux) →
  `chmod -R 775 storage bootstrap/cache`.
- **Laporan/PDF kosong atau gagal** → cek `APP_DEBUG=true` untuk melihat pesan error saat pengembangan.
- **Ingin reset total data** → `php artisan migrate:fresh --seed`.

---

## Lisensi

Project akademik/magang (UHM) — kode sumber terbuka untuk keperluan tugas akhir.
