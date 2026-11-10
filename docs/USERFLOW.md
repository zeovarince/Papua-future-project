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
/bergabung             Ajakan, manfaat, langkah, dan daftar jalur/batch terbuka
Formulir eksternal     Dibuka pada tab baru setelah pengunjung memilih batch
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
/admin/batch                   CRUD batch, link formulir eksternal, buka/tutup pendaftaran per batch
/admin/program                 Daftar, tambah, ubah (termasuk foto dokumentasi)
/admin/berita                  Daftar, tambah, ubah (termasuk foto dokumentasi)
/admin/faq                     Daftar, tambah, ubah
/admin/batch                   Daftar batch, buat, ubah, buka atau tutup
/admin/pesan                   Pesan masuk dari form kontak
/admin/pesan/{id}              Detail pesan
/admin/akun                    Daftar akun admin dan buat admin baru
/admin/akun/password           Ubah kata sandi akun sendiri
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
  B --> C{"Ada batch berstatus dibuka dengan link pendaftaran valid?"}
  C -- "Tidak" --> D["Tampilkan status pendaftaran ditutup"]
  C -- "Ya" --> E["Pilih batch yang dibuka"]
  E --> F["Daftar membuka formulir dari link batch itu pada tab baru"]
  F --> G["Isi, kirim, dan lihat konfirmasi pada layanan tersebut"]
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
  G --> H{"Kelola akun?"}
  H -- "Buat admin" --> I["/admin/akun: isi nama, email, dan kata sandi awal"]
  I --> G
  H -- "Ubah kata sandi sendiri" --> J["/admin/akun/password: verifikasi kata sandi saat ini"]
  J --> G
  H -- "Keluar" --> K["Keluar lewat sidebar"]
  K --> C
```

Admin baru dibuat dari halaman **Pengaturan → Pengelola Admin**. Kata sandi awal minimal 12 karakter dan hanya disimpan sebagai hash. Penggantian kata sandi memerlukan verifikasi kata sandi lama.

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

## 8. Alur Admin 4: mengatur pendaftaran per batch

```mermaid
flowchart TD
  A["Buka /admin/batch"] --> B{"Batch sudah ada?"}
  B -- "Belum" --> C["Tambah batch: pilih Internship atau Relawan"]
  B -- "Ya" --> D["Pilih Ubah untuk memasukkan atau memperbarui link pendaftaran"]
  C --> E["Isi nama batch dan link pendaftaran"]
  D --> F["Simpan perubahan"]
  E --> F
  F --> G{"Link HTTP/HTTPS valid dan tersimpan?"}
  G -- "Tidak" --> H["Pendaftaran tetap tertutup"]
  G -- "Ya" --> I["Aktifkan toggle pada baris batch"]
  I --> J["Website menampilkan tombol Daftar pada batch itu"]
  J --> K["Nonaktifkan toggle untuk menutup pendaftaran di website"]
```

Tautan dan status buka/tutup disimpan per batch. Internship dan Relawan dapat dibuka bersamaan, dan setiap batch dapat memakai layanan formulir berbeda. Tombol Daftar hanya tampil jika link valid tersedia dan toggle batch aktif. Menutup batch di website menyembunyikan tombol; Admin juga perlu menutup penerimaan respons di layanan formulir jika form tidak boleh menerima kiriman lagi.

## 9. Data pendaftar

Pendaftaran baru berlangsung di layanan eksternal pada link batch. Daftar jawaban, status seleksi, foto, CV, dan ekspor dikelola dari layanan tersebut; panel admin website tidak menyediakan halaman pendaftar atau ekspor untuk data dari layanan eksternal. Tabel lokal dari formulir website versi terdahulu tetap dipertahankan sebagai arsip dan pengaman relasi batch, tetapi bukan bagian alur admin saat ini.

## 10. Alur Admin 5: membaca pesan masuk

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
| Tidak ada batch dibuka | `/bergabung` menampilkan status pendaftaran ditutup. Bagian ajakan, manfaat, dan langkah tetap terlihat. |
| Lebih dari satu batch dibuka | Pengunjung memilih batch. Tipe (Intern atau Relawan) ditampilkan di samping nama batch. |
| Link pendaftaran batch belum diisi | Toggle tidak dapat dibuka dan tombol Daftar tidak tampil di website. |
| Batch ditutup setelah tab formulir dibuka | Website menyembunyikan tautan bagi kunjungan berikutnya. Tab formulir yang sudah terbuka tetap mengikuti status penerimaan respons di layanan tersebut. |
| Sesi admin habis saat mengisi form | Admin diarahkan ke login. Setelah login, kembali ke halaman yang sama. Isian yang belum disimpan hilang, jadi form panjang menampilkan peringatan sebelum meninggalkan halaman. |
| Admin menghapus batch yang punya anggota tim | Ditolak agar relasi anggota tidak rusak. Arsip pendaftar lokal lama tetap tersimpan dengan `batch_id` kosong bila batch dihapus. |
| Admin menghapus program atau berita yang punya foto | Dialog konfirmasi menyebut jumlah foto yang ikut terhapus. File fisik ikut dihapus. |
| Foto galeri melebihi 30 | Unggahan ditolak seluruhnya dengan pesan yang menyebut sisa kuota. |
| Program atau berita berstatus draf | Tidak tampil di halaman publik, tidak muncul di preview Beranda, dan URL detailnya menjawab 404 untuk pengunjung. |
| Konten kosong (belum ada program, berita, FAQ, atau tim) | Halaman publik menyembunyikan section yang kosong. Panel admin menampilkan layar kosong yang mengajak menambah. |
| Kurang dari tiga berita Penghargaan | Section Penghargaan di Beranda menampilkan sebanyak yang ada. Bila nol, section disembunyikan. |
| Pengunjung mengirim pesan kontak terlalu sering | Ditolak dengan pesan untuk menunggu. Tidak ada pesan yang disimpan. |
| Founder belum ditandai | Section profil founder di Beranda disembunyikan. |
