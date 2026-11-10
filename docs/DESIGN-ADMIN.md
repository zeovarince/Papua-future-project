# DESIGN-ADMIN: Spesifikasi Desain Panel Admin

Panduan visual untuk panel admin Papua Future Project. Layout memakai **rail ikon tetap di kiri** (bukan navbar atas) dengan **flyout detail yang muncul saat hover**. Kebutuhan fitur ada di [PRD.md](PRD.md) bagian 7. Route ada di [ARCHITECTURE.md](ARCHITECTURE.md) bagian 6. Alur pengguna ada di [USERFLOW.md](USERFLOW.md).

> **Ringkasan perubahan dari versi sebelumnya**
> - Sidebar lebar 260 px diganti **rail ikon 64 px berwarna biru terang** (Primary `#3770c1`). Label tidak lagi tampil permanen.
> - Menu dikelompokkan menjadi 6 ikon utama. Saat di-hover, **flyout putih** muncul di sisi kanan rail berisi nama halaman, deskripsi singkat, dan sub-halaman.
> - Flyout menimpa konten (overlay), tidak mendorong layout.
> - Tidak ada tema gelap di navigasi dan tidak ada emoji. Ikon memakai set ikon garis (misalnya Lucide).
> - **Area isi ikut dirombak** mengikuti gambar referensi: kartu putih membulat 20 px di atas latar abu muda, tombol pill, tabel lebih ringan, dan pola halaman yang seragam (daftar, form, detail).
> - Ringkasan hanya menampilkan pesan belum dibaca, berita terbit, dan pesan terbaru. Link pendaftaran dan buka/tutup pendaftaran diatur dari halaman Batch.
> - Inkonsistensi lama diselesaikan: bagian 3.3 menyebut sidebar tablet 76 px, sedangkan bagian 7 menyebut hamburger di 768-1023 px. Sekarang rail berlaku di semua layar >= 768 px.

## 1. Prinsip desain

1. **Rail ikon tetap, bukan navbar.** Rail ada di sisi kiri pada layar >= 768 px, dan berubah menjadi laci geser di layar sempit.
2. **Detail muncul saat dibutuhkan.** Rail hanya berisi ikon agar area konten luas. Nama halaman, deskripsi, dan sub-menu tampil di flyout saat hover atau fokus.
3. **Konsisten dengan situs publik.** Font Plus Jakarta Sans, palet warna Primary `#3770c1`, Secondary `#40b8fd`, dan aksen yang sama.
4. **Minimalis tapi informatif.** Ruang putih cukup, tipografi jelas, dan ikon hanya untuk mendampingi teks.
5. **Isi berbasis kartu.** Semua konten berada di kartu putih membulat di atas latar `--admin-bg`. Tiap kartu punya judul, dan kontrolnya (cari, filter, aksi) ada di baris atas kartu.
6. **Responsif.** Rail + flyout di >= 768 px, drawer hamburger di bawah 768 px. Tabel dapat digulir horizontal jika kolomnya melebihi lebar layar. Detail breakpoint ada di [RWD.md](RWD.md).
7. **Toast, bukan modal, untuk umpan balik.** Kecuali konfirmasi hapus yang memakai dialog.

## 2. Palet warna admin

