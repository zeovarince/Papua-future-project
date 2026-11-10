# Deploy ke DirectAdmin

Panduan ini menyiapkan PFP di shared hosting DirectAdmin dengan PHP 8.4 dan MariaDB. Pengerjaan dilakukan lewat DirectAdmin File Manager/FTP dan phpMyAdmin; SSH pada akun yang diberikan tidak aktif, jadi deploy tidak bergantung pada terminal server.

## Status akun dari screenshot

- Control panel di port `2222` adalah DirectAdmin, bukan halaman website atau dashboard WordPress.
- PHP Selector memperlihatkan PHP `8.4` sudah dipilih. Screenshot Installatron masih menyebut PHP 8.3.35 untuk instalasi WordPress; itu status aplikasi WordPress dan belum membuktikan versi PHP aktif untuk seluruh domain. Verifikasi versi yang dipilih pada domain utama dan subdomain staging.
- Opsi PHP menunjukkan `upload_max_filesize = 2M`. Aplikasi menerima CV sampai 5 MB, jadi ubah batas ini menjadi minimal `6M`; `post_max_size` minimal `8M` (disarankan `16M`). Simpan perubahan di PHP Selector/PHP Options.
- Dump yang dikirim menyebut MariaDB `10.6.20`, memenuhi target proyek MariaDB 10.6+.
- Screenshot File Manager/Installatron menunjukkan WordPress sudah terpasang di `public_html`. Jangan menimpa file atau database itu selama persiapan; lakukan staging dan backup lebih dulu.

Versi PHP, ekstensi, dan web server yang tersedia diatur oleh penyedia hosting. DirectAdmin dapat menyediakan pilihan PHP per domain bila fiturnya diaktifkan, tetapi versi dan ekstensi yang tersedia tetap bergantung pada konfigurasi host.

## 1. Susunan file yang disarankan

Jaga source code, `.env`, vendor, log, dan file pendaftar di luar document root. Salin **isi** folder `public/` ke document root domain atau subdomain.

```text
/home/ACCOUNT/
├── pfp-app/                                  # privat; bukan di bawah public_html
│   ├── app/
│   ├── bin/
│   ├── config/
│   ├── database/
│   ├── storage/
│   ├── vendor/
│   ├── bootstrap.php
│   ├── composer.json
│   └── .env
└── domains/DOMAIN/public_html/               # document root
    ├── index.php                              # dari isi repo public/
    ├── .htaccess
    └── assets/, uploads/, favicon.ico, ...    # aset publik
```

`public/index.php` sudah mendukung layout ini: ia mencari folder privat bernama `pfp-app` di atas document root. Jika host menggunakan document root khusus, variabel server `PFP_APP_ROOT` dapat diarahkan ke path absolut aplikasi bila provider mengizinkan. Jangan membuat salinan `.env` di `public_html`.

## 2. Rencana cutover ke domain utama

Subdomain staging **opsional**. Screenshot website saat ini menampilkan contoh bawaan WordPress (“Hello world!” dan “Sample Page”); sesuai keputusan pemilik proyek, PFP dapat dipasang langsung pada domain utama dengan mengganti isi `public_html`.

Sebelum mengganti file, pastikan salinan dump database WordPress tersedia di komputer. Dump SQL yang ada tidak menyimpan file website; bila ingin bisa memulihkan WordPress sepenuhnya, unduh juga arsip file `public_html` atau backup melalui Installatron. Simpan backup di komputer, bukan di folder publik. Jangan mulai menghapus/mengganti `public_html` sebelum source PFP, `vendor/`, konfigurasi `.env`, dan database baru PFP siap.

Buat database PFP tersendiri lalu impor [`../database/pfp.sql`](../database/pfp.sql) ke database kosong itu. Jangan mengimpor dump WordPress ke database PFP. Saat cutover, ganti hanya isi document root dengan isi folder `public/` PFP; jangan menaruh seluruh source code atau `.env` di `public_html`.

Jika masih ingin mencoba PFP tanpa mengganti domain utama, barulah buat subdomain staging. Itu pilihan untuk menguji lebih dulu, bukan persyaratan teknis.

## 3. Siapkan paket aplikasi

