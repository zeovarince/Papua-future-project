# Papua Future Project

Website Papua Future Project (PFP) untuk memperkenalkan program pendidikan dan kesehatan anak di Papua Barat serta memberi pengelola cara memperbarui konten dan menangani pendaftaran relawan/intern.

## Status proyek

UI publik sudah dipindahkan ke **PHP native MVC** dengan PHP **8.4+**. Struktur target memakai **MySQL/MariaDB melalui PDO** dan **shared hosting cPanel**. Fase 1 mencakup konversi route publik dan pemisahan stylesheet/script. Panel admin dan backend akan dibangun pada fase berikutnya. Tidak dibutuhkan VPS atau proses Node.js yang selalu berjalan.

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

Jalankan server pengembangan PHP dari root repositori:

```sh
php -S localhost:8000 -t public public/index.php
```

Halaman publik tersedia di `/`, `/tentang`, `/program`, `/berita`, `/bergabung`, dan `/kontak`. Detail program menggunakan `/program/{slug}` untuk lima program yang ada. Database, autentikasi admin, dan skrip migrasi belum dibuat. Composer autoload disiapkan, sementara autoloader lokal juga memungkinkan fase UI dijalankan sebelum dependency dipasang. Tailwind Play CDN dan Google Fonts masih dipakai dari sumber eksternal untuk mempertahankan UI lama; rencanakan pemindahan CSS/font secara lokal sebelum menerapkan CSP ketat.

## Target deployment

Deployment produksi dirancang memakai Git Version Control cPanel, Composer production, migrasi CLI, HTTPS, dan folder aplikasi/penyimpanan privat di luar document root bila paket hosting mengizinkan. Lihat [panduan deployment](docs/ARCHITECTURE.md#13-deployment-di-cpanel-dengan-git). Pada shared hosting, ketersediaan PHP 8.4, ekstensi, SSH/Terminal, cron, dan batas upload/resource bergantung pada provider serta paket akun. Uji paket yang dipilih; tidak diperlukan akses root.

## Kontribusi dan agent

Ikuti [aturan pengembangan](docs/RULES.md) dan [panduan AI agent](AGENT.md). Perubahan skema harus memperbarui migrasi, ERD, dan dokumentasi terkait secara konsisten. Jangan menambahkan dependency, fitur, atau mengubah scope produk tanpa alasan dan catatan yang jelas.