| Token | Hex | Pemakaian |
| --- | --- | --- |
| `--admin-bg` | `#f4f7fb` | Latar belakang halaman |
| `--admin-sidebar` | `#3770c1` | Latar rail dan drawer (sama dengan Primary) |
| `--admin-sidebar-hover` | `rgba(255,255,255,0.16)` | Ikon atau baris navigasi saat di-hover |
| `--admin-sidebar-active` | `#ffffff` | Latar kotak ikon rail yang aktif |
| `--admin-sidebar-text` | `#dbe7f7` | Ikon dan teks pada rail dan drawer |
| `--admin-sidebar-text-active` | `#3770c1` | Ikon atau teks pada item aktif (di atas latar putih) |
| `--admin-flyout` | `#ffffff` | **Baru.** Latar kartu flyout |
| `--admin-flyout-border` | `#e2e8f0` | **Baru.** Border kartu flyout dan pemisah di dalamnya |
| `--admin-flyout-hover` | `#eef4fc` | **Baru.** Baris flyout saat di-hover |
| `--admin-flyout-active` | `#dfeafa` | **Baru.** Baris flyout halaman aktif |
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
┌────┬────────────────────────────────────────────────────┐
│ PF │  Admin > Tim > Internship                          │
│ +  │  Internship                    [+ Tambah Anggota]  │
│ -- │                                                    │
│ Rg │  ┌──────────────────────────────────────────────┐  │
│ Tk │  │                                              │  │
│ Tm ├──┼─────────────────────┐                        │  │
│ Kn │  │ Tim                 │    KONTEN HALAMAN      │  │
│ Dt │  │ Anggota, magang ... │                        │  │
│    │  │ | Tim Penggerak     │                        │  │
│    │  │   Internship        │                        │  │
│    │  │   Volunteer         │                        │  │
│    │  │   Batch         [2] │                        │  │
│ -- │  └─────────────────────┘                        │  │
│ Ad │  └──────────────────────────────────────────────┘  │
└────┴────────────────────────────────────────────────────┘
 64px   flyout muncul saat hover, menimpa konten
