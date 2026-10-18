# Responsive Web Design (RWD)

Dokumen ini menetapkan perilaku tampilan Papua Future Project pada ponsel, tablet, dan desktop. Implementasi CSS publik ada di public/assets/css/responsive.css; penyesuaian sidebar admin ada di public/assets/css/admin/admin-layout.css.

## Breakpoint

| Kategori | Lebar viewport | Perilaku utama |
| --- | --- | --- |
| Mobile | kurang dari 480 px | Navigasi hamburger; konten satu kolom bila ruang tidak cukup; gutter kecil. |
| Tablet kecil | 480–767 px | Navigasi hamburger; menu dropdown; gutter dan ruang konten lebih lega. |
| Tablet | 768–1024 px | Navigasi publik memakai hamburger; sidebar admin tampil sebagai ikon saja. |
| Desktop | 1025 px ke atas | Navigasi publik horizontal; sidebar admin tampil penuh dengan lebar 260 px. |

Pada batas 768 px, layar masuk kategori Tablet. Aturan mobile berakhir di 767.98 px, aturan tablet berlaku dari 768 sampai 1024 px, dan gaya default desktop berlaku mulai 1025 px.

## Navigasi publik

- Baris navigasi ponsel menampilkan logo Papua Future Project di kiri dan tombol hamburger di kanan.
- Tombol membuka menu dropdown putih yang turun dari bawah baris navigasi. Menu memuat Beranda, Tentang Kami, Program, Berita, Kontak, dan tombol Jadi Relawan.
- Jadi Relawan menjadi pilihan terakhir di dalam dropdown pada viewport sampai 1024 px.
- Tombol berubah menjadi ikon silang saat menu terbuka.
- Menu ditutup setelah tautan dipilih, saat pengguna menekan Escape, saat area di luar navigasi diklik, atau saat viewport berpindah ke ukuran desktop.
- aria-expanded, aria-controls, dan label tombol diperbarui oleh JavaScript. Tombol dan tautan tetap dapat digunakan dengan keyboard.
- Menu ditampilkan mendatar dan tombol hamburger disembunyikan pada desktop (>1024 px).

## Panel admin

- Desktop (>1024 px): sidebar tetap di kiri dengan lebar 260 px dan label navigasi terlihat.
- Tablet (768–1024 px): sidebar tetap terlihat dengan lebar 76 px. Ikon tetap tampil; label, nama admin, dan label kelompok disembunyikan. Teks aksesibilitas dan tooltip tetap memberi nama item.
- Mobile dan tablet kecil (<768 px): sidebar berada di luar layar sampai tombol hamburger di header ditekan. Sidebar masuk dari kiri dan lapisan gelap menutup area utama. Tombol X, Escape, tautan navigasi, atau klik lapisan gelap menutup menu; fokus keyboard tetap berada di dalam drawer saat terbuka.
- Konten admin menggunakan lebar penuh saat sidebar menjadi drawer. Tabel tetap dapat digulir horizontal bila kolomnya lebih lebar daripada layar.

## Aturan layout

- Gambar, video, dan iframe tidak boleh melampaui lebar kontainer.
- Kontainer publik memakai gutter yang menyesuaikan ukuran layar.
- Grid halaman beralih menjadi jumlah kolom yang lebih sedikit bila konten tidak lagi nyaman dibaca.
- Form dan tombol harus tetap terlihat utuh pada lebar 320 px; teks panjang boleh membungkus.
- Animasi dropdown singkat dan tetap mengikuti pengaturan prefers-reduced-motion global.

## Berkas implementasi

- app/Views/partials/public/header.php — struktur brand, tautan, dan tombol hamburger.
- public/assets/js/main.js — buka/tutup menu dan state aksesibilitas.
- public/assets/css/responsive.css — breakpoint publik dan aturan dropdown.
- app/Views/partials/admin/sidebar.php — label aksesibel dan ikon sidebar.
- public/assets/css/admin/admin-layout.css — sidebar desktop, ikon-only tablet, dan drawer ponsel.
- public/assets/js/admin/admin-ui.js — interaksi drawer admin.

## Pemeriksaan tampilan

Saat meninjau UI, periksa setidaknya lebar 375 px, 600 px, 900 px, dan 1280 px. Pastikan tidak ada scroll horizontal yang tidak disengaja; logo tidak terpotong; menu ponsel dapat dibuka, ditutup, dan dijalankan dengan keyboard; sidebar tablet hanya menampilkan ikon; dan drawer admin tidak menutupi konten setelah ditutup.
