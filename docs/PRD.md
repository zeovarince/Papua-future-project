# PRD: Website Papua Future Project

Versi 1.0. Daftar fitur sudah disepakati (fix). Dokumen ini adalah acuan utama produk. Bila ada konflik dengan dokumen lain, PRD yang berlaku.

Dokumen terkait: [ARCHITECTURE.md](ARCHITECTURE.md), [ERD.md](ERD.md), [SCHEMA.md](SCHEMA.md), [USERFLOW.md](USERFLOW.md), [DESIGN-ADMIN.md](DESIGN-ADMIN.md), [GSAP.md](GSAP.md), [RULES.md](RULES.md), [AGENT.md](../AGENT.md), [STEP.md](STEP.md).

## 1. Latar belakang

Papua Future Project (PFP) adalah gerakan pendidikan dan kesehatan anak di Papua Barat dengan moto "Every Child Matters". PFP bermula dari satu tenda belajar di Pulau Mansinam pada 2020, digagas oleh Bhrisco Jordy Dudi Padatu, dan berdiri resmi pada 18 Agustus 2023. Saat ini PFP menjangkau 700+ anak di 13 kampung binaan dengan 250+ relawan dari seluruh Indonesia. PFP pernah menerima SATU Indonesia Awards 2022 dari Astra, dipercaya UNICEF sebagai mitra NGO potensial, dan berkolaborasi dengan Kemenkes RI. Kantornya berada di Kabupaten Manokwari.

Keadaan saat ini:

- UI publik sudah jadi sebagai situs statis (HTML, CSS, JS) di GitHub Pages: Beranda, Tentang Kami, Program (daftar dan detail), Berita (daftar dan detail), Jadi Relawan, dan Kontak.
- Yang belum ada: panel admin (dashboard) dan backend.

Karena itu proyek ini punya dua pekerjaan: memindahkan UI publik ke PHP native MVC dengan data dari MySQL, dan membangun panel admin beserta backend-nya.

## 2. Tujuan

1. Publik memahami siapa PFP, apa programnya, dan seberapa jauh dampaknya, lengkap dengan foto dokumentasi lapangan.
2. Calon relawan dan intern dapat memilih batch yang dibuka lalu melanjutkan pendaftaran melalui formulir eksternal yang ditautkan Admin.
3. Pengunjung bisa menghubungi PFP lewat form kontak.
4. Admin (Product Owner) bisa memperbarui konten, mengelola tim, mengatur link pendaftaran dan status per batch, serta membaca pesan masuk tanpa menyentuh kode.

## 3. Di luar cakupan v1

- Halaman Galeri terpisah. Foto dokumentasi hanya ada di dalam tiap Program dan tiap Berita atau Kegiatan.
- Pembayaran atau donasi uang online. Program Pojok Baca dan Donasi Buku hanya berupa informasi.
- Editor admin untuk konten statis: teks hero, Sekilas tentang PFP, angka impact, peta wilayah, logo mitra, Perjalanan kami, manfaat dan langkah bergabung, serta data kontak. Semua ini ditulis di `config/site.php` dan View (lihat A4).
- Akun pengunjung, komentar, newsletter, versi multi-bahasa.
- Notifikasi email ke admin saat ada pesan atau pendaftar baru.
- Banyak level admin. v1 hanya punya satu peran admin.

## 4. Peran pengguna

| Peran | Akses |
| --- | --- |
| Public | Melihat semua halaman publik, mengirim pesan lewat form kontak, dan membuka formulir eksternal saat batch tersedia. Tanpa login. |
| Admin / Product Owner | Login ke panel admin. Mengelola konten, tim, batch dan link pendaftaran, serta pesan masuk. |

## 5. Daftar fitur yang disepakati

Public:

- Beranda: 8 section (hero, sekilas PFP, profil founder, penghargaan, angka impact, preview program, preview berita, peta wilayah).
- Tentang Kami: cerita Jordy, visi dan misi, Tim Penggerak, Internship per batch, Volunteer per batch.
- Program: daftar dan detail per program, dengan foto dokumentasi.
- Berita dan Kegiatan: daftar dan detail per artikel, dengan foto dokumentasi.
- Bergabung: daftar batch pendaftaran; tombol Daftar membuka formulir dari link batch yang sedang dibuka.
- Kontak: lokasi, sosial media, form kontak, FAQ, peta, dan logo mitra.

