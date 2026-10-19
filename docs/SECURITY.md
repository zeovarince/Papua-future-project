# SECURITY

Panduan keamanan website Papua Future Project (PHP native MVC, MySQL, hosting cPanel dengan Git). Dokumen ini menjadi acuan saat menulis kode, menyiapkan server, dan merespons insiden. Bila aturan di sini bertabrakan dengan kenyamanan, aturan ini yang dipakai.

Dokumen terkait: [PRD.md](PRD.md), [ARCHITECTURE.md](ARCHITECTURE.md) bagian 9, 10, dan 13, [RULES.md](RULES.md) bagian 5, [STEP.md](STEP.md), [SCHEMA.md](SCHEMA.md).

## 1. Apa yang dilindungi dan dari siapa

| Aset | Contoh | Ancaman utama |
| --- | --- | --- |
| Data pribadi pendaftar | Nama, tanggal lahir, WhatsApp, email, domisili, foto, CV | Kebocoran lewat celah kode, file yang bisa ditebak, ekspor yang dikirim sembarangan |
| Akun admin | Login panel | Tebak sandi, sandi bocor, sesi dicuri |
| Isi situs | Program, berita, FAQ, halaman publik | Judol dan konten spam yang disisipkan, defacement |
| File unggahan | Foto galeri, foto dan CV pendaftar | Unggahan berbahaya (webshell), pemindaian jalur file |
| Akun di luar kode | cPanel, registrar domain, FTP, SSH, MySQL | Pengambilalihan akun atau domain |
| Ketersediaan | Situs bisa dibuka | Banjir kiriman form, unggahan raksasa, hosting kehabisan memori |

Penyerang yang paling mungkin bukan peretas yang menyasar PFP secara khusus. Yang paling sering datang adalah bot pemindai otomatis dan pelaku judol yang mencari situs dengan sandi lemah atau celah umum. Pertahanan di bawah disusun untuk itu.

## 2. Prinsip

1. **Tolak dulu, izinkan kemudian.** Rute admin, file privat, dan aksi ubah data tertutup kecuali ada pemeriksaan yang eksplisit.
2. **Jangan percaya masukan apa pun,** termasuk nama file, `Content-Type`, header, parameter URL, dan isi `$_POST`.
3. **Escape saat keluar, validasi saat masuk.** Keduanya wajib, tidak saling menggantikan.
4. **Hak seminimal mungkin.** Pengguna MySQL hanya untuk database ini. Folder yang bisa ditulis hanya `storage/` dan `public/uploads/`.
5. **Rahasia tidak ada di kode dan tidak ada di Git.**
6. **Berlapis.** Alamat admin rahasia tidak menggantikan login kuat. Login kuat tidak menggantikan CSRF. Satu lapis boleh gagal, lapis lain menahan.
7. **Bisa dipulihkan.** Cadangan yang sudah diuji pemulihannya lebih penting daripada fitur tambahan.

## 3. Aturan kode PHP

### 3.1 Database (PDO)

```php
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = :slug AND is_published = 1');
$stmt->execute(['slug' => $slug]);
```

1. Semua query memakai prepared statement. Tidak ada penyambungan string ke SQL.
2. Nama kolom dan arah urutan untuk `ORDER BY` diambil dari daftar putih: `$column = $allowed[$sort] ?? 'created_at';`.
3. Simpan hanya field yang ditetapkan Validator. Jangan `INSERT` atau `UPDATE` langsung dari `$_POST`.
4. Pengguna MySQL tidak dipakai bersama aplikasi lain.

### 3.2 Output, XSS, dan URL

1. Semua output di View melewati `e()` (`htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`).
2. Isi berita dan program adalah teks. Hanya paragraf dan `**tebal**` yang dirender, oleh `Text::render()` yang meng-escape lebih dulu.
3. Nilai yang masuk ke `href` atau `src` dari database harus lolos pemeriksaan skema. `javascript:` di `href` tetap menjadi XSS walaupun sudah di-escape.

```php
function safe_external_url(?string $url): ?string
{
    if ($url === null || $url === '') {
        return null;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    if (!in_array($scheme, ['http', 'https'], true) || filter_var($url, FILTER_VALIDATE_URL) === false) {
        return null;
    }

    return $url;
}
```

Dipakai untuk `posts.source_url`. Tautan keluar memakai `target="_blank" rel="noopener noreferrer"`.

4. Redirect "kembali ke halaman semula" setelah login hanya boleh menuju jalur di dalam panel:

