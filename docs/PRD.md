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
2. Calon relawan dan intern bisa mendaftar online, lengkap dengan foto dan CV.
3. Pengunjung bisa menghubungi PFP lewat form kontak.
4. Admin (Product Owner) bisa memperbarui konten, mengelola tim, membuka atau menutup pendaftaran per batch, meninjau pendaftar, dan mengekspor data tanpa menyentuh kode.

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
| Public | Melihat semua halaman publik, mengirim pesan lewat form kontak, dan mengisi form pendaftaran saat ada batch yang dibuka. Tanpa login. |
| Admin / Product Owner | Login ke panel admin. Mengelola konten, tim, batch, pendaftar, dan pesan masuk. |

## 5. Daftar fitur yang disepakati

Public:

- Beranda: 8 section (hero, sekilas PFP, profil founder, penghargaan, angka impact, preview program, preview berita, peta wilayah).
- Tentang Kami: cerita Jordy, visi dan misi, Tim Penggerak, Internship per batch, Volunteer per batch.
- Program: daftar dan detail per program, dengan foto dokumentasi.
- Berita dan Kegiatan: daftar dan detail per artikel, dengan foto dokumentasi.
- Bergabung: form relawan (dibuka atau ditutup oleh admin).
- Kontak: lokasi, sosial media, form kontak, FAQ, peta, dan logo mitra.

Admin:

- CRUD Tentang Kami: cerita, visi dan misi, Tim Penggerak, data intern per batch, data volunteer per batch.
- Toggle buka atau tutup pendaftaran intern per batch.
- Toggle buka atau tutup pendaftaran relawan per batch.
- CRUD Program, termasuk foto dokumentasi.
- CRUD Berita dan Kegiatan, termasuk foto dokumentasi.
- CRUD FAQ.
- Lihat dan kelola pendaftar (detail, ekspor PDF dan Excel).

Tambahan kecil di luar daftar di atas, perlu persetujuan (lihat A6): halaman admin Pesan masuk untuk membaca isi form kontak, dan halaman Ringkasan di dashboard.

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
| PUB-40 | Hero ajakan dan tombol Daftar Sekarang yang menuju form. | Statis |
| PUB-41 | Manfaat menjadi relawan (tiga poin) dan langkah bergabung (empat langkah dengan perkiraan waktu). | Statis |
| PUB-42 | Form pendaftaran, hanya tampil saat ada batch yang dibuka. Bila tidak ada, tampil pesan bahwa pendaftaran belum dibuka beserta tautan Instagram. Isian dan aturan di bagian 8. | DB: batch yang dibuka |
| PUB-43 | Setelah berhasil, tampil halaman konfirmasi (`/bergabung/berhasil`). | Statis |

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
| ADM-01 | Login admin. Semua halaman `/admin` dan semua file pendaftar hanya bisa diakses setelah login. |
| ADM-02 | CRUD Tentang Kami: ubah cerita, visi, dan misi. |
| ADM-03 | CRUD Tim Penggerak: tambah, ubah, hapus, atur urutan, tandai founder. |
| ADM-04 | CRUD data Intern, dikelompokkan per batch. |
| ADM-05 | CRUD data Relawan (Volunteer), dikelompokkan per batch. |
| ADM-06 | Batch: buat, ubah, hapus. Toggle buka atau tutup pendaftaran per batch, baik untuk intern maupun relawan. Toggle tersedia di halaman Batch dan di header tiap kelompok batch pada halaman Internship dan Volunteer. |
| ADM-07 | CRUD Program: semua isian detail program (bagian 6.3), tandai unggulan, atur urutan, dan kelola foto dokumentasi. |
| ADM-08 | CRUD Berita dan Kegiatan: judul, kategori, tanggal terbit, ringkasan, isi, gambar sampul, penulis, keterangan khusus (untuk Penghargaan dan Liputan Media), dan kelola foto dokumentasi. Terbitkan atau simpan sebagai draf. |
| ADM-09 | CRUD FAQ: tambah, ubah, hapus, atur urutan, terbitkan atau sembunyikan. |
| ADM-10 | Daftar pendaftar dengan filter tipe, batch, dan status, serta pencarian nama. Halaman detail menampilkan semua data, foto inline, tombol "Lihat CV" (tab baru) dan "Download CV". Admin mengubah status: Baru, Ditinjau, Diterima, Ditolak. |
| ADM-11 | Ekspor data pendaftar ke Excel/CSV dan PDF (bagian 10). |
| ADM-12 | (Tambahan, A6) Pesan masuk: daftar pesan dari form kontak, detail, tanda sudah dibaca, hapus. |
| ADM-13 | (Tambahan) Ringkasan: jumlah pendaftar baru, pesan belum dibaca, batch yang sedang dibuka, dan berita terakhir. |