Admin:

- CRUD Tentang Kami: cerita, visi dan misi, Tim Penggerak, data intern per batch, data volunteer per batch.
- Kelola link pendaftaran dan toggle buka atau tutup pendaftaran di halaman Batch, per batch.
- CRUD Program, termasuk foto dokumentasi.
- CRUD Berita dan Kegiatan, termasuk foto dokumentasi.
- CRUD FAQ.
- Pendaftaran baru dan jawaban dikelola oleh layanan eksternal pada link batch; tidak ada daftar pendaftar atau ekspor jawaban di panel admin website.

Tambahan kecil di luar daftar di atas, perlu persetujuan (lihat A6): halaman admin Pesan masuk untuk membaca isi form kontak, dan Ringkasan sebagai halaman awal admin. Ringkasan hanya menampilkan statistik konten/pesan dan pesan terbaru; pengaturan pendaftaran tetap hanya di Batch.

## 6. Kebutuhan Public

Kolom "Sumber" menunjukkan dari mana isi halaman berasal: **DB** (MySQL), **Statis** (ditulis di `config/site.php` atau View), atau **Hitung** (dihitung dari DB).

### 6.1 Beranda (`/`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-01 | Hero: tagline "Every Child Matters", subjudul, foto lapangan, tombol ajakan. | Statis |
| PUB-02 | Sekilas tentang PFP. | Statis |
| PUB-03 | Profil founder: foto, nama, teks profil panjang. | DB: anggota Tim Penggerak yang ditandai founder |
| PUB-04 | Showcase penghargaan: tiga penghargaan terbaru (judul, deskripsi, tahun dan keterangan). | DB: berita kategori Penghargaan |
| PUB-05 | Angka impact: 700+ anak, 13 kampung, 250+ relawan, sejak 2020. | Statis |
| PUB-06 | Preview 3 program unggulan. | DB: program yang ditandai unggulan |
| PUB-07 | Preview 3 berita atau kegiatan terbaru. | DB |
| PUB-08 | Peta wilayah: peta OpenStreetMap, daftar wilayah (Pulau Mansinam, Manokwari Kota, Warmare, Prafi, Ransiki, dan 8 kampung lainnya). | Statis |

### 6.2 Tentang Kami (`/tentang`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-10 | Sejarah dan latar belakang: cerita Jordy dan latar belakang pendirian. | DB |
| PUB-11 | Perjalanan kami: linimasa 2020 sampai 2023. | Statis |
| PUB-12 | Deretan foto dokumentasi umum. | Statis |
| PUB-13 | Visi (satu pernyataan) dan Misi (daftar butir). | DB |
| PUB-14 | Tim Penggerak: Founder, Koordinator Program, Koordinator Relawan, Koordinator Kesehatan. Grid 4 kolom. Card berisi foto bulat (inisial bila belum ada foto), nama, pill jabatan, dan deskripsi singkat. | DB |
| PUB-15 | Internship: intern per batch, berurutan ke bawah. Card berisi foto bulat, nama lengkap, asal universitas, pill batch, dan peran atau bidang. | DB |
| PUB-16 | Volunteer/Relawan: struktur dan tampilan sama dengan Internship, tetapi terpisah secara visual dan data. | DB |

### 6.3 Program (`/program` dan `/program/{slug}`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-20 | Strip angka di atas daftar: 700+ anak, 13 kampung, 250+ relawan, dan jumlah program aktif. | Statis, kecuali jumlah program (Hitung) |
| PUB-21 | Daftar program. Tiap card: label kategori, judul, ringkasan, dua angka kecil (contoh "700+ Anak" dan "13 Kampung"), tombol Lihat Detail Program. | DB |
| PUB-22 | "Dari Lapangan": enam foto dokumentasi terbaru dari semua program. | DB: foto program |
| PUB-23 | Detail program: breadcrumb, label kategori, judul, tagline, Tentang Program (paragraf), Kegiatan dalam Program (daftar), Mitra Kolaborasi (daftar), Informasi Program (pasangan label dan nilai), Program Lainnya, dan Dokumentasi (foto program dengan keterangan). | DB |

