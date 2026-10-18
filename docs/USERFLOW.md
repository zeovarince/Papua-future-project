# USERFLOW

Peta halaman dan alur pengguna website Papua Future Project, untuk Public dan Admin. Kebutuhan ada di [PRD.md](PRD.md). Route lengkap ada di [ARCHITECTURE.md](ARCHITECTURE.md) bagian 6. Desain panel admin ada di [DESIGN-ADMIN.md](DESIGN-ADMIN.md).

## 1. Peta halaman

### Publik

```
/                      Beranda (hero, sekilas, founder, penghargaan, angka impact,
                       preview program, preview berita, peta wilayah)
/tentang               Tentang Kami (cerita, perjalanan, visi dan misi, Tim Penggerak,
                       Internship per batch, Volunteer per batch)
/program               Daftar program, strip angka, "Dari Lapangan"
/program/{slug}        Detail program (kegiatan, mitra, informasi, Dokumentasi)
/berita                Terbaru, semua berita (filter kategori), Diakui dan Diliput
/berita/{slug}         Detail berita (isi, bagikan, informasi, Dokumentasi, berita lain)
/bergabung             Ajakan, manfaat, langkah, form pendaftaran (bila ada batch dibuka)
/bergabung/berhasil    Konfirmasi setelah mendaftar
/kontak                Temukan kami, peta, form pesan, logo mitra, FAQ
```

### Admin

```
/admin/login
/admin                         Ringkasan
/admin/tentang                 Cerita, visi, misi
/admin/tim/penggerak           Tim Penggerak
/admin/tim/internship          Intern per batch
/admin/tim/volunteer           Relawan per batch
/admin/program                 Daftar, tambah, ubah (termasuk foto dokumentasi)
/admin/berita                  Daftar, tambah, ubah (termasuk foto dokumentasi)
/admin/faq                     Daftar, tambah, ubah
/admin/batch                   Daftar batch, buat, ubah, buka atau tutup
/admin/pendaftar               Daftar pendaftar, filter, ekspor
/admin/pendaftar/{id}          Detail pendaftar
/admin/pesan                   Pesan masuk dari form kontak
/admin/pesan/{id}              Detail pesan
```

## 2. Alur Public 1: menjelajah situs

```mermaid
flowchart TD
  A["Masuk ke Beranda"] --> B["Baca hero, sekilas, founder, angka impact"]
  B --> C{"Ingin tahu lebih banyak?"}
  C -- "Program" --> D["/program lalu /program/{slug}"]
  C -- "Berita" --> E["/berita lalu /berita/{slug}"]
  C -- "Tim dan cerita" --> F["/tentang"]
  C -- "Ikut terlibat" --> G["/bergabung"]
  C -- "Hubungi" --> H["/kontak"]
  D --> I["Baca detail, lihat Dokumentasi, pilih program lain"]
  E --> J["Baca isi, lihat Dokumentasi, bagikan tautan, baca berita lain"]
```

Detail program dan berita memuat bagian Dokumentasi dari foto yang diunggah admin. Bila belum ada foto, bagian itu disembunyikan.

## 3. Alur Public 2: mendaftar sebagai relawan atau intern

```mermaid
flowchart TD
  A["Buka /bergabung"] --> B["Baca ajakan, manfaat, dan 4 langkah"]
  B --> C{"Ada batch yang dibuka?"}
  C -- "Tidak" --> D["Tampil pesan pendaftaran belum dibuka dan tautan Instagram"]
  C -- "Ya" --> E["Pilih batch"]
  E --> F["Isi data diri, ketersediaan dan minat, motivasi"]
  F --> G["Unggah foto dan CV"]
  G --> H{"Validasi di layar lolos?"}
  H -- "Tidak" --> F
  H -- "Ya" --> I["Kirim"]
  I --> J{"Pemeriksaan di server"}
  J -- "Batch sudah ditutup" --> K["Pesan: batch ini sudah ditutup. Isian teks tetap ada"]
  J -- "Email sudah terdaftar di batch ini" --> L["Pesan: email ini sudah mendaftar di batch ini"]
  J -- "Isian atau file ditolak" --> M["Pesan: jelaskan field atau file mana dan alasannya"]
  J -- "Lolos" --> N["Simpan data dan file"]
  N --> O["/bergabung/berhasil"]
```

## 4. Alur Public 3: mengirim pesan lewat form kontak

