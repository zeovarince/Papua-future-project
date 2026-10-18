# SCHEMA: DDL MySQL

DDL lengkap semua tabel, siap dijalankan di MySQL 8 atau MariaDB 10.6 ke atas. Untuk impor sekaligus lewat phpMyAdmin, gunakan [`../database/pfp.sql`](../database/pfp.sql) setelah membuat dan memilih database kosong. Diagram dan penjelasan relasi ada di [ERD.md](ERD.md). Kebutuhan produk ada di [PRD.md](PRD.md).

## 1. Aturan penamaan

1. Tabel memakai snake_case dan bentuk jamak (`team_members`), kecuali `about_content` yang memang satu set konten.
2. Kolom memakai snake_case. Kunci asing memakai `{tabel_tunggal}_id`.
3. Hindari kata yang dicadangkan SQL. Karena itu kolomnya bernama `member_group` (bukan `group`), `batch_type`, dan `bucket`.
4. Boolean memakai `TINYINT(1)` dengan awalan `is_`.
5. Semua tabel memakai `ENGINE=InnoDB`, `utf8mb4`, dan collation `utf8mb4_unicode_ci`.
6. Semua waktu disimpan dalam WIT (UTC+9). Koneksi PDO menjalankan `SET time_zone = '+09:00'` (lihat [ARCHITECTURE.md](ARCHITECTURE.md)).
7. Kolom `JSON` dibaca PDO sebagai string. Model yang mengubahnya dengan `json_decode` dan `json_encode`. Di MariaDB, `JSON` adalah alias `LONGTEXT` dengan pemeriksaan `JSON_VALID`.

## 2. Urutan migrasi

Satu file SQL per berkas di `database/migrations/`. Urutan mengikuti urutan pengerjaan di [STEP.md](STEP.md).

| File | Isi | Dipakai di step |
| --- | --- | --- |
| `0001_create_admin_users.sql` | `admin_users` | 3 |
| `0002_create_rate_limits.sql` | `rate_limits` | 3 |
| `0003_create_about_content.sql` | `about_content` | 4 |
| `0004_create_batches_and_team_members.sql` | `batches`, `team_members` | 4 dan 8 |
| `0005_create_programs_and_photos.sql` | `programs`, `program_photos` | 5 |
| `0006_create_posts_and_photos.sql` | `posts`, `post_photos` | 6 |
| `0007_create_faqs_and_contact_messages.sql` | `faqs`, `contact_messages` | 7 |
| `0008_create_applications.sql` | `applications` | 9 |
| `0009_seed_initial_programs.sql` | Lima data awal program untuk instalasi migrasi | 5 |
| `0010_seed_news_and_faqs.sql` | Konten awal berita, penghargaan, liputan media, dan FAQ | 6 dan 7 |

## 3. Tabel infrastruktur migrasi

`bin/migrate.php` membuat tabel pencatatan berikut sebelum menjalankan migrasi. Tabel ini dikelola skrip migrasi dan bukan tabel domain di ERD.

```sql
CREATE TABLE schema_migrations (
  migration VARCHAR(190) NOT NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (migration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 4. DDL

### 0001_create_admin_users.sql

```sql
CREATE TABLE admin_users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email         VARCHAR(190) NOT NULL,
  name          VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 0002_create_rate_limits.sql