Lima program awal: Bimbingan Belajar dan Literasi, Literasi Digital, Pojok Baca dan Donasi Buku, Kesehatan dan Imunisasi (kolaborasi UNICEF dan Kemenkes RI), Pemberdayaan Pemuda Lokal.

### 6.4 Berita dan Kegiatan (`/berita` dan `/berita/{slug}`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-30 | Bagian "Terbaru": satu berita terbaru tampil besar dengan gambar sampul. | DB |
| PUB-31 | Daftar semua berita dengan label kategori dan tanggal. Empat kategori: Kegiatan Lapangan, Update Program, Liputan Media, Penghargaan. Bisa difilter per kategori. | DB |
| PUB-32 | "Diakui dan Diliput": daftar Penghargaan dan daftar Liputan Media (nama media, bulan dan tahun, judul, tautan ke artikel asli). | DB: berita kategori Penghargaan dan Liputan Media |
| PUB-33 | Detail berita: breadcrumb, label kategori, judul, tanggal, isi (paragraf dan kutipan), tombol Bagikan (salin tautan, tautan Instagram), Informasi Artikel (tanggal terbit, kategori, ditulis oleh), Berita Lainnya (3), Dokumentasi (foto), dan Baca Juga (3). | DB |

### 6.5 Bergabung (`/bergabung`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-40 | Hero ajakan ke halaman Bergabung. | Statis |
| PUB-41 | Manfaat menjadi relawan (tiga poin) dan langkah bergabung (empat langkah dengan perkiraan waktu). | Statis |
| PUB-42 | Tampilkan batch yang sedang dibuka. Tombol Daftar membuka link pendaftaran pada batch tersebut di tab baru. Beberapa batch Internship dan Relawan dapat dibuka bersamaan. | DB: status batch dan URL pendaftaran |
| PUB-43 | Pengiriman dan halaman konfirmasi dikelola oleh layanan pada link yang dipilih. Website PFP tidak menerima jawaban pendaftaran langsung. | Layanan formulir eksternal |

### 6.6 Kontak (`/kontak`)

| ID | Kebutuhan | Sumber |
| --- | --- | --- |
| PUB-50 | Temukan kami: Instagram @papuafutureproject, email papuafutureproject@gmail.com, kantor di Kabupaten Manokwari, jam operasional Senin sampai Jumat 08.00 sampai 17.00 WIT. | Statis |
| PUB-51 | Lokasi: peta OpenStreetMap dengan penanda Manokwari dan keterangan Pulau Mansinam sebagai titik utama kegiatan. | Statis |
| PUB-52 | Form kirim pesan: nama lengkap, email aktif, nomor WhatsApp (opsional), pesan, dan teks persetujuan penggunaan data. Aturan di bagian 9. | DB |
| PUB-53 | Logo mitra: UNICEF, Kemenkes RI, Astra (aset resmi menggantikan placeholder). | Statis |
| PUB-54 | FAQ (pertanyaan umum) dalam bentuk akordeon. | DB |
| PUB-55 | Ajakan penutup: Daftar Jadi Relawan dan Lihat Program. | Statis |

### 6.7 Kebutuhan umum halaman publik

- Header dengan navigasi (Beranda, Tentang Kami, Program, Berita, Kontak) dan tombol "Jadi Relawan". Footer berisi logo, deskripsi singkat, navigasi, alamat, Instagram, dan email. Tahun hak cipta dihitung otomatis.
- Halaman 404 dan halaman galat yang ramah.

## 7. Kebutuhan Admin

