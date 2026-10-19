# RULES: Aturan Pengembangan

Aturan wajib untuk semua kode di proyek Papua Future Project. Berlaku untuk manusia maupun AI agent. Bila bertentangan dengan [PRD.md](PRD.md) atau [ARCHITECTURE.md](ARCHITECTURE.md), ketiga dokumen dibahas dan disamakan. Aturan ini tidak bisa dilanggar diam-diam.

## 1. Bahasa dan gaya kode

| Aturan | Detail |
| --- | --- |
| PHP | PSR-12 (`phpcs.xml`). `declare(strict_types=1)` di setiap file PHP. |
| Tipe | Tipe parameter, return type, dan property type ditulis eksplisit. `mixed` hanya bila benar-benar perlu. |
| Nama kelas | PascalCase. Satu kelas per file. Nama file sama dengan nama kelas. |
| Nama metode dan variabel | camelCase. Nama deskriptif, bukan singkatan: `$programId` bukan `$pid`. |
| Nama tabel dan kolom | snake_case. Tabel jamak (`programs`, `posts`). Kunci asing: `{tabel_tunggal}_id` (contoh: `batch_id`). |
| CSS | Kelas dengan kebab-case: `.team-card`, `.admin-sidebar`. Tidak ada ID untuk styling. Variabel CSS di `tokens.css`. |
| JavaScript | ES6+. `const` lebih diutamakan dari `let`. Tidak ada `var`. Semicolon wajib. |
| Komentar | Bahasa Indonesia untuk komentar biasa. Bahasa Inggris untuk nama fungsi dan variabel. Komentar menjelaskan "mengapa", bukan "apa". |
| Indentasi | 4 spasi untuk PHP, 2 spasi untuk CSS dan JS. |

## 2. Struktur MVC

### 2.1 Controller

- **Tipis.** Controller hanya menerima request, memanggil Validator, Model, dan/atau Service, lalu memilih View atau redirect.
- Tidak ada query SQL di Controller.
- Tidak ada logika bisnis di Controller.
- Tidak ada `echo` atau `print` di Controller. Gunakan `View::render()`.
- Tidak membaca `$_GET`, `$_POST`, `$_FILES`, `$_SERVER`, atau `$_COOKIE` langsung. Gunakan kelas `Request`.

### 2.2 Model

- Satu kelas per tabel.
- Semua query ada di Model. Prepared statement tanpa kecuali.
- Tidak membaca superglobal PHP.
- Tidak mengeluarkan HTML.
- Return type jelas: `?object` untuk satu baris, `array` untuk banyak baris, `int` untuk insert ID, `bool` untuk update/delete.

### 2.3 View

- Hanya menampilkan data. Boleh memakai `if`, `foreach`, `for`, dan helper.
- Tidak ada query database.
- Tidak ada aturan bisnis (contoh: jangan hitung usia di View).
- Semua output dinamis melewati `e()` (htmlspecialchars).
- Memakai layout dan partial. Tidak ada HTML boilerplate yang diulang.

### 2.4 Service

- Logika yang menyentuh lebih dari satu Model, atau menyentuh file system: upload, gambar, galeri, pendaftaran, ekspor, autentikasi.
- Tidak membaca superglobal PHP langsung.

### 2.5 Validator

- Satu kelas per form/entitas.
- Memeriksa dan membersihkan masukan.
- Mengembalikan array galat per field: `['email' => 'Email sudah terdaftar di batch ini']`.
- Boleh mengakses database untuk aturan unik (email per batch, slug unik).
- Tidak mengubah data.

## 3. Keamanan

Dokumen ini menetapkan aturan dasar. Untuk konfigurasi hosting, pemantauan integritas, pencatatan keamanan, dan respons insiden, ikuti [SECURITY.md](SECURITY.md).

### 3.1 SQL Injection

- **Semua query memakai prepared statement PDO** dengan parameter binding (`:param` atau `?`).
- `PDO::ATTR_EMULATE_PREPARES` diset `false`.
- Nama kolom untuk `ORDER BY` diambil dari daftar putih, bukan dari input pengguna.
- Tidak ada string concatenation untuk membangun query.

### 3.2 XSS (Cross-Site Scripting)

- Semua output dinamis di View melewati `e()` (`htmlspecialchars(ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`).
- Konten berita dan program disimpan sebagai teks biasa, bukan HTML. `Text::render()` yang meng-escape dulu, lalu mengubah paragraf dan `**tebal**`.
- Tidak ada `{!! !!}` atau `echo` tanpa escaping.
- Content Security Policy belum diaktifkan karena masih ada style/script inline dan dependensi CDN. Jangan aktifkan sebelum aset dipindah dan kebijakan diuji; lihat [SECURITY.md](SECURITY.md).

