# Papua Future Project

Website Papua Future Project (PFP) untuk memperkenalkan program pendidikan dan kesehatan anak di Papua Barat serta memberi pengelola cara memperbarui konten dan menangani pendaftaran relawan/intern.

## Status proyek

UI publik sudah dipindahkan ke **PHP native MVC** dengan PHP **8.4+**. Struktur target memakai **MySQL/MariaDB melalui PDO** dan **shared hosting cPanel**. Fase 1 mencakup konversi route publik dan pemisahan stylesheet/script. Step 3 dan pengelolaan Tentang Kami, Tim Penggerak, Internship, Volunteer, serta batch pada Step 4 sudah tersedia. Tidak dibutuhkan VPS atau proses Node.js yang selalu berjalan.

Pertahankan UI publik yang sudah ada sebagai referensi saat mengikuti tahapan migrasi. Foto dokumentasi ditampilkan di detail program dan berita/kegiatan; proyek tidak memakai halaman Galeri terpisah.

## Fitur yang direncanakan

- Publik: Beranda, Tentang Kami, Program dan detail, Berita & Kegiatan dan detail, Bergabung, Kontak.
- Admin: dashboard bersidebar, pengelolaan Tentang Kami/tim/batch, program dan foto dokumentasi, berita dan foto dokumentasi, FAQ, pendaftar, ekspor PDF/Excel, dan pesan kontak.
- Pendaftaran intern dan relawan dibuka/ditutup per batch. Foto dan CV pendaftar disimpan secara privat.

Ruang lingkup lengkap dan keputusan produk ada di [`docs/PRD.md`](docs/PRD.md).

## Dokumen proyek

Mulai dari [`AGENT.md`](AGENT.md) untuk konteks agent, kemudian ikuti [`docs/STEP.md`](docs/STEP.md) untuk urutan implementasi.

| Dokumen | Kegunaan |
| --- | --- |
| [PRD](docs/PRD.md) | Tujuan, scope, fitur publik/admin, dan kriteria penerimaan. |
| [STEP](docs/STEP.md) | Tahap kerja dari migrasi HTML hingga deployment. |
| [ARCHITECTURE](docs/ARCHITECTURE.md) | Stack, struktur folder, route, alur aplikasi, dan cPanel. |
| [ERD](docs/ERD.md) | Hubungan tabel database. |
| [SCHEMA](docs/SCHEMA.md) | DDL dan urutan migrasi MySQL. |
| [USERFLOW](docs/USERFLOW.md) | Alur pengguna publik dan admin. |
| [DESIGN-ADMIN](docs/DESIGN-ADMIN.md) | Rancangan dashboard dengan sidebar dan halaman admin. |
| [GSAP](docs/GSAP.md) | Panduan animasi publik dan admin. |
| [RULES](docs/RULES.md) | Konvensi kode, MVC, keamanan, aksesibilitas, dan Git. |

## Stack target

- PHP 8.4 atau lebih baru, tanpa framework PHP.
- MVC buatan sendiri, Composer untuk autoload dan pustaka yang diperlukan.
- MySQL atau MariaDB melalui PDO dan prepared statements.
- Apache/cPanel, CSS dan JavaScript biasa; GSAP disajikan dari server sendiri.
- Shared hosting cukup selama paket akun menyediakan versi PHP, ekstensi PHP, basis data, dan konfigurasi upload yang dibutuhkan. Pastikan versi dan fitur yang benar-benar tersedia pada paket hosting sebelum deployment.

## Menjalankan aplikasi

### Setup database lewat phpMyAdmin

1. Buat database kosong di phpMyAdmin dengan nama yang kamu pilih dan collation `utf8mb4_unicode_ci`.
2. Pilih database itu, buka tab **Import**, lalu impor [`database/pfp.sql`](database/pfp.sql). File ini membuat seluruh tabel dari ERD/SCHEMA sekaligus dan menandai skema awal sebagai sudah diterapkan.
3. Isi nama database dan kredensial MySQL yang sama di `.env` (`DB_NAME`, `DB_USER`, `DB_PASS`, `DB_HOST`, `DB_PORT`). Jangan commit `.env`.
4. Dari PowerShell Laragon di root proyek, jalankan:

```powershell
composer install
php bin/migrate.php
```

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

Halaman publik tersedia di `/`, `/tentang`, `/program`, `/berita`, `/bergabung`, dan `/kontak`. Login admin ada di `/admin/login`, dashboard di `/admin`.
## Target deployment

Deployment produksi dirancang memakai Git Version Control cPanel, Composer production, migrasi CLI, HTTPS, dan folder aplikasi/penyimpanan privat di luar document root bila paket hosting mengizinkan. Lihat [panduan deployment](docs/ARCHITECTURE.md#13-deployment-di-cpanel-dengan-git). Pada shared hosting, ketersediaan PHP 8.4, ekstensi, SSH/Terminal, cron, dan batas upload/resource bergantung pada provider serta paket akun. Uji paket yang dipilih; tidak diperlukan akses root.

## Kontribusi dan agent

Ikuti [aturan pengembangan](docs/RULES.md) dan [panduan AI agent](AGENT.md). Perubahan skema harus memperbarui migrasi, ERD, dan dokumentasi terkait secara konsisten. Jangan menambahkan dependency, fitur, atau mengubah scope produk tanpa alasan dan catatan yang jelas.
