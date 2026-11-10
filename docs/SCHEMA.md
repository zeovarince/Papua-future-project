# SCHEMA: DDL MySQL

DDL lengkap semua tabel, siap dijalankan di MySQL 8 atau MariaDB 10.6 ke atas. Untuk impor lewat phpMyAdmin, gunakan satu file [`../database/pfp.sql`](../database/pfp.sql). File ini membuat skema baru atau memperbarui kolom form PFP versi sebelumnya tanpa menghapus data yang ada. Diagram dan penjelasan relasi ada di [ERD.md](ERD.md). Kebutuhan produk ada di [PRD.md](PRD.md).

## 1. Aturan penamaan

1. Tabel memakai snake_case dan bentuk jamak (`team_members`), kecuali `about_content` yang memang satu set konten.
2. Kolom memakai snake_case. Kunci asing memakai `{tabel_tunggal}_id`.
3. Hindari kata yang dicadangkan SQL. Karena itu kolomnya bernama `member_group` (bukan `group`), `batch_type`, dan `bucket`.
4. Boolean memakai `TINYINT(1)` dengan awalan `is_`.
5. Semua tabel memakai `ENGINE=InnoDB`, `utf8mb4`, dan collation `utf8mb4_unicode_ci`.
6. Semua waktu disimpan dalam WIT (UTC+9). Koneksi PDO menjalankan `SET time_zone = '+09:00'` (lihat [ARCHITECTURE.md](ARCHITECTURE.md)).
7. Kolom `JSON` dibaca PDO sebagai string. Model yang mengubahnya dengan `json_decode` dan `json_encode`. Di MariaDB, `JSON` adalah alias `LONGTEXT` dengan pemeriksaan `JSON_VALID`.

## 2. Urutan migrasi

File di `database/migrations/` adalah riwayat internal untuk `bin/migrate.php`. Untuk pengaturan database melalui phpMyAdmin, gunakan satu file `database/pfp.sql`; tidak perlu mengimpor berkas migrasi satu per satu.

| File | Isi | Dipakai di step |
| --- | --- | --- |
| `0001_create_admin_users.sql` | `admin_users` | 3 |
| `0002_create_rate_limits.sql` | `rate_limits` | 3 |
| `0003_create_about_content.sql` | `about_content` | 4 |
| `0004_create_batches_and_team_members.sql` | `batches`, `team_members` | 4 dan 8 |
| `0005_create_programs_and_photos.sql` | `programs`, `program_photos` | 5 |
| `0006_create_posts_and_photos.sql` | `posts`, `post_photos` | 6 |
| `0007_create_faqs_and_contact_messages.sql` | `faqs`, `contact_messages` | 7 |
| `0008_create_applications.sql` | `applications` (skema formulir lokal versi lama; arsip saja) | Arsip |
| `0009_seed_initial_programs.sql` | Lima data awal program untuk instalasi migrasi | 5 |
| `0010_seed_news_and_faqs.sql` | Konten awal berita, penghargaan, liputan media, dan FAQ | 6 dan 7 |
| `0011_create_application_forms.sql` | Tabel formulir lokal versi lama (arsip saja) | Arsip |
| `0012_expand_application_questions.sql` | Melengkapi skema pertanyaan formulir lokal versi lama (arsip saja) | Arsip |
| `0015_add_google_form_url.sql` | URL Google Form per jenis versi lama; dipertahankan untuk migrasi data | Arsip |
| `0016_google_forms_per_batch.sql` | Menyimpan URL dan status buka/tutup per batch; URL lama dipindahkan ke batch terbaru per jenis | Batch |

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
  google_form_url VARCHAR(500) NULL,
  is_open    TINYINT(1) NOT NULL DEFAULT 0,
  opened_at  DATETIME NULL,
  closed_at  DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_batches_type_name (batch_type, name),
  KEY idx_batches_type_open (batch_type, is_open),
  KEY idx_batches_type_id (batch_type, id),
  KEY idx_batches_created (created_at)
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
  home_photo_path VARCHAR(255) NULL,
  is_founder   TINYINT(1) NOT NULL DEFAULT 0,
  sort_order   INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_team_public_order (member_group, is_published, batch_id, sort_order, name),
  KEY idx_team_batch (batch_id),
  CONSTRAINT fk_team_members_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Catatan kolom `team_members`:

