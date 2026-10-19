# DESIGN-ADMIN: Spesifikasi Desain Panel Admin

Panduan visual untuk panel admin Papua Future Project. Layout memakai **sidebar tetap** (bukan navbar atas). Kebutuhan fitur ada di [PRD.md](PRD.md) bagian 7. Route ada di [ARCHITECTURE.md](ARCHITECTURE.md) bagian 6. Alur pengguna ada di [USERFLOW.md](USERFLOW.md).

## 1. Prinsip desain

1. **Sidebar tetap, bukan navbar.** Sidebar ada di sisi kiri pada layar lebar, dan berubah menjadi laci geser di layar sempit.
2. **Konsisten dengan situs publik.** Font Plus Jakarta Sans, palet warna Primary `#3770c1`, Secondary `#40b8fd`, dan aksen yang sama.
3. **Minimalis tapi informatif.** Ruang putih cukup, tipografi jelas, dan ikon hanya untuk mendampingi teks.
4. **Responsif.** Sidebar penuh di desktop (>1024 px), ikon saja di tablet (768–1024 px), dan menjadi drawer hamburger di bawah 768 px. Tabel dapat digulir horizontal jika kolomnya melebihi lebar layar. Detail breakpoint ada di [RWD.md](RWD.md).
5. **Toast, bukan modal, untuk umpan balik.** Kecuali konfirmasi hapus yang memakai dialog.

## 2. Palet warna admin

| Token | Hex | Pemakaian |
| --- | --- | --- |
| `--admin-bg` | `#f4f7fb` | Latar belakang halaman |
| `--admin-sidebar` | `#0c1527` | Latar sidebar |
| `--admin-sidebar-hover` | `#162240` | Item sidebar saat di-hover |
| `--admin-sidebar-active` | `#1e3260` | Item sidebar yang aktif |
| `--admin-sidebar-text` | `#94a3b8` | Teks sidebar |
| `--admin-sidebar-text-active` | `#ffffff` | Teks item aktif |
| `--admin-card` | `#ffffff` | Latar kartu dan tabel |
| `--admin-border` | `#e2e8f0` | Border kartu dan pemisah |
| `--admin-primary` | `#3770c1` | Tombol utama, tautan, aksen |
| `--admin-primary-hover` | `#28589c` | Hover tombol utama |
| `--admin-secondary` | `#40b8fd` | Aksen sekunder |
| `--admin-success` | `#10b981` | Status berhasil, toggle aktif |
| `--admin-warning` | `#f59e0b` | Status menunggu |
| `--admin-danger` | `#ef4444` | Tombol hapus, status ditolak |
| `--admin-text` | `#1e293b` | Teks utama |
| `--admin-text-muted` | `#64748b` | Teks sekunder |

## 3. Layout utama

```
┌──────────────────────────────────────────────────────┐
│ Browser viewport                                     │
│                                                      │
│ ┌─────────┬──────────────────────────────────────┐   │
│ │         │  ┌──────────────────────────────────┐ │   │
│ │         │  │ Page Header                      │ │   │
│ │         │  │ Judul · Breadcrumb · Tombol aksi │ │   │
│ │ SIDEBAR │  └──────────────────────────────────┘ │   │
│ │  260px  │                                      │   │
│ │         │  ┌──────────────────────────────────┐ │   │
│ │  Logo   │  │                                  │ │   │
│ │  Menu   │  │        KONTEN HALAMAN            │ │   │
│ │  Items  │  │                                  │ │   │
│ │         │  │                                  │ │   │
│ │         │  └──────────────────────────────────┘ │   │
│ │         │                                      │   │
│ │ ─────── │                                      │   │
│ │ Profil  │                                      │   │
│ │ Logout  │                                      │   │
│ └─────────┴──────────────────────────────────────┘   │
└──────────────────────────────────────────────────────┘
```

### 3.1 Sidebar (desktop, > 1024 px)