```php
function safe_admin_redirect(?string $target): string
{
    $base = admin_path();

    if ($base === null || $target === null) {
        return $base ?? '/';
    }

    $isInternal = ($target === $base || str_starts_with($target, $base . '/'))
        && !str_contains($target, '//')
        && !str_contains($target, '\\')
        && preg_match('/[\x00-\x1f]/', $target) !== 1;

    return $isInternal ? $target : $base;
}
```

5. Jangan pernah menampilkan ulang parameter query di halaman. Parameter yang dipakai hanya `?kategori=` dengan daftar putih nilai.

### 3.3 Login dan sesi

```php
// Sekali di bootstrap, sebelum session_start().
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_name('pfp_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => true,   // false hanya di lokal tanpa HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
```

```php
// Di AuthService::attempt(), setelah menemukan atau tidak menemukan admin.
static $dummyHash = null;
$dummyHash ??= password_hash('pfp-dummy-password', PASSWORD_DEFAULT);

$admin = $adminModel->findByEmail(strtolower(trim($email)));
$hash = $admin['password_hash'] ?? $dummyHash;
$valid = password_verify($password, $hash) && $admin !== null;

if (!$valid) {
    // catat percobaan gagal, kembalikan pesan yang sama untuk email salah dan sandi salah
    return false;
}

if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
    $adminModel->updatePasswordHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
}

session_regenerate_id(true);
$_SESSION['admin_id']  = (int) $admin['id'];
$_SESSION['last_seen'] = time();
```

```php
// Di AuthMiddleware: sesi habis bila diam lebih dari 2 jam.
if (!isset($_SESSION['admin_id']) || time() - ($_SESSION['last_seen'] ?? 0) > 7200) {
    session_destroy();
    // redirect ke login, simpan tujuan awal lewat safe_admin_redirect()
}

$_SESSION['last_seen'] = time();
```

Aturan:

1. Sandi disimpan dengan `password_hash()` dan diperiksa dengan `password_verify()`. Tidak ada sandi dalam teks biasa di mana pun, termasuk seed dan log.
2. Menjalankan `password_verify()` terhadap hash tiruan bila email tidak ditemukan membuat waktu respons sama, sehingga email admin tidak bisa ditebak dari kecepatan respons.
3. Pesan galat sama untuk email salah dan sandi salah.
4. Logout memakai POST dengan CSRF, menghapus data sesi, dan menghancurkan sesi.
5. Sandi seed wajib diganti setelah login pertama.
6. Pembatas laju login: 5 kegagalan per 15 menit per kombinasi IP dan email (bagian 3.9).

### 3.4 CSRF

```php
function csrf_token(): string
{
    return $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_check(?string $token): bool
{
    return isset($_SESSION['_csrf'])
        && is_string($token)
        && hash_equals($_SESSION['_csrf'], $token);
}
```

1. Setiap form POST memuat `csrf_field()`. `CsrfMiddleware` memeriksa token sebelum controller jalan.
2. Aksi yang mengubah data tidak boleh lewat GET, termasuk logout, hapus, toggle batch, dan ubah status.
3. Cookie sesi memakai `SameSite=Lax` sebagai lapis kedua.

### 3.5 Otorisasi, IDOR, dan mass assignment

1. Semua rute admin (kecuali login) memakai `AuthMiddleware`. Controller admin tidak boleh mengandalkan "halaman ini tidak ditautkan dari mana pun".
2. ID berurutan (`/pendaftar/12`) aman selama rute dilindungi sesi. File pendaftar memakai `file_token` acak sehingga jalurnya tidak bisa ditebak.
3. Parameter `{group}`, `{kind}`, dan `{format}` di URL divalidasi ke daftar putih (`penggerak|internship|volunteer`, `foto|cv`, `csv|xlsx|pdf`). Nilai lain menjawab 404.
4. Validator menetapkan field yang boleh masuk. Field lain diabaikan.
5. Aturan yang tidak bisa dijaga MySQL (kecocokan `member_group` dengan `batch_type`, hanya satu founder, batch terbuka saat mendaftar) dijaga di Validator dan Model, lihat [SCHEMA.md](SCHEMA.md) bagian 5.

### 3.6 Unggah file

Jangan percaya `$_FILES['x']['type']` atau ekstensi. Periksa isi file, ukuran, dan dimensi sebelum GD membukanya, supaya server hosting tidak kehabisan memori.