```mermaid
flowchart TD
  A["Buka /kontak"] --> B["Lihat Instagram, email, kantor, peta"]
  B --> C["Isi nama, email, WhatsApp bila mau, dan pesan"]
  C --> D["Kirim Pesan"]
  D --> E{"Pemeriksaan di server"}
  E -- "Isian tidak valid" --> F["Galat di bawah field, isian tetap ada"]
  F --> C
  E -- "Terlalu sering mengirim" --> G["Pesan: tunggu sebentar lalu coba lagi"]
  E -- "Lolos" --> H["Pesan tersimpan"]
  H --> I["Kembali ke /kontak dengan pesan sukses"]
```

Pertanyaan umum dibaca di bagian FAQ pada halaman yang sama. Tiap pertanyaan membuka dan menutup jawabannya.

## 5. Alur Admin 1: masuk dan keluar

```mermaid
flowchart TD
  A["Buka URL /admin apa pun"] --> B{"Sudah login?"}
  B -- "Belum" --> C["/admin/login"]
  C --> D{"Email dan kata sandi benar?"}
  D -- "Tidak" --> E["Pesan galat umum, jumlah percobaan dibatasi"]
  E --> C
  D -- "Ya" --> F["Kembali ke halaman yang dituju, atau /admin"]
  B -- "Sudah" --> F
  F --> G["Bekerja di panel lewat sidebar"]
  G --> H["Keluar lewat sidebar"]
  H --> C
```

## 6. Alur Admin 2: mengelola konten

Pola yang sama dipakai untuk Program, Berita dan Kegiatan, FAQ, Tim Penggerak, Intern, Relawan, dan Batch.

```mermaid
flowchart TD
  A["Buka daftar di sidebar"] --> B{"Aksi"}
  B -- "Tambah" --> C["Form baru"]
  B -- "Ubah" --> D["Form berisi data lama"]
  B -- "Hapus" --> E["Dialog konfirmasi menyebut nama item"]
  C --> F{"Valid?"}
  D --> F
  F -- "Tidak" --> G["Galat di bawah field yang salah"]
  G --> C
  F -- "Ya" --> H["Simpan, toast, kembali ke daftar"]
  E -- "Konfirmasi" --> I["Hapus data dan file, toast"]
  E -- "Batal" --> A
  H --> J["Halaman publik langsung diperbarui"]
  I --> J
```

Khusus Tentang Kami: admin tidak menambah atau menghapus. Admin langsung membuka satu form berisi cerita, visi, dan misi, lalu menyimpan.

## 7. Alur Admin 3: mengelola foto dokumentasi

```mermaid
flowchart TD
  A["Buka halaman ubah program atau berita"] --> B["Bagian Foto dokumentasi"]
  B --> C{"Aksi"}
  C -- "Tambah foto" --> D["Pilih satu atau banyak foto"]
  D --> E{"Total foto lebih dari 30 atau ada file ditolak?"}
  E -- "Ya" --> F["Pesan: sebut jumlah dan file yang ditolak beserta alasannya"]
  E -- "Tidak" --> G["Foto diubah ke WebP dan disimpan, toast"]
  C -- "Ubah keterangan atau urutan" --> H["Simpan, toast"]
  C -- "Hapus foto" --> I["Dialog konfirmasi, lalu foto dan file dihapus"]
  G --> J["Foto tampil di bagian Dokumentasi halaman publik"]
  H --> J
  I --> J
```

Program atau berita baru harus disimpan lebih dulu sebelum foto bisa ditambahkan. Form tambah menampilkan catatan tentang hal ini.

## 8. Alur Admin 4: membuka dan menutup pendaftaran

```mermaid
flowchart TD
  A["Buka /admin/batch, atau header kelompok di halaman Internship atau Volunteer"] --> B{"Batch sudah ada?"}
  B -- "Belum" --> C["Tambah batch: pilih tipe Intern atau Relawan dan beri nama"]
  C --> D["Batch dibuat dalam keadaan tertutup"]
  B -- "Ya" --> E["Geser toggle Pendaftaran dibuka"]
  D --> E
  E --> F["Toast: Pendaftaran Batch X dibuka atau ditutup"]
  F --> G["/bergabung langsung mengikuti status baru"]
```

## 9. Alur Admin 5: meninjau pendaftar

```mermaid
flowchart TD
  A["/admin/pendaftar"] --> B["Filter tipe, batch, status, atau cari nama"]
  B --> C["Buka detail pendaftar"]
  C --> D["Lihat data lengkap dan foto inline"]
  D --> E{"Perlu lihat CV?"}
  E -- "Lihat CV" --> F["PDF terbuka di tab baru"]
  E -- "Download CV" --> G["PDF terunduh"]
  E -- "Tidak" --> H["Ubah status: Baru, Ditinjau, Diterima, Ditolak"]
  F --> H
  G --> H
  H --> I["Simpan status, toast"]
```