```

Keterangan diagram: PF = logo, Rg = Ringkasan, Tk = Tentang Kami, Tm = Tim, Kn = Konten, Dt = Data, Ad = avatar admin. Pada implementasi, singkatan ini diganti ikon garis.

### 3.1 Rail (>= 768 px)

- Lebar tetap: 64 px, tinggi penuh viewport, posisi `fixed`. Konten digeser `margin-left: 64px`.
- Latar `--admin-sidebar` (#3770c1), biru solid tanpa gradient gelap.
- **Atas:** logo PFP dalam kotak putih 36 px (border-radius 10 px). Di bawahnya tombol aksi cepat "+" (opsional, lihat 3.5), lalu garis pemisah 1 px putih 24%.
- **Tengah:** ikon menu. Tiap item berupa kotak 44x44 px, border-radius 12 px, ikon garis 20 px berwarna `--admin-sidebar-text`, jarak antar item 8 px.
  - Hover atau fokus: latar `--admin-sidebar-hover`, ikon putih.
  - Aktif (halaman saat ini berada di dalam grup ini): latar putih `--admin-sidebar-active`, ikon `--admin-sidebar-text-active` (#3770c1). Kotak putih ini penanda utama item aktif.
  - Lencana angka untuk pesan belum dibaca: bulatan kecil di pojok kanan atas ikon, latar `--admin-danger`, teks putih 10 px, dengan border 2 px `--admin-sidebar` supaya terpisah dari ikon.
  - Fokus keyboard: outline 2 px putih dengan offset 2 px.
- **Bawah:** garis pemisah, lalu avatar admin: lingkaran putih 36 px, inisial `--admin-primary` tebal, ring 2 px `--admin-secondary`. Hover pada avatar membuka flyout profil.
- Tidak ada label teks di rail. Setiap item wajib punya `aria-label`.

### 3.2 Flyout detail (hover)

Kartu putih kecil yang muncul di kanan item rail saat di-hover atau difokus. Ini yang menjelaskan halaman apa yang ada di balik tiap ikon. Kontras biru-putih membuatnya jelas terpisah dari rail.

```
┌──────────────────────────────┐
│ [ ]  Tim                     │
│ Anggota, magang, dan relawan │
│ ──────────────────────────── │
│ | [ ] Tim Penggerak          │
│       Pengurus dan founder   │
│   [ ] Internship             │
│       Anggota magang/batch   │
│   [ ] Volunteer              │
│       Relawan per batch      │
│   [ ] Batch              [2] │
│       Periode Intern/Vol.    │
└──────────────────────────────┘
```

Tanda "[ ]" adalah tempat ikon garis. Tanda "|" di kiri baris adalah penanda halaman aktif.

**Struktur kartu**

1. **Header:** kotak ikon 32 px (border-radius 10 px) berlatar gradient `--admin-primary` ke `--admin-secondary` dengan ikon putih. Di sampingnya nama grup (15 px, bold, `--admin-text`) dan satu baris deskripsi grup (12 px, `--admin-text-muted`).
2. **Pemisah:** garis 1 px `--admin-flyout-border`.
3. **Daftar sub-halaman:** tiap baris berisi ikon 18 px `--admin-text-muted`, nama halaman (13 px, semi-bold, `--admin-text`), deskripsi satu baris (11 px, `--admin-text-muted`), dan lencana jika ada. Tinggi baris minimal 48 px, padding 8 px 10 px, border-radius 10 px.
   - Hover baris: latar `--admin-flyout-hover`, ikon dan nama `--admin-primary`.
   - Baris halaman aktif: latar `--admin-flyout-active`, garis kiri 3 px `--admin-primary`, ikon dan nama `--admin-primary`.
4. **Grup dengan satu halaman** (Ringkasan, Tentang Kami): kartu hanya berisi header, deskripsi, dan satu baris info singkat (opsional, misalnya jumlah pesan belum dibaca untuk Ringkasan).

**Tampilan**

- Lebar 280 px. Latar `--admin-flyout`, border 1 px `--admin-flyout-border`, border-radius 16 px, padding 12 px, shadow `0 12px 32px rgba(15,23,42,0.18)`.
- Posisi: 8 px di kanan tepi rail, sejajar dengan atas item yang di-hover. Jika kartu akan keluar dari bagian bawah viewport, geser ke atas sampai muat. Flyout profil di bawah rail membuka ke atas, sejajar dengan bawah avatar.
- `z-index: 50`: di atas konten, di bawah dialog (100) dan toast (110).
- Animasi masuk: opacity 0 ke 1 dan translateX -6 px ke 0, 150 ms ease-out. Keluar: 100 ms.
- Fokus keyboard pada baris: outline 2 px `--admin-secondary`.
- Flyout **tidak mendorong konten**. Ia menimpa area konten selama terbuka.

### 3.3 Perilaku hover, fokus, dan sentuh

| Aksi | Hasil |
| --- | --- |
| Kursor masuk ke ikon rail | Flyout grup itu terbuka setelah jeda 80 ms |
| Kursor pindah ke ikon rail lain | Flyout langsung berganti tanpa jeda |
| Kursor pindah dari ikon ke flyout | Flyout tetap terbuka. Celah 8 px di antaranya ditutup elemen transparan agar kursor tidak "jatuh" |
| Kursor keluar dari rail dan flyout | Flyout tertutup setelah jeda 150 ms |
| Klik ikon grup | Menuju sub-halaman pertama grup tersebut. Ikon grup satu halaman langsung menuju halamannya |
| Klik baris di flyout | Menuju halaman itu dan flyout tertutup |
| Fokus keyboard (Tab) ke ikon | Flyout terbuka seperti hover |
| Panah bawah / atas di dalam flyout | Pindah antar baris. `Enter` membuka halaman |
| `Esc` atau Tab keluar dari flyout | Flyout tertutup, fokus kembali ke ikon |
| Layar sentuh, ketuk ikon grup | Ketukan pertama membuka flyout, ketukan pada baris membuka halaman. Ketuk di luar menutup |

Persyaratan aksesibilitas: ikon memakai `aria-haspopup="menu"` dan `aria-expanded`. Flyout memakai `role="menu"` dan baris memakai `role="menuitem"`. Animasi dimatikan jika pengguna memilih `prefers-reduced-motion`.

### 3.4 Daftar menu rail dan isi flyout

| # | Ikon rail (Lucide) | Grup | Isi flyout (sub-halaman, route, deskripsi) | Lencana |
| --- | --- | --- | --- | --- |
| 1 | `layout-dashboard` | Ringkasan | `/admin`: "Ringkasan aktivitas dan data terbaru." | - |
| 2 | `info` | Tentang Kami | `/admin/tentang`: "Cerita, visi, dan misi organisasi." | - |
| 3 | `users` | Tim | **Tim Penggerak** `/admin/tim/penggerak`: "Pengurus dan founder." **Internship** `/admin/tim/internship`: "Anggota magang per batch." **Volunteer** `/admin/tim/volunteer`: "Relawan per batch." **Batch** `/admin/batch`: "Periode Intern dan Volunteer." | - |
| 4 | `newspaper` | Konten | **Program** `/admin/program`: "Program, kegiatan, dan foto dokumentasi." **Berita** `/admin/berita`: "Berita, liputan, dan penghargaan." **FAQ** `/admin/faq`: "Pertanyaan yang sering diajukan." | - |
| 5 | `inbox` | Data | **Pesan Masuk** `/admin/pesan`: "Pesan dari formulir kontak." | Pesan belum dibaca |
| - | avatar | Profil | Nama admin, keterangan "Administrator", tautan **Lihat Website** (buka tab baru), dan **Keluar** | - |

Ikon sub-halaman di flyout (Lucide): `user-check` (Tim Penggerak), `graduation-cap` (Internship), `hand-heart` (Volunteer), `layers` (Batch), `book-open` (Program), `newspaper` (Berita), `help-circle` (FAQ), `mail` (Pesan Masuk).

Grup **Pengaturan** menambahkan halaman **Pengelola Admin** (`/admin/akun`) untuk melihat akun admin dan membuat akun baru, serta **Ubah Kata Sandi** (`/admin/akun/password`) untuk memperbarui kata sandi akun yang sedang masuk. Kedua tautan juga tersedia di flyout avatar.

- Lencana pada ikon grup Data menunjukkan jumlah Pesan Masuk yang belum dibaca.
- Ikon Tim, Konten, dan Data aktif bila route saat ini berada di salah satu sub-halamannya, termasuk halaman tambah, ubah, dan detail.

### 3.5 Aksi cepat "+" (opsional)

Tombol bulat 36 px di bawah logo, latar putih 16%, ikon putih. Hover membuka flyout berisi pintasan tambah data: Tambah Program, Tambah Berita, Tambah Anggota Tim, Tambah FAQ. Tiap pintasan menuju form tambah yang sudah ada di [ARCHITECTURE.md](ARCHITECTURE.md). Jika belum ingin dipakai, hapus tombol ini tanpa mengubah bagian lain.

### 3.6 Navigasi mobile (< 768 px)

Hover tidak ada di layar sentuh kecil, jadi navigasi memakai drawer penuh dengan label.

- Rail tersembunyi (translateX -100%). Tombol hamburger 44 px di pojok kiri atas area konten.
- Saat dibuka: overlay hitam 50% dan drawer selebar 280 px bergeser masuk dari kiri, berlatar `--admin-sidebar` (biru terang yang sama dengan rail).
- Isi drawer: logo dalam kotak putih + teks "Papua Future Project" dan subtitle "Admin Panel" (putih), lalu grup dari tabel 3.4. Label grup berupa teks kecil uppercase putih 70%. Grup yang punya sub-halaman tampil sebagai akordeon (ketuk untuk membuka). Item aktif: latar putih, teks dan ikon `--admin-primary`, border-radius 12 px. Item biasa: teks `--admin-sidebar-text`. Bagian bawah: profil admin dan tombol "Keluar".
- Klik overlay, tombol X, tombol hamburger, tautan navigasi, atau tekan Escape untuk menutup drawer.

### 3.7 Page Header

- Di atas area konten, di dalam wrapper dengan padding 24 px.
- Baris 1: Breadcrumb (contoh: Admin > Program > Ubah).
- Baris 2: Judul halaman (h1, 24 px, bold) di kiri. Tombol aksi utama di kanan (contoh: "+ Tambah Program"). Di kanan jauh ada tombol pill Secondary "Lihat Website" dengan ikon `external-link`. Khusus Ringkasan, di sebelahnya ada dropdown periode (bagian 5.1).
- Di mobile, tombol hamburger ada di kiri sebelum breadcrumb.

### 3.8 Varian B: rail melebar (opsional)

Alternatif bila flyout dirasa kurang cocok, mengikuti perilaku sidebar pada video referensi.

- Saat kursor masuk ke rail, seluruh rail melebar dari 64 px menjadi 240 px dalam 180 ms, menampilkan ikon + label putih. Konten **tidak bergeser**: rail melebar sebagai overlay dengan shadow `4px 0 24px rgba(15,23,42,0.25)` di sisi kanan.
- Grup berisi sub-halaman tampil sebagai daftar berindentasi di bawah labelnya. Deskripsi singkat tidak ditampilkan.
- Baris yang di-hover: latar `--admin-sidebar-hover`. Item aktif: latar putih, teks `--admin-primary`. Kursor keluar: rail menyempit kembali setelah 150 ms.
- Varian ini lebih cepat untuk berpindah halaman, tetapi tidak menampilkan deskripsi halaman. Pilih satu varian untuk seluruh panel dan jangan dicampur.

## 4. Komponen UI admin

### 4.0 Tipografi, jarak, dan bentuk

| Elemen | Ukuran / nilai |
| --- | --- |
| Font | Plus Jakarta Sans |
| Judul halaman (h1) | 24 px, bold |
| Subjudul halaman | 13 px, `--admin-text-muted` |
| Judul kartu | 14 px, bold |
| Teks isi dan sel tabel | 13 px |
| Label dan teks bantu | 12 px, `--admin-text-muted` |
| Keterangan kecil (waktu, pill) | 11 px |
| Angka besar (metrik, total) | 28 px, extra-bold (800) |
| Padding halaman | 24 px (16 px di mobile) |
| Lebar maksimal konten | 1280 px, rata tengah |
| Jarak antar kartu | 16 px |
| Radius kartu | 20 px |
| Radius input | 12 px |
| Radius tombol dan pill | penuh (999 px) |

### 4.1 Kartu (Card)

- Latar putih, border 1 px `--admin-border`, border-radius 20 px, shadow `0 1px 2px rgba(15,23,42,0.04)`.
- Padding dalam: 20 px (16 px di mobile). Jarak antar kartu: 16 px.
- **Header kartu** (opsional): baris atas berisi judul (14 px, bold) dengan teks bantu 12 px di bawahnya di kiri, dan kontrol (pencarian, filter, tombol kecil, tautan "Lihat semua") di kanan. Jarak ke isi 16 px.
- Kartu yang bisa diklik: hover mengubah border menjadi `--admin-primary`, transisi 150 ms.

### 4.2 Tabel data

- Di dalam kartu. Tabel memenuhi lebar kartu, padding sel 12 px 20 px.
- Header: tanpa latar penuh, teks uppercase 11 px semi-bold, tracking wide, `--admin-text-muted`, dengan border bawah 1 px.
- Baris: tinggi minimal 52 px, border bawah 1 px `#eef2f7`, hover latar `#f8fafc`.
- Foto orang berbentuk lingkaran 40 px. Thumbnail gambar border-radius 8 px.
- Kolom aksi: tombol pill kecil bergaya lembut (latar warna 10%, teks warna penuh): biru untuk "Ubah", merah untuk "Hapus", hijau untuk "Setujui".
- Di layar sempit: tabel gulung horizontal, atau berubah menjadi kartu bertumpuk.

