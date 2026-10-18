-- Papua Future Project: complete MySQL schema
-- Import this file into an already-created, empty database using phpMyAdmin.
-- Compatible with MySQL 8+ and MariaDB 10.6+.
-- Source of truth: docs/SCHEMA.md and docs/ERD.md.
-- The schema_migrations rows mark all eight schema units as applied so bin/migrate.php
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
  KEY idx_applications_batch_status (batch_id, status),
  KEY idx_applications_created (created_at),
  CONSTRAINT fk_applications_batch
    FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE RESTRICT
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

INSERT INTO schema_migrations (migration) VALUES
  ('0001_create_admin_users.sql'),
  ('0002_create_rate_limits.sql'),
  ('0003_create_about_content.sql'),
  ('0004_create_batches_and_team_members.sql'),
  ('0005_create_programs_and_photos.sql'),
  ('0006_create_posts_and_photos.sql'),
  ('0007_create_faqs_and_contact_messages.sql'),
  ('0008_create_applications.sql');