## 10. Alur Admin 6: mengekspor data

```mermaid
flowchart TD
  A["/admin/pendaftar"] --> B["Atur filter bila perlu"]
  B --> C["Menu Ekspor data"]
  C --> D{"Format"}
  D -- "Excel atau CSV" --> E["Unduh file dengan semua kolom dan tautan foto serta CV"]
  D -- "PDF" --> F{"Hasil filter 100 pendaftar atau kurang?"}
  F -- "Tidak" --> G["Pesan: persempit filter, misalnya per batch"]
  G --> B
  F -- "Ya" --> H["Unduh laporan PDF dengan foto inline dan tautan CV"]
```

## 11. Alur Admin 7: membaca pesan masuk

```mermaid
flowchart TD
  A["Sidebar: Pesan masuk (lencana jumlah belum dibaca)"] --> B["Daftar pesan, yang belum dibaca ditandai"]
  B --> C["Buka detail pesan"]
  C --> D["Pesan ditandai sudah dibaca"]
  D --> E{"Aksi"}
  E -- "Balas lewat email atau WhatsApp" --> F["Tautan mailto atau wa.me dari data pengirim"]
  E -- "Hapus" --> G["Dialog konfirmasi, lalu pesan dihapus"]
```

## 12. Kasus pinggir

| Situasi | Perilaku yang diharapkan |
| --- | --- |
| Tidak ada batch dibuka | `/bergabung` tidak menampilkan form. Tampil pesan, dan bagian ajakan, manfaat, serta langkah tetap terlihat. |
| Lebih dari satu batch dibuka | Pengunjung memilih batch. Tipe (Intern atau Relawan) ditampilkan di samping nama batch. |
| Batch ditutup saat pengunjung mengisi | Pengiriman ditolak dengan pesan jelas. Isian teks tetap, file perlu dipilih ulang. |
| Pengunjung menekan kirim dua kali | Tombol nonaktif selama proses. Server tetap menolak duplikat lewat aturan email per batch. |
| File lebih besar dari batas | Ditolak di server dengan pesan yang menyebut ukuran file dan batasnya. Bila ukuran bisa dibaca di layar, ditolak lebih awal. |
| File berekstensi PDF tetapi isinya bukan PDF | Ditolak dengan pesan "File CV harus berupa PDF yang valid". |
| Pendaftar berusia di bawah 17 tahun | Ditolak dengan pesan yang menyebut batas usia. |
| Pendaftar non-mahasiswa tanpa asal universitas | Diterima. Asal universitas hanya wajib untuk Mahasiswa dan Fresh Graduate. |
| Sesi admin habis saat mengisi form | Admin diarahkan ke login. Setelah login, kembali ke halaman yang sama. Isian yang belum disimpan hilang, jadi form panjang menampilkan peringatan sebelum meninggalkan halaman. |
| Admin menghapus batch yang punya data | Ditolak. Pesan menyebut jumlah pendaftar dan anggota tim, dan menawarkan "Tutup batch". |
| Admin membuka tautan file dari Excel tanpa login | Diarahkan ke login, lalu kembali ke file setelah masuk. |
| Admin menghapus program atau berita yang punya foto | Dialog konfirmasi menyebut jumlah foto yang ikut terhapus. File fisik ikut dihapus. |
| Foto galeri melebihi 30 | Unggahan ditolak seluruhnya dengan pesan yang menyebut sisa kuota. |
| Program atau berita berstatus draf | Tidak tampil di halaman publik, tidak muncul di preview Beranda, dan URL detailnya menjawab 404 untuk pengunjung. |
| Konten kosong (belum ada program, berita, FAQ, atau tim) | Halaman publik menyembunyikan section yang kosong. Panel admin menampilkan layar kosong yang mengajak menambah. |
| Kurang dari tiga berita Penghargaan | Section Penghargaan di Beranda menampilkan sebanyak yang ada. Bila nol, section disembunyikan. |
| Pendaftar belum ada | Daftar pendaftar menampilkan layar kosong. Tombol ekspor nonaktif. |
| Pengunjung mengirim pesan kontak terlalu sering | Ditolak dengan pesan untuk menunggu. Tidak ada pesan yang disimpan. |
| Founder belum ditandai | Section profil founder di Beranda disembunyikan. |