## 8. Pendaftaran Relawan dan Intern

Satu form dipakai untuk dua tipe. Tipe (Intern atau Relawan) ditentukan oleh batch yang dipilih. Isian di bawah menggabungkan form yang sudah ada di UI dengan kebutuhan baru (batch, asal universitas, foto, CV).

| Kelompok | Field | Wajib | Aturan |
| --- | --- | --- | --- |
| Batch | Batch | Ya | Hanya batch yang sedang dibuka yang bisa dipilih. Nama batch menyebut tipenya. |
| Data diri | Nama lengkap | Ya | Maksimal 100 karakter. |
| | Tanggal lahir | Ya | Tanggal valid. Usia minimal 17 tahun (usulan, A7). |
| | Email aktif | Ya | Format valid, disimpan dalam huruf kecil. |
| | Nomor WhatsApp | Ya | Nomor Indonesia, diawali 08 atau +62. |
| | Domisili saat ini | Ya | Maksimal 120 karakter. |
| | Pekerjaan atau status saat ini | Ya | Pilihan: Mahasiswa, Fresh Graduate, Karyawan atau Profesional, Wiraswasta, Lainnya. |
| | Asal universitas atau instansi | Bersyarat | Wajib bila status Mahasiswa atau Fresh Graduate, opsional untuk lainnya (A7). |
| Ketersediaan dan minat | Durasi penugasan | Ya | Pilihan: 2 minggu, 1 bulan, Fleksibel. |
| | Bidang yang ingin dikontribusikan | Ya | Pilih satu atau lebih: Literasi dan Bimbingan Belajar, Edukasi Kesehatan dan Gizi, Kegiatan Kreatif (seni, olahraga, permainan), Dokumentasi dan Media, Lainnya atau sesuai kebutuhan tim. |
| | Dari mana tahu PFP | Ya | Pilihan: Instagram @papuafutureproject, Diajak teman atau kenalan, Artikel atau berita online, Event atau seminar atau pameran, Lainnya. |
| Motivasi | Kenapa ingin bergabung | Ya | 50 sampai 1000 karakter. |
| | Keahlian khusus | Tidak | Maksimal 500 karakter. |
| Berkas | Foto | Ya | JPG, PNG, atau WebP. Maksimal 2 MB. |
| | CV | Ya | PDF. Maksimal 5 MB. |

Aturan bisnis:

1. Satu email hanya bisa mendaftar satu kali per batch.
2. Bila batch ditutup saat pengunjung mengisi, pengiriman ditolak dengan pesan yang jelas dan isian teks tidak hilang.
3. File disimpan di server di luar folder publik. Foto dan CV hanya bisa dibuka admin yang sedang login.
4. Batch yang sudah punya pendaftar atau anggota tim tidak bisa dihapus. Admin cukup menutupnya.
5. Setelah berhasil, pengunjung melihat halaman konfirmasi yang menyebut bahwa tim PFP akan menghubungi lewat WhatsApp atau email dalam 3 sampai 5 hari kerja.

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

## 11. Ekspor data pendaftar

