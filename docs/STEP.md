# STEP: Tahapan Pengerjaan Website PFP

Panduan urutan kerja AI agent dan developer dari UI statis sampai situs dinamis siap dipasang di shared hosting. Tahapan ini mengikuti fitur yang disepakati di [PRD.md](PRD.md), rancangan di [ARCHITECTURE.md](ARCHITECTURE.md), skema di [SCHEMA.md](SCHEMA.md), dan aturan di [RULES.md](RULES.md).

## Cara memakai dokumen

- Kerjakan tahap secara berurutan karena tiap tahap membangun prasyarat tahap berikutnya.
- Satu tahap boleh dipecah menjadi beberapa pull request kecil. Jangan memulai fitur yang bergantung pada tahap belum selesai.
- Tahap dinyatakan selesai setelah seluruh hasil dan kriteria selesai diperiksa dan dicatat.
- Tugas verifikasi di dokumen ini adalah bagian dari definisi selesai; jangan mengklaim lolos tanpa menjalankannya.
- Jika keputusan produk atau tabel perlu berubah, perbarui PRD/ERD/SCHEMA terkait lebih dahulu dan jelaskan dampaknya.

## Fase 1 — Migrasi UI publik dan fondasi MVC

### Step 1 — Migrasi HTML statis ke PHP native MVC

**Tujuan:** halaman yang sekarang berupa HTML mandiri dapat dirender melalui satu front controller PHP dan pola MVC.