### 4.3 Form

- Label: 13 px, font-weight 600, warna `--admin-text`, margin bawah 6 px.
- Input: tinggi 44 px, latar `#f8fafc`, border 1 px `--admin-border`, border-radius 12 px, padding kiri 14 px.
- Focus: border `--admin-primary`, ring 2 px `--admin-primary` 15%.
- Textarea: minimal tinggi 120 px.
- Select: sama dengan input, dengan ikon panah bawah.
- Error: teks 12 px merah `--admin-danger` di bawah field, border input berubah merah.
- Kolom pencarian di header kartu: input berbentuk pill, tinggi 36 px, ikon `search` di kiri.

### 4.4 Tombol

| Variasi | Latar | Teks | Border | Pemakaian |
| --- | --- | --- | --- | --- |
| Primary | `--admin-primary` | putih | - | Simpan, Tambah |
| Secondary | putih | `--admin-primary` | 1 px `--admin-primary` | Batal, Kembali, Lihat Website |
| Soft | `--admin-primary` 10% | `--admin-primary` | - | Aksi baris tabel (Ubah, Lihat) |
| Danger | `--admin-danger` | putih | - | Hapus (di dialog konfirmasi) |
| Ghost | transparan | `--admin-text-muted` | - | Aksi minor |

Semua tombol berbentuk pill (border-radius penuh), tinggi 40 px (atau 36 px untuk yang kecil), font-weight 600, transisi 150 ms. Hover Primary memakai `--admin-primary-hover`. Fokus keyboard: outline 2 px `--admin-secondary` dengan offset 2 px.

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
- Kartu: maks 400 px, border-radius 20 px, padding 24 px.
- Ikon peringatan bulat merah di atas.
- Teks menyebut nama item yang dihapus.
- Dua tombol pill: "Batal" (Secondary), "Hapus" (Danger).

