-- Papua Future Project: complete MySQL schema
-- Import this file into an already-created, empty database using phpMyAdmin.
-- Compatible with MySQL 8+ and MariaDB 10.6+.
-- Source of truth: docs/SCHEMA.md and docs/ERD.md.
-- The schema_migrations rows mark migrations 0001 through 0011 as applied so bin/migrate.php
-- will not try to recreate these tables after this complete schema is imported.

SET NAMES utf8mb4;
SET time_zone = '+09:00';

CREATE TABLE schema_migrations (
  migration VARCHAR(190) NOT NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (migration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY idx_applications_batch_status_created (batch_id, status, created_at),
  KEY idx_applications_created (created_at),
  KEY idx_applications_status_created (status, created_at),
  CONSTRAINT fk_applications_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE application_form_settings (
  application_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  is_active        TINYINT(1) NOT NULL DEFAULT 0,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (application_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO application_form_settings (application_type, is_active) VALUES
  ('INTERN', 0), ('VOLUNTEER', 0);

CREATE TABLE application_questions (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  question         VARCHAR(250) NOT NULL,
  field_type       ENUM('TEXT','TEXTAREA') NOT NULL DEFAULT 'TEXTAREA',
  is_required      TINYINT(1) NOT NULL DEFAULT 1,
  is_active        TINYINT(1) NOT NULL DEFAULT 1,
  sort_order       INT NOT NULL DEFAULT 0,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
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

INSERT INTO about_content (id, story, vision, mission) VALUES (
  1,
  'Pada 2020, Bhrisco Jordy Dudi Padatu turun langsung ke Pulau Mansinam dan melihat anak-anak yang ingin belajar tetapi memiliki akses terbatas terhadap buku, guru, dan layanan kesehatan.\n\nPapua Future Project lahir dari kepedulian itu sebagai gerakan kecil yang dimulai dari satu tenda belajar dan relawan yang percaya bahwa setiap anak itu penting.\n\nGerakan ini terus bertumbuh untuk mendukung anak-anak dan komunitas di Papua Barat.',
  'Mewujudkan generasi Papua yang terdidik, sehat, dan berdaya untuk membangun tanah Papua.',
  'Menyelenggarakan program bimbingan belajar gratis di kampung binaan Papua Barat\nMeningkatkan akses kesehatan dan imunisasi anak-anak Papua Barat\nMemberdayakan pemuda lokal sebagai agen perubahan di komunitas mereka\nMembangun kolaborasi lintas sektor bersama NGO, pemerintah, dan swasta'
);

INSERT INTO team_members
  (member_group, name, role, bio, long_bio, photo_path, is_founder, sort_order, is_published)
VALUES
  ('CORE', 'Bhrisco Jordy Dudi Padatu', 'Founder & Ketua', 'Penggagas PFP sejak 2020 dari Pulau Mansinam, Papua Barat.', 'Bhrisco Jordy Dudi Padatu memulai Papua Future Project dari kepedulian terhadap akses pendidikan dan kesehatan anak-anak di Papua Barat.', 'img/team/Founder.jpg', 1, 1, 1),
  ('CORE', 'Koordinator Program', 'Koordinator Program', 'Bertanggung jawab atas pelaksanaan program di seluruh kampung binaan.', NULL, NULL, 0, 2, 1),
  ('CORE', 'Koordinator Relawan', 'Koordinator Relawan', 'Mengelola rekrutmen dan penugasan relawan dari seluruh Indonesia.', NULL, NULL, 0, 3, 1),
  ('CORE', 'Koordinator Kesehatan', 'Koordinator Kesehatan', 'Mengkoordinasikan program kesehatan dan imunisasi bersama mitra.', NULL, NULL, 0, 4, 1);

-- Batch internship pertama. Foto sengaja NULL agar dapat diunggah lewat panel admin.
INSERT INTO batches (batch_type, name, is_open) VALUES ('INTERN', 'Batch 1', 0);
SET @intern_batch_1_id := LAST_INSERT_ID();
INSERT INTO team_members
  (member_group, batch_id, name, role, university, photo_path, is_founder, sort_order, is_published)
VALUES
  ('INTERN', @intern_batch_1_id, 'A. Choiril Anwar EL-A.R', 'Divisi Web Development', 'Universitas Trunodjoyo Madura', NULL, 0, 1, 1),
  ('INTERN', @intern_batch_1_id, 'Rayhani Dilha', 'Divisi Digital Communication', 'Universitas Diponegoro', NULL, 0, 2, 1),
  ('INTERN', @intern_batch_1_id, 'Suckeyla Rivella', 'Divisi Program Development', 'Universitas Brawijaya', NULL, 0, 3, 1),
  ('INTERN', @intern_batch_1_id, 'Kayla Fitri Fathiyach', 'Divisi Program Development', 'Universitas Indonesia', NULL, 0, 4, 1);

INSERT INTO programs (slug, category_label, title, summary, tagline, body, activities, partners, info_rows, stat1_value, stat1_label, stat2_value, stat2_label, sort_order, is_featured, is_published) VALUES
('bimbel-literasi', 'Pendidikan', 'Bimbingan Belajar & Literasi', 'Mendampingi anak-anak mengembangkan kemampuan membaca, menulis, dan berhitung melalui kegiatan belajar di kampung binaan.', 'Belajar bersama untuk membuka lebih banyak kesempatan.', 'Program bimbingan belajar mendukung literasi dasar anak-anak melalui kegiatan yang disesuaikan dengan kebutuhan dan kondisi komunitas setempat. Relawan mendampingi proses belajar secara rutin dan mendorong lingkungan yang aman serta menyenangkan bagi anak.', 'Bimbingan membaca, menulis, dan berhitung\nKegiatan belajar kelompok\nPojok baca dan donasi buku', NULL, JSON_ARRAY(JSON_OBJECT('label','Wilayah dampingan','value','Papua Barat')), '700+', 'Anak terdampak', '13', 'Kampung binaan', 1, 1, 1),
('literasi-digital', 'Pendidikan', 'Literasi Digital', 'Mendampingi anak dan pemuda mengenal teknologi digital serta menggunakannya dengan aman dan bermanfaat.', 'Membuka akses belajar di dunia digital.', 'Kegiatan literasi digital mengenalkan perangkat, internet, dan keterampilan dasar teknologi secara bertahap. Materi mempertimbangkan ketersediaan perangkat dan konektivitas di lokasi kegiatan.', 'Pengenalan komputer dan perangkat digital\nKeterampilan dasar internet\nPendampingan penggunaan teknologi yang aman', NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 1),
('pojok-baca-donasi-buku', 'Pendidikan', 'Pojok Baca & Donasi Buku', 'Menghadirkan akses buku dan ruang baca yang dekat dengan anak-anak di kampung.', 'Mendekatkan buku dan kebiasaan membaca.', 'Pojok baca menyediakan ruang dan bahan bacaan yang dapat digunakan anak-anak serta komunitas. Donasi buku dikelola untuk mendukung akses bacaan yang sesuai.', 'Penyediaan pojok baca\nPengumpulan dan penyaluran buku\nAktivitas membaca bersama', NULL, NULL, NULL, NULL, NULL, NULL, 3, 0, 1),
('kesehatan-imunisasi', 'Kesehatan', 'Kesehatan & Imunisasi', 'Mendukung edukasi kesehatan anak dan akses layanan kesehatan di wilayah dampingan melalui kolaborasi.', 'Mendukung anak tumbuh sehat melalui edukasi dan kolaborasi.', 'Program kesehatan berfokus pada edukasi kesehatan anak dan dukungan kegiatan layanan bersama tenaga serta mitra terkait. Pelaksanaan mengikuti koordinasi dan arahan penyedia layanan kesehatan.', 'Edukasi kesehatan anak\nDukungan kegiatan imunisasi\nKolaborasi dengan tenaga kesehatan', 'Kolaborasi program dengan UNICEF/Kemenkes sesuai kegiatan yang diselenggarakan.', NULL, NULL, NULL, NULL, NULL, 4, 0, 1),
('pemberdayaan-pemuda', 'Pemuda', 'Pemberdayaan Pemuda Lokal', 'Membuka ruang bagi pemuda lokal untuk mengembangkan kepemimpinan dan berkontribusi bagi komunitas.', 'Pemuda lokal sebagai penggerak perubahan di komunitas.', 'Program mendorong pemuda lokal untuk terlibat dalam kegiatan komunitas, mengembangkan kapasitas diri, dan mengambil peran dalam inisiatif yang sesuai kebutuhan wilayahnya.', 'Kegiatan pengembangan kepemimpinan\nPelibatan pemuda dalam program komunitas\nRuang kolaborasi dan inisiatif lokal', NULL, NULL, NULL, NULL, NULL, NULL, 5, 1, 1);

INSERT INTO schema_migrations (migration) VALUES
  ('0001_create_admin_users.sql'),
  ('0002_create_rate_limits.sql'),
  ('0003_create_about_content.sql'),
  ('0004_create_batches_and_team_members.sql'),
  ('0005_create_programs_and_photos.sql'),
  ('0006_create_posts_and_photos.sql'),
  ('0007_create_faqs_and_contact_messages.sql'),
  ('0008_create_applications.sql'),
  ('0009_seed_initial_programs.sql');


INSERT INTO posts (slug, category, title, excerpt, content, author_name, meta_label, source_name, source_url, published_at, is_published) VALUES
('500-buku-baru', 'ACTIVITY', '500 Buku Baru Tiba di Pulau Mansinam', 'Donasi buku tiba di Pulau Mansinam untuk menambah bacaan anak-anak di pojok baca.', 'Pada Mei 2025, ratusan buku tiba di Pulau Mansinam setelah dikumpulkan dari para pendukung di berbagai wilayah. Buku-buku baru menambah bahan bacaan yang bisa dijelajahi anak-anak bersama relawan.\n\nPojok baca menyediakan ruang bagi anak-anak untuk membaca, berbagi cerita, dan belajar bersama. Donasi buku membantu memperluas pilihan bacaan yang tersedia.', 'Tim Papua Future Project', 'Kegiatan Lapangan', NULL, NULL, '2025-05-15 09:00:00', 1),
('imunisasi-dan-edukasi-kesehatan', 'ACTIVITY', 'Imunisasi dan Edukasi Kesehatan Anak', 'Kegiatan kolaborasi kesehatan mendukung edukasi dan akses layanan untuk keluarga di wilayah dampingan.', 'Papua Future Project mendukung kegiatan edukasi kesehatan dan layanan imunisasi melalui koordinasi dengan tenaga kesehatan serta mitra terkait. Kegiatan disesuaikan dengan kebutuhan kampung dan arahan petugas kesehatan.\n\nInformasi jadwal dan cakupan layanan mengikuti penyelenggara kesehatan setempat.', 'Tim Papua Future Project', 'Kesehatan', NULL, NULL, '2025-04-03 09:00:00', 1),
('pemuda-lokal-kembangkan-kepemimpinan', 'UPDATE', 'Pemuda Lokal Mengembangkan Kepemimpinan', 'Ruang belajar dan kolaborasi membantu pemuda lokal mengambil peran dalam kegiatan komunitas.', 'Papua Future Project mendorong keterlibatan pemuda lokal dalam kegiatan komunitas. Melalui pendampingan dan ruang kolaborasi, pemuda dapat mengembangkan keterampilan serta mengusulkan inisiatif yang sesuai dengan kebutuhan wilayah.\n\nKegiatan pemberdayaan pemuda terus disesuaikan bersama komunitas setempat.', 'Tim Papua Future Project', 'Pemberdayaan Pemuda', NULL, NULL, '2025-02-18 09:00:00', 1),
('belajar-bersama-anak-mansinam', 'ACTIVITY', 'Belajar Bersama Anak-anak di Mansinam', 'Kegiatan pendampingan literasi dasar dilakukan melalui sesi belajar yang menyesuaikan kebutuhan anak.', 'Relawan mendampingi anak-anak dalam kegiatan belajar membaca, menulis, dan berhitung. Sesi belajar dilakukan bersama dan disesuaikan dengan kebutuhan peserta.\n\nKegiatan ini merupakan bagian dari upaya PFP mendukung akses belajar anak di kampung dampingan.', 'Tim Papua Future Project', 'Pendidikan', NULL, NULL, '2024-11-12 09:00:00', 1),
('pojok-baca-ruang-literasi-komunitas', 'UPDATE', 'Pojok Baca sebagai Ruang Literasi Komunitas', 'Pojok baca mendekatkan bahan bacaan dan kegiatan membaca bersama kepada anak-anak.', 'Pojok baca memberi anak-anak dan komunitas ruang untuk mengenal buku serta membangun kebiasaan membaca. Koleksi bacaan dapat digunakan bersama dalam kegiatan belajar dan pendampingan.\n\nPFP terus mengajak masyarakat untuk berkolaborasi mendukung ketersediaan bahan bacaan yang sesuai.', 'Tim Papua Future Project', 'Literasi', NULL, NULL, '2024-08-20 09:00:00', 1),
('relawan-dukung-kegiatan-komunitas', 'ACTIVITY', 'Relawan Mendukung Kegiatan Komunitas', 'Relawan membantu pelaksanaan kegiatan pendidikan, kesehatan, dan aktivitas komunitas di wilayah dampingan.', 'Relawan bergabung bersama tim lokal untuk mendukung kegiatan PFP di lapangan. Sebelum kegiatan, relawan berkoordinasi dengan tim dan mengikuti pembekalan sesuai peran masing-masing.\n\nPFP mengutamakan kerja bersama komunitas dalam merancang dan menjalankan kegiatan.', 'Tim Papua Future Project', 'Relawan', NULL, NULL, '2024-05-10 09:00:00', 1),
('satu-indonesia-awards-2022', 'AWARD', 'Apresiasi SATU Indonesia Awards 2022', 'Papua Future Project menerima apresiasi melalui SATU Indonesia Awards 2022.', 'Apresiasi ini menjadi penyemangat bagi tim dan para relawan untuk melanjutkan kerja bersama anak-anak dan komunitas di Papua Barat. Terima kasih kepada seluruh pihak yang telah mendukung perjalanan PFP.', 'Tim Papua Future Project', 'SATU Indonesia Awards 2022', NULL, NULL, '2022-10-28 09:00:00', 1),
('mitra-ngo-potensial-unicef', 'AWARD', 'Apresiasi Mitra NGO Potensial UNICEF', 'Pengakuan atas kolaborasi dan kerja bersama untuk mendukung anak-anak.', 'Apresiasi sebagai mitra NGO potensial UNICEF mencerminkan pentingnya kolaborasi lintas organisasi. PFP berterima kasih kepada para mitra dan komunitas yang ikut mendukung kegiatan di Papua Barat.', 'Tim Papua Future Project', 'Mitra NGO Potensial UNICEF', NULL, NULL, '2023-06-15 09:00:00', 1),
('kolaborasi-kemenkes-ri', 'AWARD', 'Apresiasi Kolaborasi Kemenkes RI', 'Apresiasi atas kolaborasi untuk mendukung kegiatan kesehatan anak.', 'Kolaborasi dengan Kementerian Kesehatan Republik Indonesia mendukung upaya edukasi dan layanan kesehatan anak. PFP menghargai kontribusi tenaga kesehatan, mitra, relawan, dan komunitas dalam pelaksanaan kegiatan.', 'Tim Papua Future Project', 'Kolaborasi Kemenkes RI', NULL, NULL, '2024-03-01 09:00:00', 1),
('papua-future-project-young-on-top', 'MEDIA', 'Mengenal Papua Future Project dan Inisiatif Sosial Jordy', 'Liputan Young On Top membahas Papua Future Project dan gerakan sosial yang diprakarsai Jordy.', 'Baca liputan media untuk mengetahui lebih jauh tentang Papua Future Project dan upaya memperluas akses pendidikan serta kesehatan anak di Papua Barat.', 'Tim Papua Future Project', NULL, 'Young On Top', 'https://youngontop.com/5-apa-itu-papua-future-project-inisiatif-sosial-dari-bhrisco-jordy/', '2024-01-15 09:00:00', 1),
('papua-future-project-indorelawan', 'MEDIA', 'Papua Future Project di IndoRelawan', 'Profil organisasi Papua Future Project tersedia melalui direktori IndoRelawan.', 'Kunjungi profil IndoRelawan untuk informasi organisasi dan cara mendukung kegiatan Papua Future Project.', 'Tim Papua Future Project', NULL, 'IndoRelawan', 'https://www.indorelawan.org/organization/64b6b425459a1c0028c13b00', '2023-08-01 09:00:00', 1);

INSERT INTO faqs (question, answer, sort_order, is_published) VALUES
('Bagaimana cara mendaftar jadi relawan Papua Future Project?', 'Kamu bisa mendaftar melalui halaman Bergabung saat pendaftaran batch relawan sedang dibuka. Tim kami akan menghubungi pendaftar setelah formulir ditinjau. Pantau Instagram @papuafutureproject untuk informasi pembukaan batch.', 1, 1),
('Apakah ada biaya untuk menjadi relawan?', 'Tidak ada biaya pendaftaran. Biaya transportasi dan akomodasi selama penugasan bergantung pada ketentuan batch dan diinformasikan oleh tim saat proses seleksi.', 2, 1),
('Berapa lama durasi penugasan relawan di lapangan?', 'Durasi penugasan bervariasi menurut program dan batch. Informasi durasi akan dicantumkan saat pendaftaran batch dibuka.', 3, 1),
('Apakah harus punya latar belakang pendidikan untuk jadi relawan pengajar?', 'Tidak harus. Yang terpenting adalah kemauan untuk belajar, beradaptasi, dan berkomitmen selama penugasan. Tim PFP memberikan pembekalan sebelum relawan turun ke lapangan.', 4, 1),
('Bagaimana cara mendonasikan buku ke Papua Future Project?', 'Hubungi kami melalui Instagram atau email untuk mendapatkan informasi pengiriman buku. Kami menerima buku pelajaran, buku cerita anak, dan ensiklopedia dalam kondisi baik.', 5, 1),
('Apakah Papua Future Project menerima kolaborasi dari organisasi lain?', 'Ya, kami terbuka untuk kolaborasi dari komunitas, perusahaan, maupun lembaga lain. Ceritakan ide atau proposalmu melalui formulir kontak atau email.', 6, 1),
('Di mana saja wilayah jangkauan program Papua Future Project?', 'Kegiatan PFP berpusat di Kabupaten Manokwari, Papua Barat. Pulau Mansinam menjadi salah satu titik utama kegiatan.', 7, 1),
('Bagaimana cara memantau perkembangan program Papua Future Project?', 'Ikuti Instagram @papuafutureproject atau baca halaman Berita & Kegiatan di website ini untuk mendapatkan kabar terbaru.', 8, 1);

INSERT INTO schema_migrations (migration) VALUES
  ('0010_seed_news_and_faqs.sql'),
  ('0011_create_application_forms.sql');