1. Inventarisasi halaman, tautan, aset, formulir, dan perbedaan antar halaman HTML di root.
2. Buat struktur folder target lengkap sesuai [ARCHITECTURE.md bagian 3](ARCHITECTURE.md#3-struktur-folder-lengkap), termasuk `public/`, `app/`, `config/`, `database/`, `storage/`, file Composer, dan berkas konfigurasi.
3. Siapkan `public/index.php`, bootstrap aplikasi, Router, Request, Response, View, helper, layout publik, dan route halaman.
4. Pindahkan markup tiap halaman ke View PHP/partial; satukan header, footer, navigasi, metadata, dan komponen berulang.
5. Pindahkan aset yang dipakai ke `public/assets/` dengan mempertahankan nama/isi visual yang relevan; ganti tautan `.html` dengan URL route bersih.
6. Pada tahap ini, data boleh sementara berasal dari array statis/fixture agar konversi markup dapat dilakukan tanpa menunggu database. Jangan mencampur query ke View.
7. Siapkan aturan rewrite Apache agar URL bersih diteruskan ke front controller; file aset yang ada tetap disajikan langsung.

**Hasil:** seluruh halaman publik dapat dibuka sebagai route PHP, memiliki layout bersama, navigasi berfungsi, dan belum membutuhkan login atau database untuk ditampilkan.

**Kriteria selesai:** daftar halaman pada PRD punya route; tidak ada tautan navigasi yang menuju `.html`; tampilan utama dibandingkan dengan file statis pada lebar mobile dan desktop; 404 ditangani dengan benar.

### Step 2 — Pisahkan CSS dan JavaScript dari file PHP

**Tujuan:** View hanya memuat markup PHP/HTML dan tidak menjadi tempat penampung stylesheet atau skrip inline.

1. Ekstrak CSS inline dan atribut `style` ke `public/assets/css/`, pisahkan base, komponen, halaman, dan admin sesuai struktur arsitektur.
2. Ekstrak JavaScript inline ke `public/assets/js/`; gunakan `data-*` atau konfigurasi JSON yang di-escape untuk nilai dinamis yang memang perlu dikirim dari View.
3. Perbaiki semua path aset menjadi path helper `asset()`; pastikan font, gambar, ikon, dan file lokal dimuat melalui URL yang benar.
4. Tambahkan atribut `data-page` yang konsisten untuk kebutuhan skrip per halaman.
5. Hilangkan ketergantungan skrip/gaya inline agar Content Security Policy yang direncanakan dapat diterapkan.
6. Cocokkan kembali tampilan dengan HTML awal. Jangan mengubah desain publik sebagai efek samping pemisahan aset.

**Hasil:** file CSS/JS terpisah dan terorganisasi, serta halaman publik tetap berfungsi.

**Kriteria selesai:** pencarian sumber tidak menemukan blok `<style>`/`<script>` inline yang berisi logika/aturan halaman; semua file aset memberi respons sukses; tidak ada galat Console yang menghambat fungsi utama; UI tetap responsif.

## Fase 2 — Dashboard dan konten dinamis

### Step 3 — Fondasi database, autentikasi, dan dashboard admin

1. Pasang Composer/autoload PSR-4 dan konfigurasi `.env` tanpa memasukkan rahasia ke Git.
2. Terapkan migrasi yang tersedia menurut urutan di [SCHEMA.md](SCHEMA.md) (Step 3 membuat tabel admin dan rate limit). Siapkan koneksi PDO, logger, sesi, Flash, CSRF, dan penanganan error.
3. Buat akun admin awal lewat `php bin/create-admin.php` dengan password dari environment; skrip hanya menyimpan hash dan menolak email duplikat.
4. Implementasikan login, logout, middleware autentikasi, rate limit login, regenerasi sesi, dan route admin terlindungi.
5. Bangun kerangka dashboard sesuai [DESIGN-ADMIN.md](DESIGN-ADMIN.md): sidebar di kiri, tanpa navbar atas, layout responsif, breadcrumb, area konten, dan halaman ringkasan sederhana.
6. Tambahkan status kosong yang jujur jika metrik belum tersedia; jangan menampilkan angka palsu.

**Kriteria selesai:** pengunjung tanpa sesi tidak dapat membuka admin; admin dapat login/logout; sesi dan token CSRF bekerja; dashboard dan sidebar berfungsi di desktop dan mobile. Kode fondasi sudah tersedia; sebelum pemakaian, isi `.env`, jalankan migrasi, lalu buat akun admin dengan skrip CLI.

### Step 4 — Tentang Kami dan pengelolaan tim/batch

1. Sambungkan cerita, visi, dan misi ke `about_content` serta buat formulir admin.
2. Implementasikan CRUD Tim Penggerak, termasuk penanda founder dan profil singkat/panjang.
3. Implementasikan CRUD batch intern dan relawan beserta toggle buka/tutup pendaftaran per batch.
4. Implementasikan CRUD daftar intern/relawan per batch dan validasi kecocokan jenis anggota dengan jenis batch.
5. Render halaman Tentang Kami dari database: cerita, visi-misi, Tim Penggerak, internship per batch, dan volunteer per batch.

**Kriteria selesai:** perubahan konten tampil publik tanpa deploy ulang; batch/anggota tersusun sesuai urutan; batch dengan anggota atau pendaftar tidak dapat dihapus jika melanggar aturan relasi.

### Step 5 — Program, detail, dan foto dokumentasi

1. Buat CRUD program dengan status draf/terbit dan penanda unggulan.
2. Buat layanan unggah gambar: periksa MIME/ukuran, nama acak, optimasi, batas foto, dan hapus file saat foto/item dihapus.
3. Buat pengelola foto dokumentasi di formulir program: urutan, caption, unggah, dan hapus.
4. Sambungkan daftar program dan detail `/program/{slug}` dengan data MySQL.
5. Tampilkan foto dokumentasi pada detail program dan foto pilihan di bagian lapangan yang ditentukan PRD.

**Kriteria selesai:** program draf tidak tampil publik; slug unik; unggah yang tidak valid ditolak; urutan foto konsisten; beranda menampilkan program unggulan.

### Step 6 — Berita & Kegiatan, detail, dan foto dokumentasi

1. Buat CRUD artikel dengan kategori, status publikasi, dan tanggal terbit.
2. Buat pengelola foto dokumentasi artikel menggunakan aturan unggah yang sama dan tabel `post_photos`.
3. Sambungkan halaman daftar, filter/kategori yang sudah ditetapkan, detail artikel, preview beranda, penghargaan, dan liputan media.
4. Pastikan isi artikel ditampilkan sebagai teks aman sesuai aturan rendering PRD/RULES.

**Kriteria selesai:** hanya artikel terbit yang tampil pada publik; halaman detail menampilkan foto dokumentasi; penghargaan dan liputan media berasal dari kategori artikel yang sesuai.

### Step 7 — FAQ, form kontak, dan konten publik pendukung

1. Implementasikan CRUD FAQ dan urutan tampilnya.
2. Implementasikan form kontak dengan validasi, CSRF, rate limit, dan penyimpanan ke `contact_messages`.
3. Implementasikan daftar/detail pesan masuk admin dan aksi tandai sudah dibaca.
4. Isi data statis publik di `config/site.php` sesuai PRD: kontak/lokasi, sosial media, peta, mitra, angka dampak, hero, dan konten statis lain.
5. Sambungkan semua section halaman Kontak dan footer.

**Kriteria selesai:** FAQ yang aktif tampil publik; pesan tersimpan dan dapat dibaca admin; spam dasar dibatasi; konten statis konsisten dengan aset yang disetujui.

## Fase 3 — Pendaftaran dan ekspor

### Step 8 — Direktori batch dan halaman Bergabung

1. Tampilkan batch intern dan relawan terpisah di halaman Tentang Kami sesuai urutan batch.
2. Buat halaman Bergabung yang menampilkan pilihan hanya untuk batch terbuka.
3. Tampilkan keadaan yang jelas ketika semua batch ditutup; jangan tampilkan form yang tidak dapat dikirim.

**Kriteria selesai:** toggle admin langsung tercermin di pilihan publik; tipe pendaftaran mengikuti batch; jalur intern dan relawan jelas.

### Step 9 — Form pendaftaran dan unggahan privat

1. Buat form pendaftaran sesuai kolom PRD: identitas, email, WhatsApp, universitas sesuai aturan, batch, motivasi, foto, dan CV PDF.
2. Validasi sisi server untuk tipe, ukuran, isi file, aturan umur/pendidikan, dan email unik per batch.
3. Simpan foto dan CV dengan nama acak di luar web root; lakukan operasi database/file dengan kompensasi bila salah satu gagal.
4. Buat halaman berhasil dan pesan validasi yang tidak membocorkan data sensitif.
5. Lindungi jalur penyajian foto/CV melalui controller admin yang memeriksa sesi dan tipe file.

**Kriteria selesai:** batch tertutup tidak menerima POST meski request dibuat manual; file privat tidak dapat dibuka dengan URL langsung; admin dapat melihat foto inline serta membuka/mengunduh CV.

### Step 10 — Panel pendaftar, status, dan ekspor

1. Buat tabel pendaftar dengan filter tipe/batch/status, pencarian, dan pagination.
2. Buat halaman detail pendaftar dan pengelolaan status sesuai pilihan di PRD.
3. Buat ekspor CSV dan XLSX dengan seluruh kolom teks serta URL file yang hanya dapat diakses admin.
4. Buat laporan PDF dengan data rapi, foto inline, dan akses CV yang tersedia.
5. Terapkan batas ukuran dataset dan praktik streaming/pekerjaan sederhana yang sesuai kemampuan shared hosting; jangan mengandalkan antrean proses yang menetap.

**Kriteria selesai:** data ekspor mengikuti filter yang dipilih/dijelaskan; karakter CSV dibuka dengan benar di spreadsheet; PDF menampilkan foto dan tautan CV; endpoint ekspor dilindungi autentikasi dan otorisasi.

## Fase 4 — Poles, rilis, dan operasi

### Step 11 — Animasi GSAP

Implementasikan animasi sesuai [GSAP.md](GSAP.md) setelah markup final: muat GSAP lokal, modul per halaman, ScrollTrigger seperlunya, dan animasi ringan admin. Pastikan konten tetap tampak ketika JavaScript mati atau pengguna memilih `prefers-reduced-motion`.

**Kriteria selesai:** setiap animasi punya fallback; tidak ada konten yang bergantung pada animasi untuk terlihat; animasi tidak menyebabkan pergeseran layout besar atau menghalangi interaksi.

Implementasi saat ini memakai GSAP 3.15.0 dan ScrollTrigger lokal, modul dinamis untuk halaman publik, counter dampak di beranda, serta transisi CSS ringan untuk notifikasi admin. Pemeriksaan visual tanpa JavaScript, reduced motion, dan viewport mobile dicatat pada Step 12.

### Step 12 — Pemeriksaan penerimaan, keamanan, dan aksesibilitas

1. Cocokkan kriteria penerimaan PRD pada viewport mobile dan desktop.
2. Periksa semua route publik/admin, status kosong, error validasi, 404/500, dan alur pendaftaran lengkap.
3. Tinjau SQL prepared statements, escaping, CSRF, autentikasi, rate limit, upload, header, log, dan rahasia.
4. Periksa keyboard, label form, fokus terlihat, kontras, alt gambar, dan reduced motion.
5. Jalankan pemeriksaan yang tersedia di proyek dan laporkan hasil nyata serta masalah yang tersisa.

**Kriteria selesai:** tidak ada cacat blocker pada alur utama; semua pemeriksaan yang dijalankan tercatat; masalah yang belum selesai mempunyai langkah tindak lanjut.

#### Catatan audit awal — 19 Oktober 2026

- `composer validate --no-check-publish` menyatakan `composer.json` valid; ada peringatan bahwa metadata lisensi belum diisi.
- PHP CLI yang tersedia adalah 8.4.23. Pemeriksaan sintaks lolos untuk 125 file PHP dan 17 file JavaScript. Ekstensi PDO MySQL dan GD tersedia, tetapi `zip` belum dimuat sehingga ekspor XLSX belum dapat diuji penuh.
- Smoke test lokal: `/admin/login` merespons 200, `/admin` mengarahkan ke login (303), route tidak dikenal merespons 404, dan aset CSS/JS merespons 200.
- Halaman publik yang membaca MySQL merespons 500 karena tidak ada listener TCP di `127.0.0.1:3306` meskipun service `MySQL80` berstatus berjalan. Periksa port di Laragon dan samakan dengan `DB_PORT` pada `.env`. Pemeriksaan CRUD, form, unggahan, dan ekspor yang membutuhkan database menunggu koneksi pulih.
- `tests/Unit` dan `tests/E2E` masih berisi placeholder; PHPUnit belum terpasang.
- Header Content Security Policy belum diterapkan. Halaman publik masih memuat Tailwind CDN dan Google Fonts, jadi kebijakan CSP perlu diselaraskan dengan aset tersebut sebelum diberlakukan agar tidak merusak tampilan.

**Status:** Step 12 masih berlangsung. Ulangi pemeriksaan halaman publik dan alur CRUD/form/unggah/ekspor setelah MySQL dan ekstensi yang dibutuhkan aktif. Lengkapi audit CSP sebelum menandai tahap ini selesai.

### Step 13 — Siapkan dan deploy ke shared hosting cPanel

1. Pastikan paket provider menyediakan PHP 8.4+, ekstensi wajib, MySQL/MariaDB, HTTPS, dan batas resource/unggah memadai. Verifikasi di paket akun yang benar karena fasilitas dapat berbeda antar paket.
2. Buat database, user, folder aplikasi privat, `.env` production, dan direktori penyimpanan; set permission minimum yang diperlukan.
3. Konfigurasikan document root atau layout `public_html` sesuai [ARCHITECTURE.md bagian 13](ARCHITECTURE.md#13-deployment-di-cpanel-dengan-git).
4. Pasang dependency production, jalankan migrasi melalui CLI yang tersedia, lalu buat akun admin eksplisit dengan `bin/create-admin.php` dan password kuat dari environment. Seed konten hanya ditambahkan bila tahap terkait memerlukannya.
5. Jalankan deployment dari Git/cPanel. Pastikan berkas upload dan penyimpanan privat tidak tersentuh oleh sinkronisasi kode.
6. Periksa HTTPS, beranda, route detail, login, satu alur form, unggahan, ekspor, log, backup, dan cron cleanup.

**Kriteria selesai:** rilis bisa dipulihkan dari cadangan; `.env` dan dokumen pendaftar tidak dapat diakses publik; halaman utama dan alur admin kritis berfungsi pada hosting target.

## Pemetaan fase ringkas

| Fase | Tahap | Cakupan |
| --- | --- | --- |
| 1 | 1–2 | Migrasi UI statis ke MVC dan pemisahan aset CSS/JS. |
| 2 | 3–7 | Fondasi admin, autentikasi, konten, program, berita, FAQ, dan kontak. |
| 3 | 8–10 | Batch, pendaftaran intern/relawan, peninjauan data, dan ekspor. |
| 4 | 11–13 | Animasi, pemeriksaan penerimaan, keamanan, deployment dan operasi. |
