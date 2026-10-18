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