### 3.3 CSRF

- Token acak per sesi, disimpan di `$_SESSION['csrf_token']`.
- Setiap form POST menyertakan `<?= csrf_field() ?>` (hidden input).
- `CsrfMiddleware` memeriksa token dengan `hash_equals()`.
- Cookie sesi `SameSite=Lax`.

### 3.4 Autentikasi

- Password di-hash dengan `password_hash(PASSWORD_BCRYPT)`.
- Login diperiksa dengan `password_verify()`.
- `password_needs_rehash()` dipanggil setelah login berhasil.
- Session di-regenerate setelah login: `session_regenerate_id(true)`.
- Session dihapus sepenuhnya saat logout.
- Timeout sesi: 2 jam tidak aktif.
- Cookie sesi: `HttpOnly`, `Secure` (di production), `SameSite=Lax`.
- Pesan login gagal tidak menyebutkan apakah email atau password yang salah.
- Rate limit login: 5 percobaan gagal per 15 menit per kombinasi IP+email.
- Identifier pembatas laju dibuat dengan HMAC-SHA256 dan `APP_KEY`; jangan menyimpan IP mentah sebagai identifier.
- Di production, akses admin memakai jalur acak `ADMIN_PATH`. Jalur default `/admin` menjawab 404; semua route admin tetap wajib memakai middleware autentikasi.
- Catat login, ekspor, perubahan status, dan penghapusan ke `storage/logs/security.log` tanpa kata sandi, email, isi form, token, atau data pribadi.

### 3.5 Upload File

- Periksa `UPLOAD_ERR_OK` lebih dulu.
- Ukuran: foto 2 MB (pendaftar), 8 MB (galeri), CV 5 MB. Foto pendaftar maksimal 16 megapiksel dan 8.000 piksel pada tiap sisi agar pemrosesan GD sesuai batas memori shared hosting.
- Tipe diperiksa dari isi file (`finfo_file`), bukan dari ekstensi atau Content-Type browser.
- Foto: `getimagesize()` harus berhasil. CV: awalan `%PDF-`.
- Nama file dibuat sistem. Berkas pendaftar memakai token direktori acak dari `random_bytes(16)` dan nama file tetap di dalam direktori tersebut.
- Gambar disimpan ulang lewat GD (menghapus EXIF, termasuk GPS).
- File pendaftar disimpan di `STORAGE_DIR/applications/` (di luar `public/`) dan akses langsung folder ditolak oleh `.htaccess`.
- `public/uploads/.htaccess` memblokir eksekusi PHP.

### 3.6 Rate Limiting

- Form kontak: 5 per jam per IP.
- Form pendaftaran: 5 per jam per IP.
- Login: 5 gagal per 15 menit per IP+email.
- Disimpan di tabel `rate_limits`, dibersihkan oleh cron harian.

### 3.7 Header Keamanan

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

Panel admin juga mengirim `X-Robots-Tag: noindex, nofollow` dan `Cache-Control: no-store`. HSTS hanya dikirim di production ketika koneksi HTTPS terdeteksi. CSP menunggu pembersihan inline style/script dan dependensi eksternal.

## 4. Database

- Charset: `utf8mb4`, collation: `utf8mb4_unicode_ci`.
- Timezone sesi MySQL diset `Asia/Jayapura` (WIT) saat koneksi dibuka.
- Engine: InnoDB untuk semua tabel (mendukung transaksi dan foreign key).
- Foreign key dipakai di mana ada relasi (lihat ERD.md).
- `ON DELETE CASCADE` untuk foto (ikut terhapus). `ON DELETE RESTRICT` untuk batch (ditolak jika ada data).
- Transaksi dipakai untuk operasi yang melibatkan banyak tabel (contoh: insert pendaftar + file).

## 5. Naming convention route

| Pola | Contoh |
| --- | --- |
| Publik | `/program`, `/program/{slug}`, `/berita/{slug}` |
| Admin daftar | `/admin/program`, `/admin/berita` |
| Admin tambah | `/admin/program/tambah` |
| Admin simpan | `POST /admin/program` |
| Admin ubah | `/admin/program/{id}/ubah` |
| Admin update | `POST /admin/program/{id}` |
| Admin hapus | `POST /admin/program/{id}/hapus` |
| Admin toggle | `POST /admin/batch/{id}/toggle` |