| Format | Isi |
| --- | --- |
| Excel (XLSX) dan CSV | Semua kolom teks (tipe, batch, nama lengkap, tanggal lahir, email, WhatsApp, domisili, status pekerjaan, asal universitas atau instansi, durasi, bidang kontribusi, sumber informasi, motivasi, keahlian khusus, status, tanggal daftar) ditambah tautan langsung ke foto dan CV di server. Di XLSX tautan berupa hyperlink sel. Tautan terbuka selama admin masih login di browser yang sama. |
| PDF | Laporan formal dan rapi dengan foto pendaftar tampil inline dan tautan CV yang bisa diklik. |

Kedua format diunduh langsung dari panel admin dan mengikuti filter yang sedang aktif di daftar pendaftar.

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
2. Form Jadi Relawan belum punya Batch, Asal universitas, Foto, dan CV. Ditambahkan. Keadaan "pendaftaran ditutup" juga perlu dirancang.
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
| A1 | Satu form atau dua form untuk intern dan relawan? | Satu form. Tipe mengikuti batch. | ERD, SCHEMA, USERFLOW |
| A2 | Card Intern dan Relawan diinput manual atau otomatis dari pendaftar? | Manual lewat CRUD. Tidak ada relasi otomatis ke pendaftar. | ERD, SCHEMA |
| A3 | Bagaimana file pendaftar disimpan? | Privat di luar folder publik, dibuka lewat controller yang memeriksa sesi admin. | ARCHITECTURE |
| A4 | Konten statis (hero, sekilas, angka impact, peta, logo mitra, Perjalanan kami, manfaat dan langkah bergabung, kontak) bisa diedit admin? | Tidak di v1. Ditulis di `config/site.php` dan View. Bisa ditambah di fase lanjut. | ARCHITECTURE, DESIGN-ADMIN |
| A5 | Dari mana blok Penghargaan dan Liputan Media? | Dari berita berkategori Penghargaan dan Liputan Media. | ERD, SCHEMA |
| A6 | Ke mana pesan form kontak pergi? | Disimpan di tabel `contact_messages` dan dibaca di halaman admin Pesan masuk. Halaman ini tambahan di luar daftar fix. Bila tidak diinginkan, ganti dengan pengiriman email dan hapus tabelnya. | ERD, SCHEMA, DESIGN-ADMIN |
| A7 | Aturan khusus form pendaftaran | Usia minimal 17 tahun. Asal universitas wajib hanya untuk Mahasiswa dan Fresh Graduate. | SCHEMA, RULES |
| A8 | Batas galeri | Maksimal 30 foto per item, 8 MB per foto. Foto dikelola di halaman ubah. | ARCHITECTURE |
| A9 | Slug detail program dan berita | Dibuat otomatis dari judul. | SCHEMA |
| A10 | Stack dan hosting | PHP 8.4 atau lebih baru, MySQL atau MariaDB, Composer tersedia, shared hosting cPanel dengan Git Version Control. Ketersediaan versi, ekstensi, Terminal/SSH, cron, dan batas resource diverifikasi pada paket akun yang dipilih. | ARCHITECTURE, README |

## 15. Kriteria penerimaan

1. Semua halaman publik tampil sama seperti UI statis pada lebar 360 px dan 1280 px, dengan data dari database.
2. Pengunjung tanpa login yang membuka URL `/admin` atau URL file pendaftar diarahkan ke login dan tidak menerima file apa pun.
3. Saat admin menutup batch, batch itu langsung hilang dari pilihan di form publik. Bila tidak ada batch terbuka, form tidak tampil.
4. Pendaftar dengan email yang sudah terdaftar di batch yang sama ditolak dengan pesan yang jelas.
5. Detail pendaftar menampilkan foto inline. "Lihat CV" membuka tab baru dan "Download CV" mengunduh file.
6. Ekspor XLSX dan CSV memuat semua kolom dan tautan foto serta CV. Ekspor PDF memuat foto inline dan tautan CV.
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
| 3 | Batch, tim, pendaftaran, dan ekspor (Step 8 sampai 10). |
| 4 | Animasi, keamanan dan pengujian, deployment (Step 11 sampai 13). |