| ID | Kebutuhan |
| --- | --- |
| ADM-01 | Login admin. Semua halaman `/admin` hanya bisa diakses setelah login. |
| ADM-02 | CRUD Tentang Kami: ubah cerita, visi, dan misi. |
| ADM-03 | CRUD Tim Penggerak: tambah, ubah, hapus, atur urutan, tandai founder. |
| ADM-04 | CRUD data Intern, dikelompokkan per batch. |
| ADM-05 | CRUD data Relawan (Volunteer), dikelompokkan per batch. |
| ADM-06 | Batch: buat, ubah, hapus. Daftar Batch menjadi satu-satunya tempat mengubah status buka/tutup; halaman Internship dan Volunteer hanya mengelola anggota tim. |
| ADM-07 | CRUD Program: semua isian detail program (bagian 6.3), tandai unggulan, atur urutan, dan kelola foto dokumentasi. |
| ADM-08 | CRUD Berita dan Kegiatan: judul, kategori, tanggal terbit, ringkasan, isi, gambar sampul, penulis, keterangan khusus (untuk Penghargaan dan Liputan Media), dan kelola foto dokumentasi. Terbitkan atau simpan sebagai draf. |
| ADM-09 | CRUD FAQ: tambah, ubah, hapus, atur urutan, terbitkan atau sembunyikan. |
| ADM-10 | Pengaturan pendaftaran dibuat sederhana per batch: Admin mengisi link formulir apa pun saat membuat/mengubah batch dan membuka atau menutup pendaftaran langsung dari daftar Batch. |
| ADM-12 | (Tambahan, A6) Pesan masuk: daftar pesan dari form kontak, detail, tanda sudah dibaca, hapus. |
| ADM-13 | Ringkasan sebagai halaman awal menampilkan jumlah pesan belum dibaca, berita terbit, dan pesan terbaru. Tidak ada pengaturan pendaftaran di Ringkasan. |

| ADM-16 | Admin yang sudah masuk dapat membuat akun admin tambahan melalui panel tanpa menjalankan perintah atau mengedit kode. Setiap admin dapat mengganti kata sandi sendiri dengan verifikasi kata sandi saat ini. Semua akun memiliki satu tingkat akses Admin. |

## 8. Pendaftaran Relawan dan Intern

Form pendaftaran Internship dan Relawan dibuat serta dikelola melalui layanan eksternal pilihan Admin (misalnya Google Forms). Setiap batch menyimpan tautan formnya sendiri dan status buka/tutupnya. Admin mengatur tautan melalui Ubah Batch dan status langsung di daftar Batch. Pengunjung memilih batch yang tersedia di website, lalu tombol Daftar membuka form batch itu pada tab baru. Pertanyaan, unggahan, penerimaan jawaban, dan konfirmasi dikelola oleh layanan pada tautan tersebut; jawaban baru tidak masuk ke database website. Halaman pendaftar dan pengaturan form lokal versi lama tidak lagi menjadi fitur admin; tabel lama dipertahankan di database agar data yang sudah ada tidak terhapus saat pembaruan.

| Pengaturan | Data | Aturan |
| --- | --- | --- |
| Link pendaftaran | URL layanan formulir pilihan Admin pada setiap batch | URL absolut valid dengan skema `http` atau `https`, dari domain apa pun; diisi pada halaman Ubah Batch. |
| Keterbukaan | Status per batch | Tombol publik tampil hanya bila batch dibuka dan URL valid tersedia. |
| Jawaban pendaftar | Pertanyaan, dokumen, dan konfirmasi | Dikelola sepenuhnya oleh layanan eksternal pada link; bukan disimpan pada database website. |

Aturan bisnis:

1. Setiap batch Internship dan Relawan memiliki URL pendaftaran dan sakelar aktif sendiri.
2. Link Daftar hanya ditampilkan jika URL valid tersedia dan batch terkait dibuka.
3. URL disimpan setelah validasi URL absolut HTTP/HTTPS yang valid. Domain tidak dibatasi agar Admin dapat memakai layanan apa pun.
4. Menghapus URL otomatis menutup pendaftaran batch tersebut. Menutup batch tidak menghapus data pada layanan formulir.
5. Jawaban baru, foto, CV, seleksi, dan pesan konfirmasi disimpan/dikelola oleh layanan yang ditautkan; data tersebut tidak tersedia di panel Pendaftar website.
6. Batch yang sudah memiliki anggota tim tetap mengikuti aturan hapus batch yang berlaku; Admin dapat menutupnya kapan saja.

## 9. Form kontak

| Field | Wajib | Aturan |
| --- | --- | --- |
| Nama lengkap | Ya | Maksimal 100 karakter. |
| Email aktif | Ya | Format valid. |
| Nomor WhatsApp | Tidak | Bila diisi, nomor Indonesia. |
| Pesan | Ya | 10 sampai 2000 karakter. |

Pesan disimpan di database (A6) dan muncul di halaman admin Pesan masuk. Form dilindungi token CSRF, kolom jebakan (honeypot), dan batas laju per IP. Setelah terkirim, pengunjung kembali ke halaman Kontak dengan pesan sukses.