Semua URL lowercase, tanpa trailing slash, tanpa `.php` atau `.html`.

## 6. Git

### 6.1 Branch

- `main` — kode yang siap deploy.
- `dev` — pengembangan aktif.
- `feature/{nama}` — fitur spesifik (contoh: `feature/crud-program`).
- `fix/{nama}` — perbaikan bug.

### 6.2 Commit message

Format: `tipe: deskripsi singkat`

Tipe:
- `feat` — fitur baru
- `fix` — perbaikan bug
- `refactor` — perubahan kode tanpa mengubah perilaku
- `style` — perubahan CSS/formatting
- `docs` — dokumentasi
- `chore` — maintenance (dependensi, konfigurasi)

Contoh:
```
feat: tambah CRUD program dengan upload foto
fix: validasi email pendaftar case-insensitive
refactor: pindahkan logika upload ke UploadService
docs: tambah GSAP.md
```

### 6.3 .gitignore

```
.env
vendor/
storage/logs/*
storage/applications/*
storage/cache/*
public/uploads/programs/*
public/uploads/posts/*
public/uploads/team/*
!public/uploads/.htaccess
!public/uploads/**/.gitkeep
node_modules/
.idea/
.vscode/
*.log
```

## 7. Aksesibilitas

- Semua gambar punya atribut `alt` yang deskriptif.
- Form: setiap input punya `<label>` yang terkait (atribut `for`).
- Fokus keyboard terlihat (outline atau ring). Tidak pernah `outline: none` tanpa pengganti.
- Kontras warna minimal 4.5:1 untuk teks biasa.
- Tombol dan link yang hanya berisi ikon punya `aria-label`.
- Animasi menghormati `prefers-reduced-motion: reduce`.
- Halaman tetap berfungsi tanpa JavaScript (konten terlihat, form bisa dikirim).
- Urutan heading semantik: satu `<h1>` per halaman, lalu `<h2>`, `<h3>`, dst.

## 8. SEO

- Setiap halaman punya `<title>` unik: `{Judul Halaman} — Papua Future Project`.
- Meta description unik per halaman.
- Satu `<h1>` per halaman.
- Semantic HTML: `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`.
- Open Graph meta tags untuk berita dan program.
- `robots.txt` melarang `/admin/` dan `/storage/`.
- `sitemap.xml` sederhana untuk halaman publik.
- URL bersih: `/program/bimbel-literasi`, bukan `/program.php?id=1`.

## 9. Format tanggal dan zona waktu

- Zona waktu: `Asia/Jayapura` (WIT, UTC+9).
- PHP: `date_default_timezone_set('Asia/Jayapura')`.
- MySQL: `SET time_zone = '+09:00'` saat koneksi.
- Format tampilan: `15 Mei 2025` (bahasa Indonesia). Gunakan `DateId::format()`.
- Format input (date picker): `YYYY-MM-DD`.

## 10. Error handling

- `APP_ENV=local`: tampilkan error detail.
- `APP_ENV=production`: tampilkan halaman error ramah (404 atau 500). Log error ke `storage/logs/app.log`.
- Gunakan `set_exception_handler` dan `set_error_handler` di `App::boot()`.
- Tidak ada `@` (error suppression operator).
- `try-catch` hanya di tempat yang memang perlu menangani exception spesifik, bukan membungkus semua kode.

## 11. File dan folder

- Folder baru yang perlu writable: `storage/logs/`, `storage/applications/`, `storage/cache/`, `public/uploads/programs/`, `public/uploads/posts/`, `public/uploads/team/`.
- Permission: folder 0755, file 0644, folder upload privat (`storage/applications/`) 0750.
- Folder upload punya `.gitkeep` agar masuk Git, tapi isinya di-ignore.

## 12. Dependency management

- Gunakan Composer untuk autoloading dan library.
- `composer.json` berisi autoload PSR-4: `App\\` → `app/`.
- Hanya dua library eksternal: `phpoffice/phpspreadsheet` dan `dompdf/dompdf`.
- Tidak menambah library baru tanpa alasan tertulis dan persetujuan.
- `composer install --no-dev` di production.

## 13. Testing

- Validasi: unit test untuk setiap Validator (input valid, input invalid, edge case).
- Model: unit test untuk query kritis (insert, unique constraint, cascade delete).
- Alur E2E: test pendaftaran (termasuk upload), login admin, CRUD minimal.
- Jalankan: `./vendor/bin/phpunit`.