### 4.9 Pagination

- Di footer kartu tabel, dipisah border atas 1 px. Kiri: "Menampilkan 1-10 dari 24". Kanan: tombol angka dan tombol panah kiri dan kanan.
- Tombol angka bulat 32 px. Halaman aktif: latar `--admin-primary`, teks putih.

### 4.10 Gallery manager (foto dokumentasi)

- Berada di kartu tersendiri dengan judul "Foto dokumentasi", di bawah form ubah program atau berita.
- Grid 4 kolom (2 di mobile). Tiap foto: thumbnail 1:1 dengan border-radius 12 px, keterangan di bawah (input kecil), tombol hapus (x) di pojok kanan atas.
- Tombol "+ Tambah foto" di akhir grid: area putus-putus 1:1, ikon kamera garis (`camera`).
- Sisa kuota ditampilkan di header kartu: "12 dari 30 foto".

### 4.11 Flyout menu

Komponen navigasi. Spesifikasi lengkap ada di bagian 3.2 dan 3.3. Ringkasnya: kartu putih 280 px dengan border tipis, border-radius 16 px, muncul di kanan rail biru saat hover atau fokus, berisi header grup, deskripsi, dan daftar sub-halaman beserta deskripsi satu barisnya.

### 4.12 Kartu metrik