```php
function validate_image(string $tmpPath, int $size, int $maxBytes): ?string
{
    if (!is_uploaded_file($tmpPath) || $size > $maxBytes) {
        return 'Foto terlalu besar atau tidak valid.';
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath);

    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        return 'Format foto harus JPG, PNG, atau WebP.';
    }

    $info = getimagesize($tmpPath);

    if ($info === false || $info[0] * $info[1] > 24_000_000) {
        return 'Dimensi foto tidak valid atau terlalu besar.';
    }

    return null;
}

function looks_like_pdf(string $tmpPath): bool
{
    $head = file_get_contents($tmpPath, false, null, 0, 5);

    return $head === '%PDF-'
        && (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath) === 'application/pdf';
}
```

```php
function reencode_to_webp(string $tmpPath, string $destPath, int $maxSide): bool
{
    $data = file_get_contents($tmpPath);
    $image = $data === false ? false : imagecreatefromstring($data);

    if ($image === false) {
        return false;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $scale = min(1, $maxSide / max($width, $height));

    if ($scale < 1) {
        $resized = imagescale($image, (int) round($width * $scale), (int) round($height * $scale));

        if ($resized === false) {
            return false;
        }

        $image = $resized;
    }

    return imagewebp($image, $destPath, 82);
}
```

Aturan:

1. Foto selalu disimpan ulang lewat GD. Ini membuang metadata EXIF dan lokasi GPS, dan menggagalkan file yang menyamar sebagai gambar.
2. Untuk foto galeri admin, baca orientasi EXIF dan putar gambar lebih dulu bila ekstensi `exif` ada, karena orientasi hilang setelah disimpan ulang.
3. Nama file di disk dibuat sistem: `bin2hex(random_bytes(16))` atau `file_token`. Nama asli dari pengguna tidak dipakai di jalur mana pun.
4. File dipindahkan dengan `move_uploaded_file()`. Folder tujuan dibuat dengan izin 0750.
5. Batas: foto pendaftar 2 MB, CV 5 MB (PDF), foto galeri 8 MB sebelum diproses, maksimal 30 foto per item, 20 file per kali unggah.
6. `public/uploads/` memblokir eksekusi PHP (`.htaccess` di [ARCHITECTURE.md](ARCHITECTURE.md) bagian 14). Folder itu hanya boleh berisi gambar.
7. CV dan foto pendaftar berada di `STORAGE_DIR` di luar `public_html`.
8. CV adalah PDF dari orang tak dikenal. Sajikan dengan `nosniff`, buka di browser yang selalu diperbarui, dan pertimbangkan pemindaian ClamAV bila hosting menyediakannya.

### 3.7 Menyajikan file privat

```php
function serve_private_file(
    string $storageDir,
    string $relativePath,
    string $mime,
    string $asciiDownloadName,
    bool $download
): never {
    $base = realpath($storageDir);
    $full = realpath($storageDir . '/' . $relativePath);

    if ($base === false || $full === false
        || !str_starts_with($full, $base . DIRECTORY_SEPARATOR)
        || !is_file($full)) {
        http_response_code(404);
        exit;
    }

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . (string) filesize($full));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header('Content-Disposition: ' . ($download ? 'attachment' : 'inline')
        . '; filename="' . $asciiDownloadName . '"');

    readfile($full);
    exit;
}
```

1. Cek sesi admin lebih dulu (`AuthMiddleware`), lalu cari `Application` dari `id` dan bentuk jalur dari `file_token` di database.
2. `$asciiDownloadName` dibuat dari slug ASCII (huruf, angka, tanda hubung), tidak pernah dari nama file unggahan.
3. Jawaban 404, bukan 403, untuk file yang tidak ada atau tidak boleh diakses.

### 3.8 Ekspor dan PDF

Nama, domisili, dan motivasi pendaftar yang diawali `=`, `+`, `-`, atau `@` bisa dijalankan sebagai rumus saat admin membuka file di Excel (injeksi rumus).

```php
function safe_cell(string $value): string
{
    return preg_match('/^[=+\-@\t\r]/', $value) === 1 ? "'" . $value : $value;
}
```

1. Semua kolom teks dari pengguna di CSV dan XLSX melewati `safe_cell()`. Di PhpSpreadsheet, tulis sebagai string dengan `setCellValueExplicit($cell, $value, DataType::TYPE_STRING)`.
2. Dompdf: `isRemoteEnabled` dimatikan. Foto disisipkan sebagai data URI dari thumbnail lokal.
3. Satu PDF maksimal 100 pendaftar, dan `memory_limit` cukup (256M).
4. File ekspor berisi data pribadi. Jangan dikirim lewat chat atau email tanpa perlindungan, dan hapus dari perangkat setelah dipakai.