## 10. Foto dokumentasi (galeri)

1. Tiap Program dan tiap Berita atau Kegiatan punya galeri foto dokumentasi sendiri. Tidak ada halaman Galeri terpisah.
2. Tiap foto punya keterangan singkat (opsional) dan urutan.
3. Foto ditambahkan di halaman ubah setelah item tersimpan. Satu kali unggah maksimal 20 foto. Satu item maksimal 30 foto (A8).
4. Format JPG, PNG, atau WebP, maksimal 8 MB per foto. Server mengecilkan dan mengonversi ke WebP, serta membuat versi kecil (thumbnail).
5. Halaman detail menampilkan galeri di bagian Dokumentasi. Halaman daftar Program menampilkan enam foto terbaru lintas program di bagian "Dari Lapangan".
6. Menghapus program atau berita ikut menghapus foto beserta file-nya.

## 11. Data pendaftaran

Pendaftaran baru sepenuhnya berlangsung di layanan formulir eksternal yang dipilih Admin. Admin mengatur URL dan status aktif per batch melalui halaman Batch; jawaban, file, proses seleksi, dan ekspor dikelola oleh layanan tersebut. Website tidak menampilkan dashboard pendaftar atau menyediakan ekspor data pendaftaran.

Tabel aplikasi dan jawaban dari formulir website versi lama masih ada di `database/pfp.sql` sebagai arsip kompatibilitas agar pembaruan tidak menghapus data historis. Tabel tersebut bukan bagian fitur aktif dan tidak dipakai untuk pendaftaran baru.

## 12. Kebutuhan non-fungsional

- Responsif. UI dirancang untuk layar HP lebih dulu, dan harus nyaman di lebar 360 px.
- Performa: halaman publik terbuka cepat di jaringan seluler. Foto dioptimasi dan dimuat sesuai ukuran layar.
- Aksesibilitas: kontras memadai, fokus keyboard terlihat, teks alternatif untuk gambar, gerak mengikuti preferensi pengurangan gerak, halaman tetap berfungsi tanpa JavaScript.
- Keamanan data pribadi: lihat [ARCHITECTURE.md](ARCHITECTURE.md) bagian keamanan.
- Zona waktu: WIT (Asia/Jayapura). Tanggal tampil dalam format Indonesia, misalnya "15 Mei 2025".
- Bahasa antarmuka: Indonesia.
- SEO dasar: judul dan deskripsi per halaman, Open Graph untuk berita dan program, `robots.txt`, dan `sitemap.xml` sederhana.

## 13. Perubahan yang diperlukan pada UI statis saat migrasi

Dari pembacaan UI di GitHub Pages, ada selisih antara UI dan kebutuhan di atas. Perubahan ini dikerjakan di [STEP.md](STEP.md).

1. Tentang Kami belum punya section Internship dan Volunteer per batch. Ditambahkan dengan tampilan card yang sama dengan Tim Penggerak.
2. Pendaftaran Internship dan Relawan memakai link formulir pilihan Admin per batch; website menampilkan status ditutup bila tidak ada batch aktif.
3. Label kategori berita di Beranda (Pendidikan, Kesehatan, Pemuda) berbeda dengan di halaman Berita (Kegiatan Lapangan, Update Program, Liputan Media, Penghargaan). Setelah migrasi, keduanya memakai satu kategori dari database.
4. Judul dan teks card program di Beranda sedikit berbeda dengan di halaman Program. Setelah migrasi, Beranda memakai judul dan ringkasan program dari database. Samakan data seed.
5. Detail program (lima file `program-*.html`) dan detail berita (`berita-detail.html`) menjadi satu template View masing-masing dengan data dari database.
6. Detail berita belum punya galeri Dokumentasi. Ditambahkan.
7. Logo mitra di Kontak masih placeholder. Ganti dengan aset resmi.
8. Footer belum seragam antar halaman (email dan tautan Kontak hanya ada di sebagian). Dijadikan satu partial.

## 14. Keputusan terbuka dan asumsi

Dokumen ini memakai asumsi di bawah sampai ada keputusan lain. Ubah di sini lebih dulu, lalu sesuaikan dokumen lain.