- Memakai kartu 4.1, padding 20 px.
- Baris atas: label (12 px, `--admin-text-muted`) di kiri, dan badge pill opsional di kanan (contoh: "Perlu ditinjau" memakai gaya status Baru).
- Angka utama: 28 px, extra-bold, `--admin-text`.
- Teks bantu di bawah angka: 12 px, `--admin-text-muted` (contoh: "status Baru").
- Grafik mini (sparkline) opsional di kanan bawah kartu: garis `--admin-primary` 2 px, lebar 80 px, tinggi 32 px, tanpa sumbu. Hanya tampil bila datanya ada.
- Kartu bisa diklik menuju daftar terkait (aturan hover ada di 4.1).

### 4.13 Daftar aktivitas

- Di dalam kartu "Aktivitas Terbaru" dengan teks bantu "Pembaruan terkini".
- Tiap entri: ikon bulat 36 px (latar `--admin-primary` 10%, ikon garis `--admin-primary`), satu baris teks 13 px dengan nama atau judul tebal, dan waktu relatif 11 px `--admin-text-muted` ("2 jam lalu"). Jarak antar entri 4 px, padding entri 8 px, hover latar `#f8fafc` dengan radius 12 px.
- Ikon: `mail` untuk pesan masuk.
- Maksimal 6 entri. Tautan "Lihat semua" di dasar kartu menuju Pesan Masuk.

### 4.16 Pola halaman

**Pola Daftar** (Program, Berita, FAQ, Batch, Tim, Pesan):
1. Page Header (3.7).
2. Satu kartu besar. Header kartu: judul + jumlah data (pill abu "24 data") di kiri; kolom cari pill, filter, dan tombol ekspor atau tambah di kanan.
3. Tabel (4.2), lalu footer kartu berisi pagination (4.9).
4. Bila kosong: state kosong (bagian 6) di dalam kartu yang sama.

**Pola Form** (tambah dan ubah):
1. Page Header dengan breadcrumb.
2. Grid 12 kolom, jarak 16 px. Pada >= 1280 px: kolom utama 8 kolom (kartu isian utama) dan kolom samping 4 kolom (kartu Publikasi, Gambar, atau Pengaturan). Di bawah 1280 px: satu kolom, kartu samping berada setelah kartu utama.
3. Tiap kartu punya judul 14 px bold dan teks bantu opsional.
4. **Bilah aksi** menempel di bawah layar (sticky): latar putih, border atas 1 px, padding 12 px 24 px, tombol "Batal" (Secondary) dan "Simpan" (Primary) rata kanan. Mulai di kanan rail (offset kiri 64 px) dan z-index 30 (di bawah flyout).
5. Form sederhana (Batch, FAQ, Tentang Kami) memakai satu kolom dalam satu kartu selebar maksimal 720 px.