### 3.9 Pembatas laju dan spam

```php
function rate_limit_allow(PDO $pdo, string $bucket, string $identifier, int $max, int $windowSeconds): bool
{
    $since = date('Y-m-d H:i:s', time() - $windowSeconds);

    $count = $pdo->prepare(
        'SELECT COUNT(*) FROM rate_limits WHERE bucket = :b AND identifier = :i AND created_at >= :s'
    );
    $count->execute(['b' => $bucket, 'i' => $identifier, 's' => $since]);

    if ((int) $count->fetchColumn() >= $max) {
        return false;
    }

    $pdo->prepare('INSERT INTO rate_limits (bucket, identifier) VALUES (:b, :i)')
        ->execute(['b' => $bucket, 'i' => $identifier]);

    return true;
}

$identifier = RateLimiter::identifier($ip . '|' . $extra);
```

| Bucket | Batas (usulan) | Identifier |
| --- | --- | --- |
| `login` | 5 gagal per 15 menit | IP dan email |
| `join` | 5 per jam | IP |
| `contact` | 5 per jam | IP |

1. Identifier berupa HMAC dengan `APP_KEY`, bukan IP mentah. Tambahkan `APP_KEY` (32 byte heksadesimal acak) ke `.env`.
2. IP klien dari `REMOTE_ADDR`. Jangan percaya `X-Forwarded-For` atau `CF-Connecting-IP` kecuali situs memang berada di belakang proxy tepercaya.
3. Form publik memakai kolom honeypot yang disembunyikan dengan CSS. Bila terisi, tolak tanpa pesan khusus.
4. Pasang captcha seperti Cloudflare Turnstile hanya bila spam tetap masuk.
5. `bin/cleanup.php` menghapus baris `rate_limits` yang lebih tua dari satu hari lewat cron.

### 3.10 Header keamanan

```php
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
```

1. Tambahkan `Strict-Transport-Security: max-age=31536000` setelah HTTPS dipastikan stabil di semua halaman.
2. Tambahkan `Content-Security-Policy` setelah tidak ada lagi CSS dan JS inline. Contohnya ada di [ARCHITECTURE.md](ARCHITECTURE.md) bagian 10. Peta OpenStreetMap membutuhkan `frame-src https://www.openstreetmap.org`.
3. Halaman panel admin menambahkan `X-Robots-Tag: noindex, nofollow` dan `Cache-Control: no-store`.

### 3.11 Galat dan log

1. `display_errors` mati di produksi. Pasang `set_exception_handler` yang menulis ke `storage/logs/app.log` dan menampilkan halaman 500 yang ramah, tanpa jalur server, query, atau isi konfigurasi.
2. Catat ke `storage/logs/security.log`: login berhasil dan gagal (IP sebagai hash), penghapusan data, ekspor, dan ubah status pendaftar. Satu baris per kejadian, berisi waktu, jenis, `admin_id`, dan id objek.
3. Jangan catat: sandi, token CSRF atau sesi, isi pesan kontak, data pendaftar, dan isi `$_POST`.
4. Folder `storage/logs/` tidak berada di `public_html`.

## 4. Alamat panel admin rahasia

1. Panel berada di bawah awalan dari `ADMIN_PATH` di `.env`, misalnya `/panel-k7x2m9qf4tbw`. Helper `admin_path()` ada di `app/Core/helpers.php`, sedangkan `url()` dan redirect otomatis mengubah jalur internal `/admin` menjadi awalan tersebut.
2. Buat nilainya acak, bukan kata yang bisa ditebak seperti `secret-panel`:

```bash
php -r "echo 'panel-' . bin2hex(random_bytes(6));"
```

3. Di produksi nilainya minimal 12 karakter dan memuat angka. Bila kosong atau lemah, rute admin tidak didaftarkan dan situs publik tetap jalan.
4. Jalur `/admin` tidak terdaftar dan menjawab 404 seperti halaman lain yang tidak ada.
5. Tidak ada tautan ke panel di situs publik, di `robots.txt`, atau di `sitemap.xml`.
6. Alamat rahasia mengurangi pemindaian otomatis, tetapi bukan pengganti login. Pembatas laju, sandi kuat, CSRF, dan sesi aman tetap wajib.
7. Mengganti alamat cukup dengan mengubah `ADMIN_PATH` di `.env` server, tanpa deploy ulang.

