# Papua Future Project

Website Papua Future Project (PFP) untuk memperkenalkan program pendidikan dan kesehatan anak di Papua Barat serta memberi pengelola cara memperbarui konten dan mengatur tautan pendaftaran.

## Status proyek

UI publik sudah dipindahkan ke **PHP native MVC** dengan PHP **8.4+**. Struktur memakai **MySQL/MariaDB melalui PDO** dan shared hosting **DirectAdmin** (cPanel juga bisa dipakai). Admin mengelola konten, tim, batch, tautan formulir eksternal per batch, dan pesan kontak. Tidak dibutuhkan VPS atau proses Node.js yang selalu berjalan.

Pertahankan UI publik yang sudah ada sebagai referensi saat mengikuti tahapan migrasi. Foto dokumentasi ditampilkan di detail program dan berita/kegiatan; proyek tidak memakai halaman Galeri terpisah.

## Fitur yang direncanakan

- Publik: Beranda, Tentang Kami, Program dan detail, Berita & Kegiatan dan detail, Bergabung, Kontak.
- Admin: dashboard bersidebar, pengelolaan Tentang Kami/tim/batch, program dan foto dokumentasi, berita dan foto dokumentasi, FAQ, serta pesan kontak.
- Setiap batch punya tautan formulir eksternal dan sakelar buka/tutup sendiri. Tombol pendaftaran publik tampil jika link HTTP/HTTPS tersedia dan batch dibuka. Jawaban dikelola di layanan formulir yang ditautkan.
- Ringkasan adalah halaman awal admin untuk melihat pesan belum dibaca, berita terbit, dan pesan terbaru. Pengaturan pendaftaran hanya ada di halaman Batch.

Ruang lingkup lengkap dan keputusan produk ada di [`docs/PRD.md`](docs/PRD.md).

## Dokumen proyek

Mulai dari [`AGENT.md`](AGENT.md) untuk konteks agent, kemudian ikuti [`docs/STEP.md`](docs/STEP.md) untuk urutan implementasi.

| Dokumen | Kegunaan |
| --- | --- |
| [PRD](docs/PRD.md) | Tujuan, scope, fitur publik/admin, dan kriteria penerimaan. |
| [STEP](docs/STEP.md) | Tahap kerja dari migrasi HTML hingga deployment. |
| [ARCHITECTURE](docs/ARCHITECTURE.md) | Stack, struktur folder, route, alur aplikasi, dan deployment. |
| [Deploy DirectAdmin](docs/DEPLOY-DIRECTADMIN.md) | Panduan deployment untuk akun hosting saat ini, tanpa SSH. |
| [ERD](docs/ERD.md) | Hubungan tabel database. |
| [SCHEMA](docs/SCHEMA.md) | DDL dan urutan migrasi MySQL. |
| [USERFLOW](docs/USERFLOW.md) | Alur pengguna publik dan admin. |
| [DESIGN-ADMIN](docs/DESIGN-ADMIN.md) | Rancangan dashboard dengan sidebar dan halaman admin. |
| [RWD](docs/RWD.md) | Breakpoint serta perilaku responsif halaman publik dan panel admin. |
| [GSAP](docs/GSAP.md) | Panduan animasi publik dan admin. |
| [RULES](docs/RULES.md) | Konvensi kode, MVC, keamanan, aksesibilitas, dan Git. |
| [SECURITY](docs/SECURITY.md) | Kontrol keamanan aplikasi, audit integritas, dan persiapan hosting. |

## Stack target

- PHP 8.4 atau lebih baru, tanpa framework PHP.
- MVC buatan sendiri, Composer untuk autoload dan pustaka yang diperlukan.
- MySQL atau MariaDB melalui PDO dan prepared statements.
- DirectAdmin shared hosting (Apache/LiteSpeed dengan rewrite), CSS dan JavaScript biasa; GSAP disajikan dari server sendiri.
- Shared hosting cukup selama paket akun menyediakan versi PHP, ekstensi PHP, basis data, dan konfigurasi upload yang dibutuhkan. Pastikan versi dan fitur yang benar-benar tersedia pada paket hosting sebelum deployment.

## Menjalankan aplikasi

### Setup database lewat phpMyAdmin