**Pola Detail** (Pesan):
1. Page Header dengan breadcrumb.
2. Grid yang sama dengan Pola Form: kolom utama (data lengkap atau isi pesan) dan kolom samping (kartu status, berkas, atau aksi).

## 5. Halaman per halaman

### 5.1 Ringkasan (`/admin`)

```
Selamat datang kembali, Admin               [Lihat Website]
Semoga harimu menyenangkan.

┌──────────────────────────┐ ┌──────────────────────────┐
│ Pesan belum dibaca       │ │ Berita terbit            │
└──────────────────────────┘ └──────────────────────────┘
┌─────────────────────────────────────────────────────────────────┐
│ Pesan Terbaru                                                   │
└─────────────────────────────────────────────────────────────────┘
```

- **Sapaan:** "Selamat datang kembali, [nama]" dan subjudul "Semoga harimu menyenangkan." Tidak ada pemilih rentang waktu.
- **Kartu ringkas:** pesan belum dibaca dan berita terbit beserta jumlah draf.
- **Pesan Terbaru** menampilkan pesan kontak terkini dan tautan menuju daftar Pesan Masuk.
- Ringkasan adalah halaman awal admin, bukan tempat mengelola pendaftaran. Semua link pendaftaran dan status buka/tutup hanya dikelola di halaman Batch.
- Tidak menampilkan jumlah, grafik, daftar, detail, status seleksi, atau ekspor pendaftar lokal lama.
- **Responsif:** kartu bertumpuk pada layar sempit; daftar Batch dapat digulir horizontal bila diperlukan.

### 5.2 Tentang Kami (`/admin/tentang`)

- **Tata letak:** Pola Form satu kolom (4.16). Tiga kartu terpisah (Cerita, Visi, Misi) dengan bilah aksi "Simpan" di bawah.
- Satu form berisi:
  - Cerita (textarea besar, minimal 200 px tinggi). Teks biasa, `**tebal**` diperbolehkan.
  - Visi (textarea satu paragraf).
  - Misi (textarea, satu butir per baris, dipisah baris baru).
- Tombol "Simpan" di bawah.
- Tidak ada tambah atau hapus, selalu satu baris.

### 5.3 Tim Penggerak (`/admin/tim/penggerak`)

- **Tata letak:** Pola Daftar (4.16). Form memakai Pola Form: kartu utama berisi nama, jabatan, deskripsi, bio panjang. Kartu samping berisi foto, checkbox Founder, dan nomor urut.
- Tabel: foto (lingkaran 40 px), nama, jabatan, urutan, tombol "Ubah" dan "Hapus".
- Kolom khusus: tanda bintang atau label "Founder" di samping nama untuk yang ditandai `is_founder`.
- Form tambah/ubah: nama, jabatan, deskripsi (textarea), bio panjang (textarea, opsional, untuk Beranda), foto (upload dengan pratinjau), checkbox "Founder", nomor urut.

### 5.4 Internship (`/admin/tim/internship`)

- **Tata letak:** tiap batch berupa satu kartu (4.1). Header kartu berisi nama batch dan tombol "+ Tambah Anggota", dengan tabel di dalamnya. Form anggota memakai Pola Form.
- Dikelompokkan per batch. Status pendaftaran dan URL dikelola hanya dari halaman Batch.
- Tabel per batch: foto (lingkaran 40 px), nama, universitas, peran, urutan, "Ubah", "Hapus".
- Bila belum ada batch bertipe Intern, tampilkan ajakan: "Buat batch Intern lebih dulu di halaman Batch."
- Form tambah/ubah anggota: batch (auto-fill), nama, universitas, peran/bidang, foto, urutan.

### 5.5 Volunteer (`/admin/tim/volunteer`)

Sama persis dengan Internship (termasuk tata letaknya), tetapi filter batch ke tipe `VOLUNTEER`. Status pendaftaran dan URL dikelola hanya dari halaman Batch.

### 5.6 Batch (`/admin/batch`)