## 5. Mencegah judol dan konten sisipan

### 5.1 Cara masuknya yang paling umum

1. Sandi cPanel, FTP, SSH, atau admin yang bocor atau ditebak. Ini penyebab paling sering.
2. Celah di kode: unggahan yang bisa dieksekusi, SQL injection, `include` dari input pengguna.
3. Aplikasi atau skrip lain di akun hosting yang sama.
4. Admin diambil alih, lalu menulis "berita" spam lewat CRUD biasa.
5. Subdomain atau record DNS yang terlupakan dan diambil alih orang lain.

### 5.2 Pencegahan

1. **Kunci akses akun.** 2FA di cPanel dan registrar domain. Sandi unik di setiap akun. Hapus akun FTP yang tidak dipakai. Bila deployment lewat Git, matikan FTP, atau pakai SFTP dengan kunci SSH. Periksa cron job dan `authorized_keys` sekali di awal.
2. **Kode tidak bisa ditulis oleh web.** Kode aplikasi berada di `pfp-app` di luar `public_html`. Di `public_html` hanya ada `index.php` dan aset. Hanya `storage/` dan `public/uploads/` yang perlu bisa ditulis.
3. **Folder unggahan hanya untuk gambar.** Eksekusi PHP diblokir. File selain gambar di `uploads/` adalah tanda bahaya.
4. **Larang pola berbahaya di kode.** Tidak ada `eval`, `unserialize` atas input pengguna, `extract($_POST)`, atau `include` dengan jalur dari input. Di `php.ini` set `allow_url_include=Off` dan, bila hosting mengizinkan, `disable_functions=exec,shell_exec,system,passthru,proc_open,popen`.
5. **Sinkronkan ulang dari Git setiap deploy** supaya file tambahan yang disusupkan ke `public_html` hilang. Pakai opsi `--delete` di `rsync` untuk `public/`, hanya bila `public_html` benar-benar hanya berisi aplikasi ini:

```yaml
    - /usr/bin/rsync -a --delete --include='uploads/.htaccess' --exclude='uploads/*' --exclude='.well-known/' --exclude='cgi-bin/' --exclude='error_log' public/ $WEB_DIR/
```

6. **Jaga akun admin.** Pakai sandi panjang dari pengelola sandi, pembatas laju login, dan alamat panel rahasia. Peristiwa login, ekspor, perubahan status, dan penghapusan dicatat tanpa menyimpan data pribadi. Aktifkan 2FA pada cPanel dan registrar.
7. **Jangan biarkan konten spam masuk indeks.**
   - Halaman yang tidak ada menjawab status 404 sungguhan.
   - Parameter query tidak pernah ditampilkan ulang.
   - Setiap halaman publik punya `<link rel="canonical">` yang dibentuk dari `APP_URL`, bukan dari `$_SERVER['HTTP_HOST']`.
   - Tautan keluar hanya `http://` atau `https://` (bagian 3.2).
8. **Bersihkan DNS.** Hapus subdomain dan record yang tidak dipakai, misalnya subdomain uji setelah selesai.
9. **Blokir berkas sensitif yang tersasar** di `public_html`. Tambahkan ke `public/.htaccess`:

```apache
<FilesMatch "\.(env|sql|bak|old|zip|tar|gz|log|md|lock)$|^error_log$">
  Require all denied
</FilesMatch>
```

### 5.3 Deteksi dini

1. **Pemeriksa integritas.** `bin/integrity-check.php` membandingkan hash file dengan kondisi bersih, mencari file bukan gambar di `uploads/`, dan memindai pola kode berbahaya serta kata kunci judol. Mode pemindaian dan pemeriksaan rutin tidak mencetak apa pun bila bersih, jadi cron cPanel hanya mengirim email saat ada masalah. Mode `--init` mencetak konfirmasi setelah membuat acuan.

```
php bin/integrity-check.php --scan   # pemindaian awal tanpa membuat acuan
php bin/integrity-check.php --init   # buat acuan setelah meninjau deployment yang bersih
php bin/integrity-check.php          # periksa terhadap acuan
```

   Jangan jalankan `--init` di working tree yang belum diperiksa. Skrip menolak membuat acuan bila menemukan pola mencurigakan, tautan simbolik, atau berkas non-gambar di uploads. Sesudah deploy resmi, tinjau perubahan dan perbarui acuan secara sengaja.

   Cron di cPanel (jalur PHP CLI berbeda antar hosting, cek dengan `which ea-php84`):

