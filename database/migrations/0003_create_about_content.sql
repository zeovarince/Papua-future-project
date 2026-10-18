CREATE TABLE about_content (
  id         TINYINT UNSIGNED NOT NULL DEFAULT 1,
  story      MEDIUMTEXT NOT NULL,
  vision     TEXT NOT NULL,
  mission    TEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT chk_about_content_single CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO about_content (id, story, vision, mission) VALUES (
  1,
  'Pada 2020, Bhrisco Jordy Dudi Padatu turun langsung ke Pulau Mansinam dan melihat anak-anak yang ingin belajar tetapi memiliki akses terbatas terhadap buku, guru, dan layanan kesehatan.\n\nPapua Future Project lahir dari kepedulian itu sebagai gerakan kecil yang dimulai dari satu tenda belajar dan relawan yang percaya bahwa setiap anak itu penting.\n\nGerakan ini terus bertumbuh untuk mendukung anak-anak dan komunitas di Papua Barat.',
  'Mewujudkan generasi Papua yang terdidik, sehat, dan berdaya untuk membangun tanah Papua.',
  'Menyelenggarakan program bimbingan belajar gratis di kampung binaan Papua Barat\nMeningkatkan akses kesehatan dan imunisasi anak-anak Papua Barat\nMemberdayakan pemuda lokal sebagai agen perubahan di komunitas mereka\nMembangun kolaborasi lintas sektor bersama NGO, pemerintah, dan swasta'
);
