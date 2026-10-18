# Panduan AI Agent — Papua Future Project

Dokumen ini menjadi pintu masuk bagi AI agent yang membantu mengembangkan website Papua Future Project (PFP). Ikuti dokumen proyek sebelum mengubah kode. Jangan menganggap file HTML lama sebagai arsitektur final: file itu adalah UI publik yang perlu dipertahankan tampilannya saat dipindahkan ke PHP native MVC.

## Tujuan proyek

Membangun ulang situs publik PFP dan panel admin agar konten dapat dikelola melalui browser. Stack yang disepakati adalah **PHP 8.4 atau lebih baru, PHP native dengan pola MVC, MySQL/MariaDB melalui PDO, CSS dan JavaScript biasa, dan shared hosting cPanel**. Proyek tidak memakai Laravel, CodeIgniter, VPS, maupun Node.js sebagai syarat runtime.

Situs publik berisi Beranda, Tentang Kami, Program dan detail program, Berita & Kegiatan dan detail artikel, Bergabung, serta Kontak. Foto dokumentasi ditampilkan pada detail program atau artikel masing-masing; tidak ada halaman Galeri tersendiri. Panel admin menggunakan sidebar, bukan navbar atas, dan mengelola konten serta pendaftar intern dan relawan.

## Baca dokumen sesuai urutan

1. [`docs/PRD.md`](docs/PRD.md) — ruang lingkup dan kebutuhan produk yang disepakati.
2. [`docs/STEP.md`](docs/STEP.md) — urutan implementasi dan kriteria selesai tiap tahap.
3. [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — stack, struktur folder, route, MVC, dan deployment.
4. [`docs/ERD.md`](docs/ERD.md), lalu [`docs/SCHEMA.md`](docs/SCHEMA.md) — relasi data dan DDL MySQL.
5. [`docs/USERFLOW.md`](docs/USERFLOW.md), [`docs/DESIGN-ADMIN.md`](docs/DESIGN-ADMIN.md), dan [`docs/RWD.md`](docs/RWD.md) ? alur pengguna, desain admin, dan aturan responsif.
6. [`docs/RULES.md`](docs/RULES.md) — standar kode, keamanan, aksesibilitas, dan Git.
7. [`docs/GSAP.md`](docs/GSAP.md) — implementasi animasi pada tahap yang ditentukan.

`README.md` di root menjelaskan ringkasan proyek dan cara menavigasi dokumen.

## Alur kerja agent

1. Periksa `git status`, struktur folder, file terkait, dan instruksi `AGENTS.md` yang berlaku sebelum mulai.
2. Tentukan tahap aktif di `docs/STEP.md`; kerjakan prasyarat tahap itu terlebih dahulu.
3. Baca bagian dokumen yang langsung mengatur perubahan. Jangan menciptakan fitur atau mengubah keputusan produk tanpa dasar di PRD.
4. Buat perubahan sekecil yang menyelesaikan kebutuhan, sesuai struktur dan aturan MVC. Controller tipis; query berada di Model; aturan lintas model/file berada di Service; View hanya menyajikan data yang sudah di-escape.
5. Jangan menaruh kredensial atau data pribadi di repositori. Gunakan `.env.example` untuk nama variabel tanpa nilai rahasia.
6. Perbarui dokumentasi terkait bila implementasi mengubah route, tabel, keputusan produk, atau proses deployment. Jika dokumen saling bertentangan, tandai konflik dan selaraskan dokumen acuan sebelum melanjutkan.
7. Laporkan file yang diubah, keputusan yang diambil, batasan, dan pemeriksaan yang benar-benar dijalankan. Jangan menyatakan pemeriksaan lulus bila belum dilakukan.

## Batasan produk yang harus dijaga

- Role v1 hanya Public dan satu Admin/Product Owner.
- Pendaftaran intern dan relawan memakai batch. Admin dapat membuka atau menutup pendaftaran per batch.
- Foto dan CV pendaftar disimpan di lokasi privat dan hanya disajikan setelah pemeriksaan sesi admin. Foto dokumentasi program/berita adalah data terpisah.
- Foto dokumentasi berada di dalam detail program atau berita/kegiatan; jangan menambahkan halaman galeri mandiri.
- Konten yang ditetapkan statis di PRD tetap berada di `config/site.php` untuk v1; jangan membuat CMS umum tanpa perubahan ruang lingkup.
- Ikuti ERD dan DDL untuk bentuk data. Jangan menambah/menghapus kolom diam-diam.
- Semua mutasi admin memakai POST, perlindungan CSRF, validasi server, dan pemeriksaan autentikasi.
- Pertahankan tampilan publik dari aset HTML/CSS/JS yang sudah ada di root. Jangan menghapus aset lama sampai migrasi dan kriteria penerimaan tahap terkait terpenuhi.

## Keputusan teknis utama

- PHP minimum **8.4**, MySQL atau MariaDB yang disediakan paket hosting, PDO prepared statements.
- Shared hosting cPanel adalah target; seluruh fitur operasional harus bisa berjalan tanpa akses root atau proses server yang menetap.
- Composer dipakai untuk autoload dan pustaka ekspor. GSAP disajikan lokal. Tidak ada build frontend yang wajib di server.
- Folder aplikasi, `.env`, log, dan dokumen pendaftar berada di luar web root bila konfigurasi hosting memungkinkan. Hanya `public/` yang dapat diakses langsung.
- Deployment harus menjaga `.env`, `vendor/`, file unggahan, dan penyimpanan privat. Jangan memakai perintah sinkronisasi yang menghapus data runtime.

Jika paket hosting yang dipilih tidak menyediakan PHP 8.4 atau ekstensi/fitur yang diperlukan, dokumentasikan bukti dan dampaknya. Jangan diam-diam menurunkan versi minimum proyek.