```
0 */6 * * * /usr/local/bin/ea-php84 /home/USERNAME/pfp-app/bin/integrity-check.php
```

   Tambahkan `cd $APP_DIR && /usr/local/bin/ea-php84 bin/integrity-check.php --init` sebagai tugas terakhir di `.cpanel.yml` hanya jika pipeline deploy tersebut memang dipercaya dan deployment telah ditinjau. Batasan: acuan disimpan di server yang sama, jadi penyerang dengan akses penuh bisa menimpanya. Ini pendeteksi dini, bukan pengganti sandi kuat dan 2FA.

2. **Periksa database.** Jalankan sesekali di phpMyAdmin:

```sql
SELECT id, title FROM posts
WHERE CONCAT_WS(' ', title, excerpt, content) REGEXP 'slot|gacor|togel|judi|maxwin|casino';

SELECT id, title FROM programs
WHERE CONCAT_WS(' ', title, summary, body) REGEXP 'slot|gacor|togel|judi|maxwin|casino';

SELECT id, email, created_at FROM admin_users;
```

3. **Cek cloaking.** Judol sering hanya tampil ke Googlebot atau pengunjung dari Google, sehingga pemilik tidak melihatnya:

```bash
curl -s -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" \
  -e "https://www.google.com/" https://papuafutureproject.org/ | grep -iE "slot|gacor|togel|judi"
```

   Bandingkan dengan `curl` biasa. Hasil berbeda berarti kemungkinan ada cloaking.

4. **Google Search Console.** Daftarkan domain dengan verifikasi DNS, bukan file atau meta tag, karena penyerang bisa menambahkan dirinya sebagai pemilik lewat file yang disusupkan. Periksa "Security issues", "Manual actions", "Pages" (cari URL aneh), dan daftar pemilik di "Users and permissions". Cek rutin dengan pencarian `site:papuafutureproject.org slot` dan `site:papuafutureproject.org gacor`.

## 6. Data pribadi pendaftar

Data pendaftar mencakup tanggal lahir, WhatsApp, email, domisili, foto, dan CV. Ini data pribadi yang harus diperlakukan hati-hati. Bagian ini berisi usulan teknis. **Saya bukan penasihat hukum.** Konfirmasi kewajiban sesuai UU Pelindungan Data Pribadi (UU No. 27 Tahun 2022) ke pihak yang memahaminya sebelum peluncuran.

Usulan yang perlu disetujui pengurus PFP dan dimasukkan ke [PRD.md](PRD.md):

1. **Kotak persetujuan** di form pendaftaran dan form kontak, dengan kalimat singkat tentang apa yang dikumpulkan, untuk apa, siapa yang bisa melihat, dan berapa lama disimpan.
2. **Halaman Kebijakan Privasi** (`/kebijakan-privasi`) berisi hal yang sama, dengan kontak untuk meminta akses atau penghapusan data.
3. **Kumpulkan seperlunya.** Tinjau lagi apakah setiap field memang dipakai. Tanggal lahir dipakai untuk batas usia.
4. **Aturan retensi** (usulan, tetapkan bersama pengurus): data dan file pendaftar berstatus Ditolak dihapus 6 bulan setelah batch ditutup. Pendaftar Diterima disimpan selama kegiatan dan 12 bulan sesudahnya. Pesan kontak dihapus setelah 12 bulan. Buat `bin/purge-expired.php` yang dijalankan lewat cron.
5. **Akses terbatas.** Hanya admin. Jangan membagikan akun admin dan jangan mengirim ekspor lewat grup chat.
6. **Cadangan berisi data pribadi.** Simpan di luar server, batasi aksesnya, dan hapus cadangan lama.
7. **Foto anak.** Foto anak di galeri hanya dengan izin orang tua atau pendamping, tanpa nama lengkap dan titik lokasi rumah (lihat [RULES.md](RULES.md) bagian 2).
8. **Bila terjadi kebocoran,** catat kapan, data apa, dan berapa orang, lalu beri tahu pihak terdampak dan pengurus PFP secepatnya.

## 7. Akun dan infrastruktur

