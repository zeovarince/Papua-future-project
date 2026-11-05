# Responsive Web Design (RWD)

Dokumen ini menetapkan perilaku tampilan Papua Future Project pada ponsel, tablet, dan desktop. Implementasi CSS publik ada di `public/assets/css/responsive.css`; penyesuaian navigasi dan layout admin ada di `public/assets/css/admin/admin-layout.css` dan `public/assets/js/admin/admin-ui.js`. Spesifikasi lengkap panel admin ada di [DESIGN-ADMIN.md](DESIGN-ADMIN.md).

## Breakpoint

| Kategori | Lebar viewport | Perilaku utama |
| --- | --- | --- |
| Mobile & Tablet kecil | kurang dari 768 px | Navigasi publik hamburger; panel admin memakai drawer hamburger 280 px dengan label dan akordeon; konten 1 kolom; bilah aksi sticky di dasar layar tanpa offset rail; gutter/padding 16 px. |
| Tablet | 768–1279 px | Navigasi publik memakai hamburger (sampai 1024 px) lalu horizontal; panel admin memakai rail 64 px tetap di kiri dengan flyout detail 280 px overlay saat hover/fokus/sentuh; konten 1 kolom; kartu metrik 2x2; tabel gulung horizontal bila kolom melebihi layar. |
| Desktop | 1280 px ke atas | Navigasi publik horizontal; panel admin memakai rail 64 px tetap di kiri dengan flyout hover 280 px; layout 2 kolom (8 + 4 kolom) pada Ringkasan, Form, dan Detail; kontainer konten maksimal 1280 px rata tengah; padding 24 px. |

Pada batas 768 px, panel admin beralih dari drawer mobile ke rail 64 px tetap.

## Navigasi publik

- Baris navigasi ponsel menampilkan logo Papua Future Project di kiri dan tombol hamburger di kanan.
- Tombol membuka menu dropdown putih yang turun dari bawah baris navigasi. Menu memuat Beranda, Tentang Kami, Program, Berita, Kontak, dan tombol Jadi Relawan.
- Jadi Relawan menjadi pilihan terakhir di dalam dropdown pada viewport sampai 1024 px.
- Tombol berubah menjadi ikon silang saat menu terbuka.
- Menu ditutup setelah tautan dipilih, saat pengguna menekan Escape, saat area di luar navigasi diklik, atau saat viewport berpindah ke ukuran desktop.
- `aria-expanded`, `aria-controls`, dan label tombol diperbarui oleh JavaScript. Tombol dan tautan tetap dapat digunakan dengan keyboard.
- Menu ditampilkan mendatar dan tombol hamburger disembunyikan pada desktop (>1024 px).

## Panel admin

- **Desktop dan Tablet (>= 768 px)**: Rail sempit 64 px tetap di sisi kiri dengan latar biru terang `--admin-sidebar` (`#3770c1`). Konten bergeser `margin-left: 64px`. Saat ikon rail di-hover atau difokus, kartu flyout putih 280 px muncul menimpa konten (overlay) tanpa mendorong layout, menampilkan nama halaman, deskripsi singkat, dan sub-halaman. Di layar sentuh tablet, ketukan pertama membuka flyout dan ketukan berikutnya membuka tautan.
- **Mobile dan Tablet kecil (< 768 px)**: Rail tersembunyi. Tombol hamburger 40 px di kiri atas header membuka drawer selebar 280 px dari sisi kiri dengan lapisan gelap 50%. Menu ditampilkan lengkap dengan label grup dan akordeon untuk grup yang memiliki sub-halaman. Tombol X, penekanan Escape, klik lapisan gelap, atau klik pada tautan navigasi menutup drawer.
- **Bilah Aksi Sticky**: Form tambah/ubah memiliki bilah aksi "Batal" dan "Simpan" yang menempel di dasar viewport (`z-index: 30`, di bawah flyout `z-index: 50`). Pada desktop (>= 768 px) bilah aksi memiliki offset kiri 64 px; pada mobile (< 768 px) memenuhi lebar layar penuh.
- **Tabel Responsif**: Semua tabel berada di dalam kartu dengan pembungkus `.table-responsive` yang dapat digulir horizontal bila kolom melebihi lebar layar tanpa memicu scroll horizontal pada elemen `body`.

## Aturan layout

- Gambar, video, dan iframe tidak boleh melampaui lebar kontainer.
- Kontainer publik memakai gutter yang menyesuaikan ukuran layar.
- Grid halaman beralih menjadi jumlah kolom yang lebih sedikit bila konten tidak lagi nyaman dibaca.
- Form dan tombol harus tetap terlihat utuh pada lebar 320 px; teks panjang boleh membungkus.
- Animasi dropdown dan flyout singkat dan otomatis dimatikan bila pengguna mengaktifkan preferensi `prefers-reduced-motion`.

## Berkas implementasi

- `app/Views/partials/public/header.php` — struktur brand, tautan, dan tombol hamburger publik.
- `public/assets/js/main.js` — buka/tutup menu dan state aksesibilitas publik.
- `public/assets/css/responsive.css` — breakpoint publik dan aturan dropdown.
- `app/Support/AdminMenu.php` — definisi tunggal struktur menu panel admin.
- `app/Views/partials/admin/sidebar.php` — rail 64 px, flyout detail 280 px, dan drawer mobile.
- `public/assets/css/admin/admin-layout.css` — layout rail, flyout overlay, drawer mobile, page header, dan sticky action bar.
- `public/assets/css/admin/admin-components.css` — datatable, pagination, kartu metrik, tren area chart, kartu aksi cepat, dan aktivitas.
- `public/assets/js/admin/admin-ui.js` — interaksi hover/jeda flyout, keyboard aksesibel, drawer mobile, modal dialog, dan toast.

## Pemeriksaan tampilan

Saat meninjau UI, periksa setidaknya lebar 390 px, 768 px, 1024 px, 1280 px, dan 1440 px. Pastikan tidak ada scroll horizontal pada body; rail tetap 64 px di >= 768 px; flyout tidak keluar dari layar; menu drawer ponsel dapat dibuka, ditutup, dan dijalankan dengan keyboard; serta bilah aksi form tidak menutupi isi kartu.
