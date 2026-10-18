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

INSERT INTO team_members
  (member_group, name, role, bio, long_bio, photo_path, is_founder, sort_order, is_published)
VALUES
  ('CORE', 'Bhrisco Jordy Dudi Padatu', 'Founder & Ketua', 'Penggagas PFP sejak 2020 dari Pulau Mansinam, Papua Barat.', 'Bhrisco Jordy Dudi Padatu memulai Papua Future Project dari kepedulian terhadap akses pendidikan dan kesehatan anak-anak di Papua Barat.', 'img/team/Founder.jpg', 1, 1, 1),
  ('CORE', 'Koordinator Program', 'Koordinator Program', 'Bertanggung jawab atas pelaksanaan program di seluruh kampung binaan.', NULL, NULL, 0, 2, 1),
  ('CORE', 'Koordinator Relawan', 'Koordinator Relawan', 'Mengelola rekrutmen dan penugasan relawan dari seluruh Indonesia.', NULL, NULL, 0, 3, 1),
  ('CORE', 'Koordinator Kesehatan', 'Koordinator Kesehatan', 'Mengkoordinasikan program kesehatan dan imunisasi bersama mitra.', NULL, NULL, 0, 4, 1);