- `role`: jabatan untuk grup `CORE` (contoh "Founder & Ketua"), peran atau bidang untuk intern dan relawan.
- `bio`: deskripsi singkat untuk card.
- `long_bio`: profil panjang, dipakai untuk profil founder di Beranda. Diisi hanya untuk founder.
- `photo_path`: foto profil anggota; pada founder dipakai di Core Team/Tentang Kami.
- `home_photo_path`: foto founder khusus Beranda. Jika `NULL`, Beranda memakai `photo_path`.
- `is_founder`: menandai satu profil founder yang konten dan fotonya dipakai di Beranda.

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
  KEY idx_programs_public_order (is_published, sort_order, title),
  KEY idx_programs_featured_order (is_published, is_featured, sort_order, title)
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

### Arsip: `applications` dari formulir website versi lama

Tabel berikut hanya menampung data lama. Aplikasi saat ini mengarahkan pendaftar ke layanan formulir eksternal dari link batch dan tidak membaca atau menulis tabel ini.

```sql
CREATE TABLE applications (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  batch_id          INT UNSIGNED NULL,
  file_token        CHAR(32) NOT NULL,
  full_name         VARCHAR(100) NULL,
  birth_date        DATE NULL,
  email             VARCHAR(190) NULL,
  whatsapp          VARCHAR(20) NULL,
  domicile          VARCHAR(120) NULL,
  occupation_status ENUM('STUDENT','FRESH_GRADUATE','EMPLOYEE','ENTREPRENEUR','OTHER') NULL,
  university        VARCHAR(120) NULL,
  duration          ENUM('TWO_WEEKS','ONE_MONTH','FLEXIBLE') NULL,
  interest_areas    JSON NULL,
  referral_source   ENUM('INSTAGRAM','FRIEND','ONLINE_ARTICLE','EVENT','OTHER') NULL,
  motivation        TEXT NULL,
  special_skills    TEXT NULL,
  photo_path        VARCHAR(255) NULL,
  cv_path           VARCHAR(255) NULL,
  status            ENUM('NEW','REVIEWED','ACCEPTED','REJECTED') NOT NULL DEFAULT 'NEW',
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_applications_file_token (file_token),
  UNIQUE KEY uq_applications_batch_email (batch_id, email),
  KEY idx_applications_batch_status_created (batch_id, status, created_at),
  KEY idx_applications_created (created_at),
  KEY idx_applications_status_created (status, created_at),
  CONSTRAINT fk_applications_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Kolom pribadi dan file di tabel ini berasal dari formulir website lama dan hanya dipertahankan sebagai arsip. Tidak ada halaman admin untuk menampilkan atau mengekspornya. `batch_id` boleh `NULL`; jika batch tanpa anggota tim dihapus, data arsip tetap tersimpan tanpa relasi batch.

### 0011_create_application_forms.sql

```sql
CREATE TABLE application_form_settings (
  application_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  is_active        TINYINT(1) NOT NULL DEFAULT 0,
  google_form_url  VARCHAR(500) NULL,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (application_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO application_form_settings (application_type, is_active)
SELECT 'INTERN', EXISTS(SELECT 1 FROM batches WHERE batch_type = 'INTERN' AND is_open = 1)
UNION ALL
SELECT 'VOLUNTEER', EXISTS(SELECT 1 FROM batches WHERE batch_type = 'VOLUNTEER' AND is_open = 1);

CREATE TABLE application_questions (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  field_key        VARCHAR(40) NULL,
  is_system        TINYINT(1) NOT NULL DEFAULT 0,
  question         VARCHAR(250) NOT NULL,
  field_type       ENUM('TEXT','TEXTAREA','RADIO','CHECKBOX','SELECT','FILE','EMAIL','TEL','DATE','URL','IMAGE','PDF') NOT NULL DEFAULT 'TEXTAREA',
  is_required      TINYINT(1) NOT NULL DEFAULT 1,
  is_active        TINYINT(1) NOT NULL DEFAULT 1,
  sort_order       INT NOT NULL DEFAULT 0,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_application_questions_type_field (application_type, field_key),
  KEY idx_application_questions_type_active_order (application_type, is_active, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE application_answers (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id  INT UNSIGNED NOT NULL,
  question_id     INT UNSIGNED NULL,
  question_label  VARCHAR(250) NOT NULL,
  answer          TEXT NOT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_application_answers_question (application_id, question_id),
  KEY idx_application_answers_question (question_id),
  CONSTRAINT fk_application_answers_application
    FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE,
  CONSTRAINT fk_application_answers_question
    FOREIGN KEY (question_id) REFERENCES application_questions (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Pendaftaran publik sekarang diatur langsung per baris `batches`: kolom kompatibilitas `google_form_url` menyimpan tautan pendaftaran dari layanan apa pun, sedangkan `is_open` menjadi sakelar buka/tutup. Admin mengelola nama dan tautan pada halaman Ubah Batch, lalu mengubah status dari daftar Batch. Jika database lama memiliki tautan di `application_form_settings`, `pfp.sql` memindahkannya ke batch terbaru dengan tipe yang sama. Tabel `application_form_settings`, `application_questions`, `application_answers`, dan `applications` masih disimpan sebagai arsip kompatibilitas; halaman admin pendaftar lama dan pengaturan pertanyaan tidak lagi digunakan.

`database/pfp.sql` adalah file SQL kanonis untuk membuat database baru atau memperbarui skema PFP lama. File itu menambahkan `batches.google_form_url` bila belum ada, memindahkan tautan pendaftaran lama ke batch yang sesuai, dan mempertahankan data yang sudah tersimpan.

## 5. Strategi indeks untuk query

Indeks disusun dari kolom yang dipakai untuk filter, join, dan pengurutan oleh Model. Kolom paling kiri pada indeks gabungan mengikuti kolom `WHERE` yang paling umum.

| Indeks | Query yang dibantu |
| --- | --- |
| `batches(batch_type, is_open)` | Batch terbuka per jenis, seperti pendaftaran Internship atau Relawan. |
| `batches(batch_type, id)` dan `batches(created_at)` | Daftar batch per jenis dan daftar admin terbaru. |
| `team_members(member_group, is_published, batch_id, sort_order, name)` | Anggota publik per kelompok, diurutkan per batch dan urutan tampil. |
| `programs(is_published, sort_order, title)` | Daftar program publik. |
| `programs(is_published, is_featured, sort_order, title)` | Preview program unggulan di Beranda. |
| `posts(is_published, published_at)` | Daftar berita terbaru, berita terbit, dan jumlah berita terbit. |
| `posts(category, is_published, published_at)` | Berita terkait dan filter daftar per kategori. |
| Indeks pada tabel `applications`, `application_form_settings`, `application_questions`, dan `application_answers` | Struktur lama yang dipertahankan untuk arsip/relasi historis; tidak dipakai halaman pendaftaran aktif. |
| `program_photos(program_id, sort_order)` dan `post_photos(post_id, sort_order)` | Foto dokumentasi per konten sesuai urutan. |

Kolom isi panjang `MEDIUMTEXT` tidak ikut diambil pada query kartu program/berita dan daftar admin.

## 6. Pemetaan nilai enum ke label antarmuka

| Kolom | Nilai dan label |
| --- | --- |
| `batches.batch_type` | `INTERN` Intern, `VOLUNTEER` Relawan |
| `team_members.member_group` | `CORE` Tim Penggerak, `INTERN` Internship, `VOLUNTEER` Volunteer |
| `applications.*` | Nilai enum di tabel ini hanya menjelaskan arsip jawaban formulir lokal versi lama; bukan daftar pilihan di fitur aktif. |

Label yang masih dipakai di aplikasi ditulis sekali sebagai konstanta di kelas `App\Support\Labels`.

## 7. Aturan integritas data

Beberapa aturan tidak bisa dijaga MySQL sendiri, jadi dijaga di Validator, Model, dan Service. Jangan menghapus pemeriksaan ini dengan alasan "sudah ada di database".

1. `member_group = 'CORE'` wajib punya `batch_id` kosong. `INTERN` dan `VOLUNTEER` wajib punya `batch_id`, dan `batches.batch_type` harus sama dengan `member_group`. Dijaga di `TeamMemberValidator` dan Model `TeamMember`. Tidak dibuat sebagai `CHECK` karena MySQL membatasi `CHECK` pada kolom yang dipakai kunci asing, dan aturan ini melibatkan tabel lain.
2. Hanya satu `team_members` yang boleh punya `is_founder = 1`. Model menurunkan flag anggota lain dalam transaksi yang sama saat flag baru dinyalakan.
3. Batch tidak bisa dihapus bila masih punya `team_members`. Jika ada arsip pendaftar formulir website lama, `applications.batch_id` menjadi `NULL` (`ON DELETE SET NULL`), sehingga arsip tetap tersimpan tanpa menghalangi penghapusan batch yang tidak memiliki anggota.
4. Link pendaftaran batch menerima URL absolut valid dengan skema `http` atau `https` dari domain apa pun. Username/password di dalam URL tidak diterima. Batch hanya dapat dibuka bila URL valid sudah disimpan.
5. Menghapus link pendaftaran menutup batch tersebut. Status buka/tutup tiap batch berdiri sendiri.
6. Tabel `applications`, `application_form_settings`, `application_questions`, dan `application_answers` hanya dipertahankan untuk arsip versi sebelumnya; tidak ada alur pendaftaran baru yang menulis ke tabel tersebut.
7. `about_content` hanya punya satu baris. Tidak ada aksi buat atau hapus, hanya ubah.
8. Menghapus `programs` atau `posts` menghapus baris foto lewat `CASCADE`, tetapi file fisiknya harus dihapus lebih dulu oleh `GalleryService`. Hal yang sama berlaku untuk `team_members.photo_path` dan `cover_image`.
9. Satu `programs` atau `posts` maksimal punya 30 foto. Dijaga di `GalleryService` sebelum `INSERT`.
10. `slug` dibuat dari judul dengan huruf kecil dan tanda hubung. Bila bentrok, tambahkan angka di belakangnya. Slug tidak berubah saat judul diubah, supaya tautan lama tetap berlaku.
11. `info_rows` di `programs` divalidasi sebagai larik berisi maksimal 6 objek dengan `label` dan `value` tidak kosong.
12. Baris `rate_limits` yang lebih tua dari satu hari dihapus oleh `bin/cleanup-rate-limits.php` yang dijalankan lewat cron.

## 8. Migrasi

1. `bin/migrate.php` menjalankan file di `database/migrations/` yang belum tercatat, urut nomor, lalu mencatatnya di tabel `schema_migrations` (`migration` sebagai kunci utama, `applied_at`). Tabel ini dibuat oleh skrip itu sendiri.
2. Migrasi yang sudah dijalankan tidak boleh diedit. Perubahan baru ditulis sebagai file bernomor berikutnya.
3. Tulis migrasi kecil dengan satu tujuan per file. Perhatikan bahwa `CREATE TABLE` di MySQL melakukan commit otomatis, jadi transaksi tidak bisa membatalkannya.

## 9. Penyimpanan file

| Jenis | Lokasi | Akses |
| --- | --- | --- |
| Berkas formulir website lama | Jika masih ada di `STORAGE_DIR/applications/`, tetap privat dan tidak disajikan oleh halaman admin aktif. Jangan hapus saat deploy tanpa keputusan retensi data. |
| Foto galeri program dan berita, gambar sampul | `public/uploads/programs/{program_id}/` dan `public/uploads/posts/{post_id}/` | Publik. |
| Foto Tim Penggerak, intern, relawan | `public/uploads/team/` | Publik. |

Nama file di semua jalur dibuat sistem (acak). Nama file asli dari pengguna tidak dipakai. Eksekusi PHP di `public/uploads/` diblokir (lihat [ARCHITECTURE.md](ARCHITECTURE.md)).

## 10. Akun admin awal dan konten

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
