# ERD: Entity Relationship Diagram

Gambaran semua tabel MySQL dan hubungan antar tabel. DDL lengkap ada di [SCHEMA.md](SCHEMA.md). Kebutuhan produk ada di [PRD.md](PRD.md).

## 1. Diagram

Diagram menampilkan kolom kunci saja. Daftar kolom lengkap ada di [SCHEMA.md](SCHEMA.md).

```mermaid
erDiagram
  programs ||--o{ program_photos : "punya foto dokumentasi"
  posts ||--o{ post_photos : "punya foto dokumentasi"
  batches ||--o{ team_members : "mengelompokkan"
  batches ||--o{ applications : "menerima"
  applications ||--o{ application_answers : "menyimpan jawaban"
  application_questions o|--o{ application_answers : "label pertanyaan"

  programs {
    int id PK
    varchar slug UK
    varchar category_label
    varchar title
    tinyint is_featured
    tinyint is_published
  }
  program_photos {
    int id PK
    int program_id FK
    varchar file_path
    varchar caption
    int sort_order
  }
  posts {
    int id PK
    varchar slug UK
    enum category "ACTIVITY, UPDATE, MEDIA, AWARD"
    varchar title
    datetime published_at
    tinyint is_published
  }
  post_photos {
    int id PK
    int post_id FK
    varchar file_path
    varchar caption
    int sort_order
  }
  batches {
    int id PK
    enum batch_type "INTERN atau VOLUNTEER"
    varchar name
    varchar google_form_url "Link layanan pendaftaran eksternal; NULL bila belum diatur"
    tinyint is_open
  }
  team_members {
    int id PK
    enum member_group "CORE, INTERN, VOLUNTEER"
    int batch_id FK "NULL bila CORE"
    varchar name
    tinyint is_founder
    varchar photo_path "foto profil/Core Team"
    varchar home_photo_path "foto founder di Beranda; fallback ke photo_path"
  }
  applications {
    int id PK
    int batch_id FK "NULL setelah batch dihapus; hanya data arsip"
    char file_token UK
    varchar full_name "NULL jika tidak diaktifkan/diisi"
    date birth_date "NULL jika tidak diaktifkan/diisi"
    varchar email "NULL jika tidak diaktifkan/diisi"
    varchar whatsapp "NULL jika tidak diaktifkan/diisi"
    varchar photo_path "NULL jika upload foto opsional/tidak aktif"
    varchar cv_path "NULL jika upload CV opsional/tidak aktif"
    enum status "NEW, REVIEWED, ACCEPTED, REJECTED"
  }
  application_form_settings {
    enum application_type PK "INTERN atau VOLUNTEER"
    tinyint is_active
    varchar google_form_url "URL pendaftaran HTTP/HTTPS dari layanan apa pun; NULL bila belum disetel"
  }
  application_questions {
    int id PK
    enum application_type "INTERN atau VOLUNTEER"
    varchar field_key "NULL untuk pertanyaan tambahan"
    tinyint is_system
    varchar question
    enum field_type "TEXT, TEXTAREA, RADIO, CHECKBOX, SELECT, EMAIL, TEL, DATE, URL, IMAGE, PDF"
    tinyint is_required
    tinyint is_active
    int sort_order
  }
  application_answers {
    bigint id PK
    int application_id FK
    int question_id FK "NULL bila pertanyaan dihapus"
    varchar question_label "snapshot"
    text answer
  }
  about_content {
    tinyint id PK "selalu 1"
    mediumtext story
    text vision
    text mission
  }
  faqs {
    int id PK
    varchar question
    int sort_order
    tinyint is_published
  }
  contact_messages {
    int id PK
    varchar full_name
    varchar email
    tinyint is_read
  }
  admin_users {
    int id PK
    varchar email UK
    varchar password_hash
  }
  rate_limits {
    bigint id PK
    varchar bucket
    varchar identifier
  }
```

## 2. Notasi teks

Notasi ini membaca "satu induk punya banyak anak". Tanda `1` berarti tepat satu, `0..N` berarti nol atau lebih, dan `0..1` berarti kosong atau satu.

```
programs      1 ----< 0..N  program_photos     (program_photos.program_id)
posts         1 ----< 0..N  post_photos        (post_photos.post_id)
batches       1 ----< 0..N  team_members       (team_members.batch_id, 0..1 di sisi anak)
batches       1 ----< 0..N  applications       (applications.batch_id)
applications  1 ----< 0..N  application_answers (application_answers.application_id)
application_questions 0..1 ----< 0..N application_answers (application_answers.question_id)

Tabel berdiri sendiri (tanpa relasi):
  admin_users, about_content, faqs, contact_messages, rate_limits,
  application_form_settings (arsip)
```

## 3. Penjelasan relasi