| Area | Aturan |
| --- | --- |
| Registrar domain | 2FA aktif, kunci transfer domain aktif, email pemulihan milik organisasi yang dikelola lebih dari satu orang tepercaya. |
| cPanel dan hosting | 2FA aktif, sandi unik, hapus akun yang tidak dipakai, aktifkan AutoSSL. |
| FTP dan SSH | Matikan FTP bila deployment lewat Git. Pakai SFTP atau SSH dengan kunci. |
| MySQL | Satu pengguna khusus untuk database ini. Jangan memakai pengguna dan sandi cPanel. |
| Repositori Git | Repositori privat, deploy key SSH khusus baca, tidak ada rahasia di riwayat commit. |
| PHP | Gunakan PHP 8.4 atau lebih baru sesuai `composer.json` dan [ARCHITECTURE.md](ARCHITECTURE.md). Periksa ketersediaan versi yang sama untuk situs dan cron cPanel. |
| `php.ini` | `display_errors=Off`, `expose_php=Off`, `allow_url_include=Off`, `session.cookie_httponly=1`, `session.cookie_secure=1`, `upload_max_filesize=8M`, `post_max_size=32M`, `memory_limit=256M`. |
| Izin file | `public_html`: folder 755, file 644 (bawaan cPanel). `pfp-app`: folder 750, file 640, `.env` 600. Tidak ada 777. |
| HTTPS | Paksa HTTPS di `.htaccess`, tambahkan HSTS setelah stabil. |
| Cadangan | Cadangan database harian dan folder `storage/` serta `public/uploads/`, disimpan di luar server, uji pemulihan sebelum peluncuran. |
| Dependensi | Pasang hanya yang perlu. Commit `composer.lock`. Jalankan `composer audit` sebelum rilis. Unduh GSAP dari sumber resmi dan catat versinya. |
| Pemantauan | Baca `security.log` dan log galat cPanel secara berkala. Aktifkan notifikasi email cron untuk `integrity-check.php`. |

Perintah izin file (ganti `USERNAME`):

```bash
find /home/USERNAME/pfp-app -type d -exec chmod 750 {} \;
find /home/USERNAME/pfp-app -type f -exec chmod 640 {} \;
chmod 600 /home/USERNAME/pfp-app/.env
```

## 8. Variabel lingkungan terkait keamanan

| Nama | Fungsi |
| --- | --- |
| `ADMIN_PATH` | Awalan URL panel admin yang dirahasiakan (bagian 4). |
| `APP_KEY` | Kunci acak 32 byte heksadesimal untuk HMAC identifier pembatas laju. Buat dengan `php -r "echo bin2hex(random_bytes(32));"`. |
| `APP_ENV` | `production` mengaktifkan pemeriksaan ketat `ADMIN_PATH` dan mematikan tampilan galat. |

## 9. Daftar uji keamanan manual

Jalankan sebelum peluncuran dan setelah perubahan besar. Semua harus lolos.

| Uji | Yang diharapkan |
| --- | --- |
| Di production, buka `/admin` dan `/wp-admin` | 404. Jalur `ADMIN_PATH` yang benar menampilkan login. |
| Buka URL panel atau URL file pendaftar tanpa login | Diarahkan ke login, tidak ada file terkirim. |
| Kirim form POST tanpa token CSRF | Ditolak. |
| Ketik `' OR 1=1 --` dan `"><script>alert(1)</script>` di kolom pencarian dan form | Tidak ada galat SQL, skrip tidak berjalan, teks tampil apa adanya. |
| Unggah file `.php` yang diganti namanya menjadi `.jpg` sebagai foto | Ditolak. |
| Unggah gambar berukuran piksel sangat besar | Ditolak sebelum diproses, server tidak kehabisan memori. |
| Unggah PDF palsu (teks biasa bernama `.pdf`) sebagai CV | Ditolak. |
| Buka `https://domain/uploads/` dan jalur skrip di dalamnya | Tidak ada daftar folder, skrip tidak dieksekusi. |
| Buka `https://domain/.env` dan `https://domain/error_log` | 403 atau 404. |
| Isi nama pendaftar dengan `=HYPERLINK("http://contoh.com")`, lalu ekspor XLSX dan CSV | Tampil sebagai teks, bukan rumus. |
| Login salah 6 kali berturut-turut | Percobaan ke-6 ditolak sementara. |
| Login lalu logout, tekan tombol Kembali | Tidak bisa mengakses halaman panel tanpa login ulang. |
| Ubah parameter `{group}` atau `{kind}` menjadi nilai aneh | 404. |
| Parameter redirect login diisi `https://contoh.com` | Diabaikan, kembali ke panel. |
| `curl` dengan User-Agent Googlebot | Isi halaman sama dengan pengunjung biasa. |