1. Di komputer lokal, pastikan PHP CLI 8.4 dan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`, `dom`, `simplexml`, `xmlwriter`, dan `xmlreader` aktif. `exif` disarankan untuk orientasi foto.
2. Di root proyek jalankan `composer install --no-dev --optimize-autoloader`. Sertakan `vendor/` yang dihasilkan dalam paket upload; folder ini tidak dilacak Git.
3. Buat arsip upload berisi kode aplikasi dan `vendor/`, tanpa `.git/`, `.env` lokal, `node_modules/`, `tests/`, atau dokumen internal. Folder `public/` disalin terpisah ke document root staging.
4. Lewat File Manager/FTP, buat `pfp-app` di luar `domains/DOMAIN/public_html` dan unggah kode privat ke sana. Unggah isi `public/` ke document root staging, bukan folder `public` tambahan.
5. Buat `.env` baru di `pfp-app/` berdasarkan nama variabel di `docs/ARCHITECTURE.md`; isi nilai production langsung di hosting. Gunakan `APP_ENV=production`, `APP_DEBUG=false`, HTTPS untuk `APP_URL`, `ADMIN_PATH` acak, `APP_KEY` acak baru, kredensial DB khusus, dan `STORAGE_DIR` absolut menuju `pfp-app/storage`.
6. Pastikan folder privat yang perlu ditulis PHP (`storage/cache`, `storage/logs`, `storage/sessions`, `storage/applications`) dapat ditulis oleh akun hosting. Mulai dengan permission dari File Manager; jangan gunakan `777`.

Jika dependensi Composer lokal membutuhkan ekstensi yang tidak tersedia di Laragon, aktifkan ekstensi itu pada PHP CLI lokal dahulu. Screenshot DirectAdmin memperlihatkan PHP 8.4, tetapi belum menunjukkan daftar semua ekstensi aktif.

## 4. Buat database PFP

1. DirectAdmin → **MySQL Management**: buat database dan pengguna baru khusus PFP, lalu beri hak akses database tersebut.
2. Buka database PFP di phpMyAdmin, pilih **Import**, lalu impor hanya [`../database/pfp.sql`](../database/pfp.sql). File tunggal ini membuat skema untuk database baru dan menambahkan pembaruan skema PFP lama yang diperlukan tanpa menghapus data. Tidak perlu mengimpor file SQL lain atau menjalankan migrasi CLI setelah impor penuh ini.
3. Atur `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASS` di `.env`. Beberapa provider menambahkan prefix akun pada nama database dan pengguna; pakai nama persis dari DirectAdmin.
4. Buat admin awal dengan `password_hash()` di komputer lokal, lalu masukkan email, nama, dan hash ke tabel `admin_users` melalui phpMyAdmin. Jangan simpan password mentah di SQL, README, atau Git. Setelah berhasil masuk, atur kata sandi melalui fitur profil admin.

### Tentang dump SQL WordPress yang tersedia

Dump `papua_bzwu1_...sql` adalah backup **database WordPress** yang terpasang di domain. Nama tabel berawalan `sbui_` dan berisi tabel WordPress seperti posts, options, users, comments, serta pengaturan multisite. Dump itu bukan database PFP.

File tersebut berisi struktur tabel dan data WordPress, termasuk konten/settings, alamat email, hash kata sandi, salt autentikasi, serta token sesi. Hash bukan kata sandi plaintext, tetapi salt/token tetap rahasia dan file harus dijaga privat. Dump SQL juga tidak berisi file kode WordPress, plugin/theme, atau media di `wp-content/uploads`.

Dump memiliki perintah `DROP TABLE IF EXISTS`, sehingga impor dapat mengganti tabel bernama sama di database tujuan. Gunakan hanya untuk memulihkan WordPress ke database WordPress terpisah; **jangan impor ke database PFP**. Karena sebagian isi backup dibagikan di percakapan, jika WordPress tetap aktif, ganti password akun admin WordPress dan refresh salts/sesi dari instalasi WordPress. Simpan salinan backup secara privat.

## 5. Pengaturan domain dan PHP

1. Di Domain Setup/PHP Selector pilih PHP 8.4 untuk domain staging. Terapkan perubahan, kemudian cek PHP yang benar-benar dipakai domain staging (halaman diagnostik sementara atau `phpinfo()` yang segera dihapus setelah diperiksa). Versi yang terlihat di Installatron tidak menggantikan pengecekan domain.
2. Aktifkan ekstensi yang dibutuhkan: `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`, `dom`, `simplexml`, `xmlwriter`, `xmlreader`; aktifkan `exif` bila tersedia.
3. Atur `upload_max_filesize` minimal `6M` karena batas aplikasi untuk CV 5 MB; `post_max_size` minimal `8M` (disarankan `16M` untuk menampung data form dan foto). Nilai sekarang pada screenshot adalah `2M` untuk upload, jadi unggah CV besar akan gagal sampai batasnya dinaikkan.
4. Atur `display_errors=Off` untuk production. Biarkan `memory_limit` mengikuti nilai host kecuali ekspor PDF besar gagal; ekspor dibatasi aplikasi.
5. Aktifkan SSL untuk domain staging sebelum mengandalkan HTTPS/redirect. Screenshot menunjukkan status Force SSL masih `No`; pastikan sertifikat valid dahulu.
6. Pastikan `.htaccess` dan rewrite URL didukung pada web server domain. DirectAdmin dapat berjalan dengan Apache, Nginx, LiteSpeed, atau OpenLiteSpeed; bila rewrite `.htaccess` tidak diterapkan, minta provider mengaktifkan dukungan yang sesuai atau memberi aturan rewrite untuk server tersebut.

## 6. Buat admin tanpa SSH

Karena SSH/Terminal pada detail akun disebut nonaktif, jangan mengandalkan `php bin/create-admin.php` atau cron sampai provider mengonfirmasi CLI tersedia. Buat hash lokal dengan PHP 8.4:

```powershell
$plain = Read-Host "Password admin" -AsSecureString
$ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($plain)
try {
    $password = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr)
    $env:PFP_ADMIN_PASSWORD = $password
    php -r 'echo password_hash(getenv("PFP_ADMIN_PASSWORD"), PASSWORD_DEFAULT), PHP_EOL;'
} finally {
    Remove-Item Env:PFP_ADMIN_PASSWORD -ErrorAction SilentlyContinue
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr)
}
```

Salin hanya hasil hash ke query `INSERT` di phpMyAdmin. Jangan menempelkan atau menyimpan password mentah pada halaman SQL. Hapus variabel PowerShell setelahnya (blok di atas melakukannya).

## 7. Pemeriksaan sebelum cutover

- Domain staging menjalankan PHP 8.4 dan semua ekstensi wajib aktif.
- Koneksi database sukses dan konten awal PFP tampil.
- Beranda, Tentang Kami, daftar/detail program, daftar/detail berita, Kontak, dan Bergabung tampil dengan CSS/JavaScript dan gambar.
- Dari Batch, simpan tautan pendaftaran HTTP/HTTPS dan buka satu batch percobaan; pastikan tombol Daftar membuka form tersebut di tab baru. Tutup kembali batch setelah pemeriksaan.
- Periksa CRUD konten, foto dokumentasi, tambah/ubah/hapus batch (batch beranggota harus ditolak), toggle pendaftaran, sesi logout, dan halaman 404.
- Cek `storage/logs/` dan log error domain; jangan tinggalkan file diagnostik publik.
- Pastikan backup WordPress file dan database dapat ditemukan sebelum mengganti domain utama.

## Batasan deployment akun ini

- Pengguna database maksimal 5 menurut panel; buat satu database PFP tambahan, jangan menghabiskan kuota tanpa perlu.
- Screenshot DirectAdmin menampilkan disk/bandwidth tidak terbatas, sedangkan surat aktivasi menyebut kuota tertentu. Konfirmasi kuota efektif dengan provider sebelum mengunggah aset atau melakukan cutover.
- Tidak ada akses SSH yang tercantum. Pengelolaan Composer dilakukan lokal, upload melalui File Manager/FTP, database melalui phpMyAdmin. Cron/integrity-check hanya dipasang jika menu cron tersedia dan provider memberi path PHP CLI.
- Persiapan ini tidak login ke server dan belum melakukan deploy. Semua perubahan produksi dilakukan pemilik akun setelah staging diverifikasi.