| Relasi | Kardinalitas | Kunci asing | Saat induk dihapus | Aturan |
| --- | --- | --- | --- | --- |
| `programs` ke `program_photos` | Satu program punya nol sampai banyak foto. Satu foto milik satu program. | `program_photos.program_id` | `CASCADE` (baris foto ikut terhapus) | File fisik dihapus lebih dulu oleh `GalleryService`. Maksimal 30 foto per program. |
| `posts` ke `post_photos` | Satu berita punya nol sampai banyak foto. Satu foto milik satu berita. | `post_photos.post_id` | `CASCADE` | Sama seperti program. |
| `batches` ke `team_members` | Satu batch punya nol sampai banyak anggota. Satu anggota milik nol atau satu batch. | `team_members.batch_id` (boleh `NULL`) | `RESTRICT` (ditolak bila masih ada anggota) | Anggota grup `CORE` tidak punya batch. Anggota `INTERN` dan `VOLUNTEER` wajib punya batch dengan tipe yang cocok. Dijaga di Validator dan Model. |
| `batches` ke `applications` | Relasi arsip formulir lokal versi lama; layanan formulir eksternal tidak menulis baris baru ke `applications`. | `applications.batch_id` (nullable) | `SET NULL` | Data lama dipertahankan tanpa menghalangi penghapusan batch yang tidak memiliki anggota tim; tidak dikelola melalui panel admin aktif. |
| `applications` ke `application_answers` | Satu pendaftar punya nol sampai banyak jawaban formulir. | `application_answers.application_id` | `CASCADE` | Menyimpan snapshot jawaban seluruh field aktif, termasuk field bawaan dan jawaban opsional yang kosong. Jawaban ikut dihapus jika data pendaftar dihapus. |
| `application_questions` ke `application_answers` | Satu pertanyaan dapat memiliki banyak jawaban. Satu jawaban terhubung ke nol atau satu definisi pertanyaan. | `application_answers.question_id` (nullable) | `SET NULL` | Teks pertanyaan disalin ke `question_label`, jadi jawaban lama tetap bisa dibaca setelah definisi pertanyaan dihapus. |

## 4. Ringkasan tabel

| Tabel | Fungsi | Dipakai di |
| --- | --- | --- |
| `admin_users` | Akun admin. | Login admin. |
| `rate_limits` | Pencatat percobaan untuk pembatasan laju. | Login dan form kontak. |
| `about_content` | Satu baris berisi cerita, visi, dan misi. | Tentang Kami. |
| `team_members` | Tim Penggerak, intern, dan relawan. Founder ditandai `is_founder`; `photo_path` dan `home_photo_path` dapat memakai foto berbeda. | Tentang Kami dan profil founder di Beranda. |
| `batches` | Batch Internship atau Relawan, URL formulir eksternal (disimpan pada kolom kompatibilitas `google_form_url`), dan status buka/tutup. | Tentang Kami, Bergabung, admin Batch. |
| `programs` | Program beserta semua isian halaman detail. | Beranda, Program, detail program. |
| `program_photos` | Foto dokumentasi per program. | Detail program, "Dari Lapangan". |
| `posts` | Berita dan kegiatan dalam empat kategori. | Beranda, Berita, detail berita. |
| `post_photos` | Foto dokumentasi per berita. | Detail berita. |
| `faqs` | Pertanyaan umum. | Kontak. |
| `contact_messages` | Pesan dari form kontak. | Kontak, admin Pesan masuk. |
| `applications` | Data dan berkas pendaftar dari formulir website versi sebelumnya. Form baru berada di layanan eksternal dan tidak menambahkan baris ke tabel ini. | Arsip/kompatibilitas database saja; tidak ada halaman admin aktif. |
| `application_form_settings` | Pengaturan formulir per jenis dari versi lama. Pengaturan baru tersimpan langsung di tiap batch. | Arsip/kompatibilitas database saja. |
| `application_questions` | Definisi pertanyaan formulir lokal versi sebelumnya. | Arsip/kompatibilitas database saja. |
| `application_answers` | Jawaban pendaftar dari formulir website versi sebelumnya. | Arsip/kompatibilitas database saja. |

Tabel pendukung `schema_migrations` dibuat oleh `bin/migrate.php` dan tidak termasuk diagram.

## 5. Catatan desain

1. **Pendaftaran baru memakai layanan formulir eksternal pilihan Admin.** Situs menyimpan URL dan status buka/tutup di `batches`; masing-masing batch dapat memakai tautan formulir tersendiri.
2. **Penghargaan dan Liputan Media adalah kategori di `posts`,** bukan tabel terpisah. Beranda mengambil tiga berita Penghargaan terbaru, dan halaman Berita mengelompokkannya di bagian "Diakui dan Diliput".
3. **Profil founder adalah satu baris `team_members`** dengan `is_founder = 1`. Teks profil panjang untuk Beranda disimpan di `long_bio`. Foto Core Team memakai `photo_path`; foto Beranda memakai `home_photo_path` dan kembali ke `photo_path` bila belum diisi.
4. **`application_form_settings` hanya arsip.** Pengaturan aktif tidak lagi bergantung pada tabel ini. Formulir dan jawaban baru dikelola di layanan yang ditautkan; status buka/tutup website bersifat per batch. Konten statis situs tetap berada di `config/site.php` sesuai PRD A4.
5. **Daftar sederhana disimpan sebagai teks per baris** (`activities`, `partners` di `programs`, dan `mission` di `about_content`). Pasangan label dan nilai di Informasi Program disimpan sebagai JSON (`info_rows`).
6. **`applications.file_token`** adalah nama folder file pendaftar yang acak, jadi jalur file tidak bisa ditebak dari `id`.
7. **Tombol Daftar publik memerlukan dua kondisi:** URL pendaftaran web pada batch harus valid dan tersedia, serta `batches.is_open = 1`.
8. **Aturan yang tidak bisa dijaga MySQL** (kecocokan `member_group` dengan `batch_type`, hanya satu founder, dan kesesuaian pertanyaan dengan jenis batch) dijaga di Validator dan Model. Lihat [SCHEMA.md](SCHEMA.md) bagian 5.