Pemeriksaan otomatis yang dianjurkan: `composer audit`, PHPStan, PHP_CodeSniffer, dan pemindaian dasar dengan OWASP ZAP terhadap situs uji (bukan produksi).

## 10. Bila situs sudah terkena

1. **Tahan dulu.** Aktifkan halaman perawatan atau ubah izin sementara agar konten spam tidak terus disajikan.
2. **Ganti semua sandi:** cPanel, FTP, SSH, MySQL (dan perbarui `.env`), akun admin, dan registrar domain. Cabut sesi yang aktif dan kunci SSH yang tidak dikenal.
3. **Cari pintu masuknya** lewat log akses mentah di cPanel: tanggal file pertama yang berubah, lalu permintaan di sekitar waktu itu. Menambal tanpa tahu pintunya berarti kena lagi.
4. **Bersihkan.** Hapus isi `public_html` dan deploy ulang dari Git. Periksa `.htaccess`, `.user.ini`, folder `uploads/`, cron job, akun FTP, kunci SSH, record DNS, dan tabel `admin_users`. Jalankan kueri di bagian 5.3.
5. **Pulihkan reputasi.** Di Search Console, ajukan peninjauan ulang dan minta penghapusan URL spam dari indeks. Periksa daftar pemilik.
6. **Bila data pendaftar mungkin terbuka,** ikuti butir 8 di bagian 6.
7. **Catat kejadiannya:** waktu, dampak, penyebab, dan perbaikan. Perbarui dokumen ini bila ada pelajaran baru.

## 11. Kapan tiap bagian dikerjakan

Urutan kerja rinci ada di [STEP.md](STEP.md). Pemetaan ke langkahnya:

| Step | Pekerjaan keamanan |
| --- | --- |
| 1 dan 2 | Semua tautan memakai `url()`, tidak ada inline `style` dan `script` (syarat CSP), semua output lewat `e()`. |
| 3 | PDO aman, sesi aman, login, `password_hash`, CSRF, `AuthMiddleware`, `ADMIN_PATH`, pembatas laju, `security.log`. |
| 4 sampai 8 | Validator per form, whitelist field, `safe_external_url()`, validasi parameter rute. |
| 9 | Unggah file aman (bagian 3.6), file privat (3.7), pendaftaran dengan transaksi, kotak persetujuan. |
| 10 | Ekspor aman (3.8). |
| 12 | Header keamanan dan CSP, daftar uji manual (bagian 9), `composer audit`, `integrity-check.php`, kebijakan privasi dan retensi. |
| 13 | 2FA akun hosting dan registrar, izin file, `.cpanel.yml` dengan `--delete`, cron, cadangan dan uji pemulihan, Search Console. |

## 12. Status penerapan dan pekerjaan tersisa

Kontrol aplikasi yang sudah diterapkan pada kode:

1. HMAC identifier rate limit dengan `APP_KEY`, jalur admin rahasia di production, header anti-cache dan noindex untuk panel, `Permissions-Policy`, serta HSTS hanya saat production menerima koneksi HTTPS.
2. Log keamanan untuk login berhasil/gagal, perubahan status pendaftar, ekspor, dan penghapusan konten. Log tidak menyimpan email, sandi, token, pesan, atau data pendaftar.
3. Pemeriksa integritas CLI, pemblokiran berkas sensitif di `public/.htaccess`, dan pemeriksaan ukuran piksel sebelum foto diproses. Foto tim diperkecil dan dikonversi ke WebP agar metadata EXIF tidak ikut tersimpan.
4. `APP_KEY` dan `ADMIN_PATH` sudah dijelaskan di [ARCHITECTURE.md](ARCHITECTURE.md); README memuat cara menyiapkan kunci serta memulai pemeriksaan integritas.

Hal yang belum diterapkan karena memerlukan konfigurasi hosting atau keputusan pengurus:

1. Aktifkan `ADMIN_PATH` acak di `.env` production dan atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, HTTPS, serta izin berkas pada cPanel.
2. CSP ditunda sampai semua style/script inline dan dependensi CDN dibersihkan; CSP ketat sekarang akan merusak halaman.
3. Kebijakan privasi, persetujuan pengumpulan data, dan jadwal retensi memerlukan persetujuan pengurus PFP sebelum penghapusan otomatis dibuat.
4. 2FA cPanel/registrar, pemeriksaan cron/backup, serta uji keamanan manual harus dilakukan di akun hosting dan lingkungan staging.