| ID | Pertanyaan | Asumsi sementara | Dokumen terdampak |
| --- | --- | --- | --- |
| A1 | Bagaimana pendaftaran Internship dan Relawan? | Setiap batch menyimpan link pendaftaran pilihan Admin dan sakelar aktifnya sendiri. | ARCHITECTURE, SCHEMA, USERFLOW |
| A2 | Bagaimana anggota Internship dan Relawan muncul di Tentang Kami? | Admin mengelola anggota secara manual di menu Tim; tidak disinkronkan dari formulir eksternal. | ERD, SCHEMA |
| A4 | Konten statis (hero, sekilas, angka impact, peta, logo mitra, Perjalanan kami, manfaat dan langkah bergabung, kontak) bisa diedit admin? | Tidak di v1. Ditulis di `config/site.php` dan View. Bisa ditambah di fase lanjut. | ARCHITECTURE, DESIGN-ADMIN |
| A5 | Dari mana blok Penghargaan dan Liputan Media? | Dari berita berkategori Penghargaan dan Liputan Media. | ERD, SCHEMA |
| A6 | Ke mana pesan form kontak pergi? | Disimpan di tabel `contact_messages` dan dibaca di halaman admin Pesan masuk. Halaman ini tambahan di luar daftar fix. Bila tidak diinginkan, ganti dengan pengiriman email dan hapus tabelnya. | ERD, SCHEMA, DESIGN-ADMIN |
| A7 | Form pendaftaran lokal lama | Tidak digunakan. Jawaban baru dikelola layanan pada link batch; tabel lama dipertahankan sebagai arsip saja. | ARCHITECTURE, SCHEMA |
| A8 | Batas galeri | Maksimal 30 foto per item, 8 MB per foto. Foto dikelola di halaman ubah. | ARCHITECTURE |
| A9 | Slug detail program dan berita | Dibuat otomatis dari judul. | SCHEMA |
| A10 | Stack dan hosting | PHP 8.4 atau lebih baru, MySQL atau MariaDB, Composer (dependensi dipasang lokal bila SSH tidak tersedia), shared hosting DirectAdmin. Akun saat ini menggunakan DirectAdmin dan tidak mencantumkan SSH; cPanel tetap menjadi alternatif. Ketersediaan ekstensi, cron, rewrite, SSL, dan batas resource diverifikasi pada paket akun. | ARCHITECTURE, README, DEPLOY-DIRECTADMIN |

## 15. Kriteria penerimaan

1. Semua halaman publik tampil sama seperti UI statis pada lebar 360 px dan 1280 px, dengan data dari database.
2. Pengunjung tanpa login yang membuka URL `/admin` diarahkan ke login.
3. Saat admin menutup batch, tombol Daftar batch itu tidak tampil di halaman Bergabung. Bila tidak ada batch terbuka, website menampilkan status pendaftaran ditutup.
4. Tombol Daftar membuka URL yang sudah diatur untuk batch terpilih di tab baru.
5. Link atau status yang belum lengkap tidak pernah menampilkan tombol Daftar; membuka batch mensyaratkan URL HTTP/HTTPS yang valid.
6. Pengaturan Internship dan Relawan tersedia per batch di halaman Batch dan tidak muncul sebagai pengaturan terpisah di Ringkasan.
7. Foto yang diunggah di galeri program atau berita tampil di bagian Dokumentasi halaman detailnya, dan foto terbaru program tampil di "Dari Lapangan".
8. Perubahan oleh admin (program, berita, FAQ, tim, visi misi) tampil di halaman publik tanpa deploy ulang.
9. Pesan dari form kontak muncul di Pesan masuk dan ditandai belum dibaca.
10. Semua halaman publik tetap bisa dipakai dengan JavaScript dimatikan.

## 16. Rilis bertahap

Urutan kerja rinci ada di [STEP.md](STEP.md). Ringkasnya:

| Fase | Isi |
| --- | --- |
| 1 | Migrasi HTML ke PHP MVC dan pemisahan CSS serta JS (Step 1 dan 2). |
| 2 | Fondasi admin dan konten: database, login, Tentang Kami, Program, Berita, FAQ, Kontak (Step 3 sampai 7). |
| 3 | Batch, tim, dan tautan pendaftaran eksternal (Step 8 sampai 10). |
| 4 | Animasi, keamanan dan pengujian, deployment (Step 11 sampai 13). |