- Lebar tetap: 260 px.
- Latar: `--admin-sidebar` (#0c1527), teks `--admin-sidebar-text`.
- **Atas:** logo PFP kecil + teks "Papua Future Project" + subtitle "Admin Panel".
- **Tengah:** item menu. Tiap item berisi ikon 20 px dan label. Item aktif: latar `--admin-sidebar-active`, teks putih, garis kiri 3 px `--admin-primary`. Hover: latar `--admin-sidebar-hover`.
- **Bawah:** pemisah garis. Profil admin: avatar inisial lingkaran 36 px (gradient primary), nama, keterangan "Administrator". Tombol "Keluar" dengan ikon.

### 3.2 Sidebar (mobile, < 768 px)

- Tersembunyi di luar layar (translateX -100%).
- Tombol hamburger 44 px di pojok kiri atas area konten.
- Saat dibuka: overlay hitam 50% dan sidebar bergeser masuk dari kiri.
- Klik overlay, tombol X, tombol hamburger, tautan navigasi, atau tekan Escape untuk menutup sidebar.

### 3.3 Sidebar (tablet, 768–1024 px)

- Sidebar tetap terlihat di kiri dengan lebar 76 px.
- Tampilkan ikon navigasi dan avatar; sembunyikan label, label kelompok, dan nama admin.
- Setiap ikon memiliki nama aksesibel dan tooltip agar rute tetap dapat dikenali.

### 3.4 Daftar item sidebar

| # | Label | Ikon | Route | Lencana |
| --- | --- | --- | --- | --- |
| 1 | Ringkasan | 📊 grid/dashboard | `/admin` | — |
| 2 | Tentang Kami | ℹ️ info-circle | `/admin/tentang` | — |
| — | **TIM** | — | — | — |
| 3 | Tim Penggerak | 👥 users | `/admin/tim/penggerak` | — |
| 4 | Internship | 🎓 graduation-cap | `/admin/tim/internship` | — |
| 5 | Volunteer | 🤝 hand-heart | `/admin/tim/volunteer` | — |
| 6 | Batch | 📦 layers | `/admin/batch` | — |
| — | **KONTEN** | — | — | — |
| 7 | Program | 📚 book-open | `/admin/program` | — |
| 8 | Berita | 📰 newspaper | `/admin/berita` | — |
| 9 | FAQ | ❓ help-circle | `/admin/faq` | — |
| — | **DATA** | — | — | — |
| 10 | Pendaftar | 📋 clipboard-list | `/admin/pendaftar` | jumlah status Baru |
| 11 | Pesan Masuk | ✉️ mail | `/admin/pesan` | jumlah belum dibaca |

Item tambahan di grup DATA: **Form Pendaftaran** (`/admin/pendaftaran`) membuka toggle aktif/nonaktif Internship dan Relawan serta tautan menuju pengelolaan pertanyaan masing-masing.

Label grup (TIM, KONTEN, DATA) ditampilkan sebagai teks kecil uppercase abu-abu, bukan item yang bisa diklik.

### 3.5 Page Header

- Di atas area konten, di dalam wrapper dengan padding 24 px.
- Baris 1: Breadcrumb (contoh: Admin > Program > Ubah).
- Baris 2: Judul halaman (h1, 24 px, bold) di kiri; tombol aksi utama di kanan (contoh: "+ Tambah Program").
- Di mobile, tombol hamburger ada di kiri sebelum breadcrumb.

## 4. Komponen UI admin

### 4.1 Kartu (Card)

- Latar putih, border 1 px `--admin-border`, border-radius 16 px, shadow `0 1px 3px rgba(0,0,0,0.04)`.
- Padding dalam: 24 px. Jarak antar kartu: 20 px.

### 4.2 Tabel data

- Di dalam kartu. Header: latar `#f8fafc`, teks uppercase 11 px bold, tracking wide.
- Baris: tinggi minimal 56 px, border bawah 1 px, hover latar `#f8fafc`.
- Kolom aksi: tombol kecil (pill) di kanan. Warna sesuai aksi: biru untuk "Ubah", merah untuk "Hapus", hijau untuk "Setujui".
- Di layar sempit: tabel gulung horizontal, atau berubah menjadi kartu bertumpuk.

### 4.3 Form

- Label: 13 px, font-weight 600, warna `--admin-text`, margin bawah 6 px.
- Input: tinggi 44 px, latar `#f8fafc`, border 1 px `--admin-border`, border-radius 10 px, padding kiri 14 px.
- Focus: border `--admin-primary`, ring 2 px `--admin-primary` 15%.
- Textarea: minimal tinggi 120 px.
- Select: sama dengan input, dengan ikon panah bawah.
- Error: teks 12 px merah `--admin-danger` di bawah field, border input berubah merah.

### 4.4 Tombol

| Variasi | Latar | Teks | Border | Pemakaian |
| --- | --- | --- | --- | --- |
| Primary | `--admin-primary` | putih | — | Simpan, Tambah |
| Secondary | transparan | `--admin-primary` | 1 px `--admin-primary` | Batal, Kembali |
| Danger | `--admin-danger` | putih | — | Hapus (di dialog konfirmasi) |
| Ghost | transparan | `--admin-text-muted` | — | Aksi minor |

Semua tombol: border-radius 10 px, tinggi 40 px (atau 36 px untuk yang kecil), font-weight 600, transisi 150 ms.

### 4.5 Toggle switch

- Lebar 44 px, tinggi 24 px, border-radius penuh.
- Mati: latar `#cbd5e1`, lingkaran putih di kiri.
- Hidup: latar `--admin-success`, lingkaran putih di kanan.
- Transisi 200 ms.
- Label di samping kiri: "Pendaftaran dibuka".

### 4.6 Status pill

| Status | Latar | Teks | Border |
| --- | --- | --- | --- |
| Baru | `#fef3c7` | `#92400e` | `#fde68a` |
| Ditinjau | `#dbeafe` | `#1e40af` | `#bfdbfe` |
| Diterima | `#d1fae5` | `#065f46` | `#a7f3d0` |
| Ditolak | `#fee2e2` | `#991b1b` | `#fecaca` |
| Dibuka | `#d1fae5` | `#065f46` | `#a7f3d0` |
| Ditutup | `#f1f5f9` | `#475569` | `#e2e8f0` |
| Draf | `#f1f5f9` | `#475569` | `#e2e8f0` |
| Terbit | `#dbeafe` | `#1e40af` | `#bfdbfe` |

Font-size 11 px, font-weight 600, padding 4 px 10 px, border-radius penuh.

### 4.7 Toast notification

- Muncul di pojok kanan bawah, geser masuk dari bawah.
- Lebar maksimal 360 px, border-radius 12 px, shadow besar.
- Variasi: sukses (hijau gelap), galat (merah gelap), info (biru gelap).
- Hilang otomatis setelah 4 detik.

### 4.8 Dialog konfirmasi hapus

- Modal tengah layar, overlay hitam 60%.
- Kartu: maks 400 px, border-radius 16 px, padding 24 px.
- Ikon peringatan bulat merah di atas.
- Teks menyebut nama item yang dihapus.
- Dua tombol: "Batal" (secondary), "Hapus" (danger).

### 4.9 Pagination

- Di bawah tabel, rata kanan.
- Tombol angka, tombol panah kiri dan kanan.
- Halaman aktif: latar `--admin-primary`, teks putih.

### 4.10 Gallery manager (foto dokumentasi)

- Grid 4 kolom (2 di mobile) di bawah form ubah program atau berita.
- Tiap foto: thumbnail 1:1 dengan border-radius 8 px, keterangan di bawah (input kecil), tombol hapus (×) di pojok kanan atas.
- Tombol "+ Tambah foto" di akhir grid: area putus-putus 1:1, ikon kamera.
- Sisa kuota ditampilkan: "12 dari 30 foto".

## 5. Halaman per halaman

### 5.1 Ringkasan (`/admin`)

- Salam: "Selamat datang, [nama]" di banner gradient gelap (sama seperti yang sudah ada di prototype).
- 4 kartu metrik dalam satu baris:
  - Pendaftar baru (status Baru, jumlah + ikon)
  - Pesan belum dibaca
  - Batch sedang dibuka (jumlah)
  - Berita terbit (jumlah)
- Tabel "Pendaftar Terbaru" (5 baris terakhir, kolom: nama, batch, tipe, status, tanggal, tombol "Lihat").
- Tabel "Pesan Terbaru" (3 baris terakhir, kolom: nama, email, ringkasan pesan, tanggal, sudah dibaca?).

### 5.2 Tentang Kami (`/admin/tentang`)

- Satu form berisi:
  - Cerita (textarea besar, minimal 200 px tinggi). Teks biasa, `**tebal**` diperbolehkan.
  - Visi (textarea satu paragraf).
  - Misi (textarea, satu butir per baris, dipisah baris baru).
- Tombol "Simpan" di bawah.
- Tidak ada tambah atau hapus, selalu satu baris.

### 5.3 Tim Penggerak (`/admin/tim/penggerak`)

- Tabel: foto (lingkaran 40 px), nama, jabatan, urutan, tombol "Ubah" dan "Hapus".
- Kolom khusus: tanda bintang atau label "Founder" di samping nama untuk yang ditandai `is_founder`.
- Form tambah/ubah: nama, jabatan, deskripsi (textarea), bio panjang (textarea, opsional, untuk Beranda), foto (upload dengan pratinjau), checkbox "Founder", nomor urut.

### 5.4 Internship (`/admin/tim/internship`)

- Dikelompokkan per batch. Tiap kelompok punya header: nama batch, pill status (Dibuka/Ditutup), toggle, tombol "+ Tambah Anggota".
- Tabel per batch: foto (lingkaran 40 px), nama, universitas, peran, urutan, "Ubah", "Hapus".
- Bila belum ada batch bertipe Intern, tampilkan ajakan: "Buat batch Intern lebih dulu di halaman Batch."
- Form tambah/ubah anggota: batch (auto-fill), nama, universitas, peran/bidang, foto, urutan.

### 5.5 Volunteer (`/admin/tim/volunteer`)

Sama persis dengan Internship, tetapi filter batch ke tipe `VOLUNTEER`.

### 5.6 Batch (`/admin/batch`)

- Tabel: nama batch, tipe (pill: Intern/Volunteer), status (pill: Dibuka/Ditutup), jumlah anggota, jumlah pendaftar, tanggal dibuat, toggle, "Ubah", "Hapus".
- Hapus ditolak jika ada anggota atau pendaftar (pesan galat dengan jumlah).
- Form tambah/ubah: nama, tipe (pilih Intern atau Volunteer, tidak bisa diubah setelah ada data), deskripsi (opsional).

### 5.7 Program (`/admin/program`)

- Tabel: thumbnail kecil (60×40 px, border-radius 6 px), judul, kategori (pill), unggulan (bintang), status (pill: Terbit/Draf), urutan, "Ubah", "Hapus".
- Form tambah/ubah:
  - Judul, slug (otomatis dari judul, bisa diubah manual).
  - Kategori (pilih: Pendidikan, Kesehatan, Pemuda).
  - Label badge, tagline, ringkasan (textarea).
  - Konten (textarea besar, teks biasa dengan `**tebal**`).
  - Kegiatan dalam program (textarea, satu per baris).
  - Mitra kolaborasi (textarea, satu per baris).
  - Informasi Program (pasangan label-nilai, bisa tambah baris).
  - Thumbnail (upload).
  - Checkbox "Unggulan", checkbox "Terbit".
  - Nomor urut.
- Bagian Foto dokumentasi (gallery manager) muncul di halaman ubah, bukan di tambah.

### 5.8 Berita dan Kegiatan (`/admin/berita`)

- Tabel: thumbnail, judul, kategori (pill), status (pill), tanggal terbit, "Ubah", "Hapus".
- Empat kategori: Kegiatan Lapangan, Update Program, Liputan Media, Penghargaan.
- Form tambah/ubah:
  - Judul, slug (otomatis).
  - Kategori (pilih).
  - Tanggal terbit (date picker).
  - Ringkasan (textarea).
  - Isi (textarea besar, teks biasa dengan `**tebal**` dan `> kutipan`).
  - Gambar sampul (upload).
  - Penulis (teks, default nama admin).
  - Untuk Penghargaan: keterangan pemberi penghargaan (teks).
  - Untuk Liputan Media: nama media, URL artikel asli.
  - Checkbox "Terbitkan".
- Bagian Foto dokumentasi (gallery manager) muncul di halaman ubah.

### 5.9 FAQ (`/admin/faq`)

- Tabel: pertanyaan (dipotong 60 karakter), urutan, status (pill: Terbit/Sembunyi), "Ubah", "Hapus".
- Form tambah/ubah: pertanyaan (input teks), jawaban (textarea), nomor urut, checkbox "Tampilkan".

### 5.10 Pendaftar (`/admin/pendaftar`)

- Bar filter: tipe (Semua/Intern/Relawan), batch (dropdown), status (dropdown), pencarian nama.
- Tombol "Ekspor data" (dropdown: Excel, CSV, PDF).
- Tabel: nama, email, batch (pill), tipe (pill), status (pill warna), tanggal daftar, "Lihat".
- Halaman detail (`/admin/pendaftar/{id}`):
  - Kartu data lengkap: semua field teks dalam tata letak dua kolom.
  - Foto inline: gambar pendaftar maks 200 px, border-radius 12 px.
  - CV: tombol "Lihat CV" (buka tab baru) dan "Download CV".
  - Dropdown ubah status: Baru → Ditinjau → Diterima / Ditolak. Tombol "Simpan Status".

### 5.11 Form Pendaftaran (`/admin/pendaftaran`)

- Dua kartu terpisah untuk Internship dan Relawan; masing-masing menunjukkan status formulir, jumlah batch yang dibuka, dan jumlah pertanyaan aktif.
- Tombol aktif/nonaktif bekerja terpisah per jenis dan tidak mengubah status batch. Pendaftaran publik hanya diterima jika jenis aktif serta batch terpilih dibuka.
- Tombol "Atur pertanyaan" membuka daftar dan CRUD yang hanya berlaku untuk jenis itu. Pertanyaan bisa berupa jawaban singkat atau paragraf, wajib atau opsional, tampil atau disembunyikan, serta diurutkan.
- Maksimal 25 pertanyaan tambahan per jenis. Jawaban tampil di detail pendaftar dan ikut ekspor. Teks pertanyaan disalin pada jawaban untuk mempertahankan riwayat setelah perubahan atau penghapusan.

### 5.12 Pesan Masuk (`/admin/pesan`)

- Tabel: pengirim, email, ringkasan pesan (dipotong 50 karakter), tanggal, status (pill: Belum dibaca/Sudah dibaca), "Lihat".
- Baris belum dibaca: font-weight bold, titik biru di kiri.
- Halaman detail:
  - Nama, email, WhatsApp (jika ada), tanggal.
  - Isi pesan lengkap (teks biasa).
  - Tombol: "Balas lewat Email" (mailto:), "Balas lewat WA" (wa.me, jika ada nomor), "Hapus" (merah, konfirmasi dialog).
  - Pesan ditandai sudah dibaca saat halaman ini dibuka.

### 5.13 Login (`/admin/login`)

- Tidak ada sidebar. Layout khusus auth.
- Desain yang sudah ada di `login.html` dipertahankan: split view desktop (kiri: slider foto dengan gradient gelap, kanan: kartu form), mobile: form full-width.
- Form: email, kata sandi (toggle show/hide), tombol "Masuk".
- Pesan galat muncul di atas form. Tidak menyebut apakah email atau kata sandi yang salah.
- Tautan "Kembali ke Website Utama" di bawah form.

## 6. State kosong (empty state)

Setiap halaman daftar yang belum punya data menampilkan:
- Ikon besar abu-abu di tengah (sesuai konteks).
- Judul: "Belum ada [item]".
- Subjudul: kalimat ajakan.
- Tombol "+ Tambah [item]" (primary).

Contoh untuk Program:
- Ikon: buku terbuka.
- Judul: "Belum ada program"
- Subjudul: "Tambahkan program pertama untuk ditampilkan di website."
- Tombol: "+ Tambah Program"

## 7. Responsif

| Breakpoint | Perubahan |
| --- | --- |
| ≥ 1024 px | Sidebar tetap terlihat, konten di kanan sidebar. |
| 768–1023 px | Sidebar tersembunyi, tombol hamburger. Tabel masih tabel. |
| < 768 px | Sidebar laci, tabel berubah menjadi kartu bertumpuk atau gulung horizontal. Grid form satu kolom. |
