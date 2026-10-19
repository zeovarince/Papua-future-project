CREATE TABLE application_form_settings (
  application_type ENUM('INTERN','VOLUNTEER') NOT NULL,
  is_active        TINYINT(1) NOT NULL DEFAULT 0,
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