```sql
-- Pembatas laju untuk login admin, form pendaftaran, dan form kontak.
-- bucket: login, join, contact. identifier: hash dari IP (dan email untuk login).
CREATE TABLE rate_limits (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bucket     VARCHAR(40) NOT NULL,
  identifier VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_rate_limits_lookup (bucket, identifier, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 0003_create_about_content.sql

```sql
-- Hanya satu baris, id selalu 1. Dibuat oleh seed.
CREATE TABLE about_content (
  id         TINYINT UNSIGNED NOT NULL DEFAULT 1,
  story      MEDIUMTEXT NOT NULL,
  vision     TEXT NOT NULL,
  mission    TEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT chk_about_content_single CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

`story` berisi sejarah dan latar belakang (paragraf dipisah baris kosong). `vision` berisi satu pernyataan visi. `mission` berisi satu butir misi per baris.

### 0004_create_batches_and_team_members.sql

```sql
CREATE TABLE batches (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  batch_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  name       VARCHAR(60) NOT NULL,
  is_open    TINYINT(1) NOT NULL DEFAULT 0,
  opened_at  DATETIME NULL,
  closed_at  DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_batches_type_name (batch_type, name),
  KEY idx_batches_type_open (batch_type, is_open)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_members (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  member_group ENUM('CORE','INTERN','VOLUNTEER') NOT NULL,
  batch_id     INT UNSIGNED NULL,
  name         VARCHAR(100) NOT NULL,
  role         VARCHAR(120) NOT NULL,
  university   VARCHAR(120) NULL,
  bio          VARCHAR(400) NULL,
  long_bio     TEXT NULL,
  photo_path   VARCHAR(255) NULL,
  is_founder   TINYINT(1) NOT NULL DEFAULT 0,
  sort_order   INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_team_group_sort (member_group, sort_order),
  KEY idx_team_batch (batch_id),
  CONSTRAINT fk_team_members_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Catatan kolom `team_members`:

- `role`: jabatan untuk grup `CORE` (contoh "Founder & Ketua"), peran atau bidang untuk intern dan relawan.
- `bio`: deskripsi singkat untuk card.
- `long_bio`: profil panjang, dipakai untuk profil founder di Beranda. Diisi hanya untuk founder.
- `photo_path`: kosong berarti card menampilkan inisial nama.
- `is_founder`: dipakai untuk profil founder di Beranda.

### 0005_create_programs_and_photos.sql

```sql
CREATE TABLE programs (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug           VARCHAR(160) NOT NULL,
  category_label VARCHAR(40) NOT NULL,
  title          VARCHAR(160) NOT NULL,
  summary        VARCHAR(400) NOT NULL,
  tagline        VARCHAR(220) NOT NULL,
  body           MEDIUMTEXT NOT NULL,
  activities     TEXT NULL,
  partners       TEXT NULL,
  info_rows      JSON NULL,
  stat1_value    VARCHAR(20) NULL,
  stat1_label    VARCHAR(40) NULL,
  stat2_value    VARCHAR(20) NULL,
  stat2_label    VARCHAR(40) NULL,
  cover_image    VARCHAR(255) NULL,
  sort_order     INT NOT NULL DEFAULT 0,
  is_featured    TINYINT(1) NOT NULL DEFAULT 0,
  is_published   TINYINT(1) NOT NULL DEFAULT 1,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_programs_slug (slug),
  KEY idx_programs_published_sort (is_published, sort_order),
  KEY idx_programs_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE program_photos (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  program_id INT UNSIGNED NOT NULL,
  file_path  VARCHAR(255) NOT NULL,
  caption    VARCHAR(160) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_program_photos_program (program_id, sort_order),
  KEY idx_program_photos_created (created_at),
  CONSTRAINT fk_program_photos_program
    FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Catatan kolom `programs`:

- `category_label`: label di card dan halaman detail, contoh Pendidikan, Teknologi, Literasi, Kesehatan, Pemuda.
- `summary`: teks card di daftar program dan di preview Beranda.
- `tagline`: kalimat pembuka di bagian atas halaman detail.
- `body`: bagian "Tentang Program". Paragraf dipisah baris kosong. Tanda `**tebal**` dirender sebagai huruf tebal.
- `activities`: butir "Kegiatan dalam Program", satu per baris.
- `partners`: butir "Mitra Kolaborasi", satu per baris.
- `info_rows`: larik JSON berisi `{"label": "Wilayah", "value": "13 kampung, Kab. Manokwari"}` untuk kotak Informasi Program. Maksimal 6 baris.
- `stat1_*` dan `stat2_*`: dua angka kecil di card, contoh `700+` dengan label `Anak` dan `13` dengan label `Kampung`.
- `cover_image`: jalur relatif terhadap `public/uploads/`. Opsional.
- `is_featured`: tampil di preview tiga program di Beranda.

`program_photos.file_path` berisi jalur relatif terhadap `public/uploads/`. Versi kecil (thumbnail) memakai nama yang sama dengan akhiran `-thumb.webp`.

### 0006_create_posts_and_photos.sql

```sql
CREATE TABLE posts (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug         VARCHAR(190) NOT NULL,
  category     ENUM('ACTIVITY','UPDATE','MEDIA','AWARD') NOT NULL,
  title        VARCHAR(190) NOT NULL,
  excerpt      VARCHAR(300) NOT NULL,
  content      MEDIUMTEXT NOT NULL,
  cover_image  VARCHAR(255) NULL,
  author_name  VARCHAR(100) NOT NULL DEFAULT 'Tim Papua Future Project',
  meta_label   VARCHAR(120) NULL,
  source_name  VARCHAR(120) NULL,
  source_url   VARCHAR(500) NULL,
  published_at DATETIME NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_posts_slug (slug),
  KEY idx_posts_published (is_published, published_at),
  KEY idx_posts_category_published (category, is_published, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_photos (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id    INT UNSIGNED NOT NULL,
  file_path  VARCHAR(255) NOT NULL,
  caption    VARCHAR(160) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_post_photos_post (post_id, sort_order),
  CONSTRAINT fk_post_photos_post
    FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Nilai `category` dan labelnya di antarmuka:

| Nilai | Label | Parameter URL `?kategori=` |
| --- | --- | --- |
| `ACTIVITY` | Kegiatan Lapangan | `kegiatan` |
| `UPDATE` | Update Program | `update` |
| `MEDIA` | Liputan Media | `media` |
| `AWARD` | Penghargaan | `penghargaan` |

Catatan kolom `posts`:

- `content`: isi berita. Paragraf dipisah baris kosong. Tanda `**tebal**` dirender sebagai huruf tebal.
- `author_name`: dipakai di "Ditulis oleh".
- `meta_label`: untuk kategori `AWARD`, keterangan kecil setelah tahun, contoh `Bidang Pendidikan` atau `UNICEF Indonesia`. Card Penghargaan menampilkan tahun dari `published_at` lalu `meta_label`.
- `source_name` dan `source_url`: untuk kategori `MEDIA`, nama media dan tautan ke artikel asli. Daftar Liputan Media menampilkan `source_name`, bulan dan tahun, judul, dan tautan keluar.

### 0007_create_faqs_and_contact_messages.sql

```sql
CREATE TABLE faqs (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  question     VARCHAR(300) NOT NULL,
  answer       TEXT NOT NULL,
  sort_order   INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_faqs_published_sort (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_messages (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  whatsapp   VARCHAR(20) NULL,
  message    TEXT NOT NULL,
  is_read    TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contact_messages_read_created (is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 0008_create_applications.sql

```sql
CREATE TABLE applications (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  batch_id          INT UNSIGNED NOT NULL,
  file_token        CHAR(32) NOT NULL,
  full_name         VARCHAR(100) NOT NULL,
  birth_date        DATE NOT NULL,
  email             VARCHAR(190) NOT NULL,
  whatsapp          VARCHAR(20) NOT NULL,
  domicile          VARCHAR(120) NOT NULL,
  occupation_status ENUM('STUDENT','FRESH_GRADUATE','EMPLOYEE','ENTREPRENEUR','OTHER') NOT NULL,
  university        VARCHAR(120) NULL,
  duration          ENUM('TWO_WEEKS','ONE_MONTH','FLEXIBLE') NOT NULL,
  interest_areas    JSON NOT NULL,
  referral_source   ENUM('INSTAGRAM','FRIEND','ONLINE_ARTICLE','EVENT','OTHER') NOT NULL,
  motivation        TEXT NOT NULL,
  special_skills    TEXT NULL,
  photo_path        VARCHAR(255) NOT NULL,
  cv_path           VARCHAR(255) NOT NULL,
  status            ENUM('NEW','REVIEWED','ACCEPTED','REJECTED') NOT NULL DEFAULT 'NEW',
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_applications_file_token (file_token),
  UNIQUE KEY uq_applications_batch_email (batch_id, email),
  KEY idx_applications_batch_status (batch_id, status),
  KEY idx_applications_created (created_at),
  CONSTRAINT fk_applications_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Catatan kolom `applications`:

- `file_token`: 32 karakter heksadesimal acak yang dibuat server sebelum file ditulis. Dipakai sebagai nama folder file pendaftar, sehingga jalur file tidak bisa ditebak dan tidak bergantung pada `id` yang baru ada setelah `INSERT`.
- `photo_path` dan `cv_path`: jalur relatif terhadap `STORAGE_DIR`, contoh `applications/{file_token}/photo.webp` dan `applications/{file_token}/cv.pdf`.
- `university`: kosong untuk pendaftar non-mahasiswa. Wajib bila `occupation_status` adalah `STUDENT` atau `FRESH_GRADUATE`.
- `interest_areas`: larik JSON berisi satu atau lebih dari `LITERACY`, `HEALTH`, `CREATIVE`, `MEDIA`, `OTHER`.
- Tipe pendaftar (Intern atau Relawan) tidak disimpan di sini. Tipe diambil dari `batches.batch_type`.

## 4. Pemetaan nilai enum ke label antarmuka

| Kolom | Nilai dan label |
| --- | --- |
| `batches.batch_type` | `INTERN` Intern, `VOLUNTEER` Relawan |
| `team_members.member_group` | `CORE` Tim Penggerak, `INTERN` Internship, `VOLUNTEER` Volunteer |
| `applications.status` | `NEW` Baru, `REVIEWED` Ditinjau, `ACCEPTED` Diterima, `REJECTED` Ditolak |
| `applications.occupation_status` | `STUDENT` Mahasiswa, `FRESH_GRADUATE` Fresh Graduate, `EMPLOYEE` Karyawan atau Profesional, `ENTREPRENEUR` Wiraswasta, `OTHER` Lainnya |
| `applications.duration` | `TWO_WEEKS` 2 minggu, `ONE_MONTH` 1 bulan, `FLEXIBLE` Fleksibel |
| `applications.referral_source` | `INSTAGRAM` Instagram @papuafutureproject, `FRIEND` Diajak teman atau kenalan, `ONLINE_ARTICLE` Artikel atau berita online, `EVENT` Event, seminar, atau pameran, `OTHER` Lainnya |
| `applications.interest_areas` (isi larik) | `LITERACY` Literasi dan Bimbingan Belajar, `HEALTH` Edukasi Kesehatan dan Gizi, `CREATIVE` Kegiatan Kreatif, `MEDIA` Dokumentasi dan Media, `OTHER` Lainnya atau sesuai kebutuhan tim |

Pemetaan ini ditulis sekali sebagai konstanta di kelas `App\Support\Labels` dan dipakai oleh View, Validator, dan ekspor.

## 5. Aturan integritas data

Beberapa aturan tidak bisa dijaga MySQL sendiri, jadi dijaga di Validator, Model, dan Service. Jangan menghapus pemeriksaan ini dengan alasan "sudah ada di database".

1. `member_group = 'CORE'` wajib punya `batch_id` kosong. `INTERN` dan `VOLUNTEER` wajib punya `batch_id`, dan `batches.batch_type` harus sama dengan `member_group`. Dijaga di `TeamMemberValidator` dan Model `TeamMember`. Tidak dibuat sebagai `CHECK` karena MySQL membatasi `CHECK` pada kolom yang dipakai kunci asing, dan aturan ini melibatkan tabel lain.
2. Hanya satu `team_members` yang boleh punya `is_founder = 1`. Model menurunkan flag anggota lain dalam transaksi yang sama saat flag baru dinyalakan.
3. `batches` tidak bisa dihapus bila masih punya `team_members` atau `applications` (`ON DELETE RESTRICT`). Controller menghitung keduanya lebih dulu, lalu menampilkan pesan yang menyebut jumlahnya dan menawarkan "Tutup batch". Galat kunci asing dari database (kode 23000) hanya jaring pengaman.
4. `applications.email` diubah ke huruf kecil dan dipangkas spasinya sebelum disimpan atau dibandingkan. Keunikan dijaga oleh `uq_applications_batch_email`. Controller menangkap galat duplikat (kode 1062) dan menampilkan pesan yang jelas.
5. `applications` hanya boleh dibuat bila `batches.is_open = 1` pada saat penyimpanan. Pemeriksaan memakai `SELECT ... FOR UPDATE` pada baris batch di dalam transaksi yang sama dengan `INSERT`, sehingga penutupan batch di tengah proses tidak terlewat.
6. Saat batch dibuka, `opened_at` diisi dan `closed_at` dikosongkan. Saat ditutup, `closed_at` diisi.
7. `about_content` hanya punya satu baris. Tidak ada aksi buat atau hapus, hanya ubah.
8. Menghapus `programs` atau `posts` menghapus baris foto lewat `CASCADE`, tetapi file fisiknya harus dihapus lebih dulu oleh `GalleryService`. Hal yang sama berlaku untuk `team_members.photo_path` dan `cover_image`.
9. Satu `programs` atau `posts` maksimal punya 30 foto. Dijaga di `GalleryService` sebelum `INSERT`.
10. `slug` dibuat dari judul dengan huruf kecil dan tanda hubung. Bila bentrok, tambahkan angka di belakangnya. Slug tidak berubah saat judul diubah, supaya tautan lama tetap berlaku.
11. `info_rows` di `programs` divalidasi sebagai larik berisi maksimal 6 objek dengan `label` dan `value` tidak kosong.
12. Baris `rate_limits` yang lebih tua dari satu hari dihapus oleh `bin/cleanup-rate-limits.php` yang dijalankan lewat cron.

## 6. Migrasi

1. `bin/migrate.php` menjalankan file di `database/migrations/` yang belum tercatat, urut nomor, lalu mencatatnya di tabel `schema_migrations` (`migration` sebagai kunci utama, `applied_at`). Tabel ini dibuat oleh skrip itu sendiri.
2. Migrasi yang sudah dijalankan tidak boleh diedit. Perubahan baru ditulis sebagai file bernomor berikutnya.
3. Tulis migrasi kecil dengan satu tujuan per file. Perhatikan bahwa `CREATE TABLE` di MySQL melakukan commit otomatis, jadi transaksi tidak bisa membatalkannya.

## 7. Penyimpanan file

| Jenis | Lokasi | Akses |
| --- | --- | --- |
| Foto dan CV pendaftar | `STORAGE_DIR/applications/{file_token}/photo.webp` dan `cv.pdf` | Privat. `STORAGE_DIR` berada di luar folder `public/`. Hanya lewat controller yang memeriksa sesi admin. |
| Foto galeri program dan berita, gambar sampul | `public/uploads/programs/{program_id}/` dan `public/uploads/posts/{post_id}/` | Publik. |
| Foto Tim Penggerak, intern, relawan | `public/uploads/team/` | Publik. |

Nama file di semua jalur dibuat sistem (acak). Nama file asli dari pengguna tidak dipakai. Eksekusi PHP di `public/uploads/` diblokir (lihat [ARCHITECTURE.md](ARCHITECTURE.md)).

## 8. Akun admin awal dan konten

Akun admin awal dibuat secara eksplisit lewat `php bin/create-admin.php <email> <nama>`. Password dibaca hanya dari variabel lingkungan `PFP_ADMIN_PASSWORD`, harus minimal 12 karakter, lalu disimpan menggunakan `password_hash()`. Tidak ada password default atau akun admin seed. Migrasi 0003 dan 0004 serta `database/pfp.sql` mengisi konten Tentang Kami dan empat profil Tim Penggerak awal; migrasi 0009 serta `database/pfp.sql` menambahkan lima contoh awal program yang dapat diperbarui atau dihapus lewat dashboard.

| Seed konten yang direncanakan | Isi |
| --- | --- |
| `admin.php` | Satu akun admin dari `SEED_ADMIN_EMAIL` dan `SEED_ADMIN_PASSWORD`, disimpan dengan `password_hash()`. |
| `about.php` | Satu baris `about_content`: cerita dari halaman Tentang Kami, visi, dan empat butir misi. |
| `team.php` | Empat anggota `CORE`: Bhrisco Jordy Dudi Padatu (Founder dan Ketua, `is_founder = 1`, `long_bio` dari profil di Beranda), Koordinator Program, Koordinator Relawan, Koordinator Kesehatan. |
| `programs.php` | Lima program dari halaman Program beserta isi tiap halaman detailnya. Tiga program ditandai unggulan: Bimbingan Belajar dan Literasi, Kesehatan dan Imunisasi, Pemberdayaan Pemuda Lokal. |
| `posts.php` | Enam berita dari halaman Berita, tiga berita Penghargaan (SATU Indonesia Awards 2022, Mitra NGO Potensial UNICEF, Kolaborasi Kemenkes RI), dan dua Liputan Media tambahan (Young On Top 2024, IndoRelawan 2023) dengan tautan sumbernya. |
| `faqs.php` | Delapan FAQ dari halaman Kontak. |

Migrasi 0009 menambahkan lima data awal program. Migrasi 0010 menambahkan berita, penghargaan, liputan media, dan delapan FAQ dari konten awal situs. Baris seed ini dapat dikelola atau dihapus melalui panel admin. Batch, pendaftar, pesan kontak, dan foto dokumentasi dibuat melalui fitur aplikasi.