- **Tata letak:** Pola Daftar (4.16). Ini satu-satunya halaman untuk mengatur link dan status pendaftaran.
- Tabel: nama batch, jenis (Internship/Relawan), status pendaftaran, sakelar buka/tutup, "Ubah", dan "Hapus".
- Sakelar hanya dapat dibuka setelah link pendaftaran HTTP/HTTPS valid disimpan. Status terbuka tampil di website sebagai tombol Daftar.
- Hapus ditolak hanya jika batch masih memiliki anggota tim. Arsip pendaftar formulir lokal lama tetap tersimpan dan dilepas dari batch yang dihapus.
- Form tambah/ubah: nama, jenis, dan link pendaftaran apa pun (opsional). Link kosong membuat batch tetap tertutup.

### 5.7 Program (`/admin/program`)

- **Tata letak:** Pola Daftar. Form memakai Pola Form: kartu utama (judul, slug, ringkasan, konten, kegiatan, mitra, informasi program). Kartu samping "Publikasi" (kategori, unggulan, terbit, nomor urut) dan kartu "Thumbnail". Gallery manager (4.10) berupa kartu penuh di bawah.
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

- **Tata letak:** Pola Daftar. Form memakai Pola Form: kartu utama (judul, slug, ringkasan, isi). Kartu samping "Publikasi" (kategori, tanggal terbit, penulis, terbitkan) dan kartu "Gambar sampul". Kolom khusus Penghargaan dan Liputan Media tampil di kartu "Detail tambahan" sesuai kategori. Gallery manager berupa kartu penuh di bawah.
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

- **Tata letak:** Pola Daftar. Form satu kolom dalam satu kartu.
- Tabel: pertanyaan (dipotong 60 karakter), urutan, status (pill: Terbit/Sembunyi), "Ubah", "Hapus".
- Form tambah/ubah: pertanyaan (input teks), jawaban (textarea), nomor urut, checkbox "Tampilkan".

### 5.10 Pendaftar dan Form Pendaftaran

Kedua halaman ini sudah dihapus dari panel. Admin tidak mengelola pertanyaan, jawaban, status seleksi, atau ekspor pendaftar melalui website. Pengaturan link formulir dan buka/tutup pendaftaran cukup dilakukan per batch pada halaman Batch; jawaban baru dikelola oleh layanan pada link tersebut.

### 5.12 Pesan Masuk (`/admin/pesan`)

- **Tata letak:** daftar memakai Pola Daftar. Detail memakai Pola Detail: kolom utama berisi isi pesan; kolom samping berisi kartu kontak (nama, email, WhatsApp, tanggal) dan tombol balas serta hapus.
- Tabel: pengirim, email, ringkasan pesan (dipotong 50 karakter), tanggal, status (pill: Belum dibaca/Sudah dibaca), "Lihat".
- Baris belum dibaca: font-weight bold, titik biru di kiri.
- Halaman detail:
  - Nama, email, WhatsApp (jika ada), tanggal.
  - Isi pesan lengkap (teks biasa).
  - Tombol: "Balas lewat Email" (mailto:), "Balas lewat WA" (wa.me, jika ada nomor), "Hapus" (merah, konfirmasi dialog).
  - Pesan ditandai sudah dibaca saat halaman ini dibuka.

### 5.13 Login (`/admin/login`)

- Tidak ada rail maupun drawer. Layout khusus auth.
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

| Breakpoint | Navigasi | Isi |
| --- | --- | --- |
| >= 1280 px | Rail 64 px + flyout hover | Dua kolom pada Ringkasan dan Pola Form/Detail (8 + 4 kolom). Konten maks 1280 px, rata tengah. |
| 768-1279 px | Rail 64 px + flyout hover (sama seperti desktop) | Satu kolom. Kartu metrik 2x2. Tabel tetap tabel, gulung horizontal bila kolom melebihi layar. |
| < 768 px | Drawer hamburger 280 px dengan label dan akordeon | Satu kolom, padding halaman 16 px. Tabel berubah menjadi kartu bertumpuk atau gulung horizontal. Kartu metrik satu kolom. Bilah aksi form tetap sticky di dasar layar, tanpa offset rail. |

Pada perangkat sentuh yang lebarnya >= 768 px (misalnya tablet), rail tetap tampil dan flyout dibuka dengan ketukan pertama (lihat bagian 3.3).