1. Buat database kosong di phpMyAdmin dengan nama yang kamu pilih dan collation `utf8mb4_unicode_ci`.
2. Pilih database itu, buka tab **Import**, lalu impor [`database/pfp.sql`](database/pfp.sql). Ini satu-satunya file SQL yang perlu kamu impor: isinya tabel, indeks, konten awal, dan pembaruan aman untuk skema PFP versi sebelumnya.
3. Konfigurasi aplikasi hanya disimpan di `.env` pada akar proyek. File ini tersedia di lingkungan kerja lokal dan sengaja tidak masuk Git. Pada instalasi baru, buat `.env` lalu isi variabel yang tercantum di bagian [Variabel lingkungan](docs/ARCHITECTURE.md#12-variabel-lingkungan). Isi kredensial MySQL (`DB_NAME`, `DB_USER`, `DB_PASS`, `DB_HOST`, `DB_PORT`), buat `APP_KEY` dengan `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`, dan untuk production isi `ADMIN_PATH` dengan nilai acak, misalnya hasil `php -r "echo '/panel-' . bin2hex(random_bytes(8)), PHP_EOL;"`. Jangan commit `.env`.
4. `pfp.sql` bisa dipakai untuk database baru atau memperbarui tabel PFP versi sebelumnya. Impor ulang mempertahankan konten dan arsip pendaftar lama. Tetap gunakan database khusus PFP dan jangan impor dump WordPress ke database itu.
5. Dari PowerShell Laragon di root proyek, jalankan:

```powershell
composer install
php bin/migrate.php
```

Untuk foto dokumentasi, pastikan ekstensi `fileinfo` dan GD tersedia. Arsip aplikasi dari formulir website versi lama dapat tetap berada di database, tetapi tidak digunakan oleh fitur aktif. Formulir dan respons baru dikelola di layanan eksternal pada tautan yang diatur Admin.

Migrasi awal akan dilewati karena tabel dan catatan migrasinya sudah ada dari file SQL. Migrasi baru di masa depan tetap bisa berjalan lewat CLI.

### Jalankan aplikasi

```powershell
php -S localhost:8000 -t public public/index.php
```

Untuk membuat akun admin, siapkan password tanpa menulisnya ke perintah atau Git:

```powershell
$securePassword = Read-Host "Password admin" -AsSecureString
$passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
try {
    $env:PFP_ADMIN_PASSWORD = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
    php bin/create-admin.php admin@example.com "Admin PFP"
} finally {
    Remove-Item Env:PFP_ADMIN_PASSWORD -ErrorAction SilentlyContinue
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($passwordPointer)
}
```

Halaman publik tersedia di `/`, `/tentang`, `/program`, `/berita`, `/bergabung`, dan `/kontak`. Di lokal, login admin ada di `/admin/login` dan dashboard di `/admin`. Di production, isi `ADMIN_PATH` dengan jalur rahasia acak; URL admin akan mengikuti nilai itu.

### Audit integritas

Sebelum membuat acuan pertama, jalankan `php bin/integrity-check.php --scan` dan tinjau hasilnya. Setelah memastikan deployment bersih, jalankan `php bin/integrity-check.php --init`. Pemeriksaan rutin memakai `php bin/integrity-check.php`; cron hanya dapat dipasang bila provider DirectAdmin menyediakan Cron Jobs dan akses PHP CLI. Panduan keamanan ada di [`docs/SECURITY.md`](docs/SECURITY.md#53-deteksi-dini).
## Target deployment

Target deployment saat ini adalah DirectAdmin: Composer dijalankan lokal, file diunggah lewat File Manager/FTP, database diimpor lewat phpMyAdmin, dan source code serta `.env` diletakkan di luar `public_html`. Ikuti [panduan deploy DirectAdmin](docs/DEPLOY-DIRECTADMIN.md). Akun yang ditunjukkan belum memiliki SSH; PHP 8.4 sudah dipilih, tetapi ekstensi, SSL, rewrite URL, cron, dan batas resource tetap perlu diverifikasi. Panduan cPanel Git di [ARCHITECTURE](docs/ARCHITECTURE.md#13-deployment-di-cpanel-dengan-git) adalah alternatif.

## Kontribusi dan agent

Ikuti [aturan pengembangan](docs/RULES.md) dan [panduan AI agent](AGENT.md). Perubahan skema harus memperbarui migrasi, ERD, dan dokumentasi terkait secara konsisten. Jangan menambahkan dependency, fitur, atau mengubah scope produk tanpa alasan dan catatan yang jelas.
