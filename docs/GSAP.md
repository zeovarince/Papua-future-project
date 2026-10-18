# GSAP: Panduan Implementasi Animasi

Panduan animasi GSAP 3 + ScrollTrigger untuk seluruh halaman publik dan panel admin Papua Future Project. Animasi ditambahkan di Step 11 ([STEP.md](STEP.md)) setelah semua halaman berfungsi.

Dokumen terkait: [PRD.md](PRD.md), [ARCHITECTURE.md](ARCHITECTURE.md) bagian 3 (letak file), [RULES.md](RULES.md) bagian aksesibilitas.

## 1. Prinsip

1. **Halaman harus berfungsi tanpa JavaScript.** Semua konten sudah ada di HTML. Animasi hanya menambah kesan, bukan menyembunyikan informasi. Elemen baru diberi keadaan awal animasi setelah GSAP berhasil dimuat.
2. **Hormati preferensi pengguna.** Jika `prefers-reduced-motion: reduce`, matikan semua animasi dan tampilkan konten langsung tanpa delay.
3. **GSAP disajikan dari server sendiri**, bukan dari CDN luar. File disimpan di `public/assets/js/vendor/gsap/`.
4. **Modular.** Tiap animasi adalah satu file JS di `public/assets/js/motion/`. File diimpor sesuai halaman, bukan satu bundle global yang memuat semuanya.
5. **Performa.** Hanya animasikan `transform` dan `opacity`. Tidak menganimasi `width`, `height`, `margin`, `padding`, atau `top`/`left`. Batasi jumlah ScrollTrigger aktif.

## 2. File dan struktur

```
public/assets/js/
├─ vendor/gsap/
│  ├─ gsap.min.js            GSAP core
│  └─ ScrollTrigger.min.js   Plugin ScrollTrigger
│
└─ motion/
   ├─ tokens.js              Konstanta durasi, easing, dan jarak
   ├─ index.js               Entry point: cek reduced-motion, daftarkan plugin, muat modul per halaman
   ├─ hero-intro.js           Animasi masuk hero (semua halaman dengan hero)
   ├─ count-up.js             Counter angka (section Impact di Beranda)
   ├─ reveal-group.js         Fade-in + slide-up untuk kelompok elemen saat di-scroll
   └─ admin.js                Sidebar, toast, dan transisi ringan di admin
```

### 2.1 tokens.js

```js
export const MOTION = {
  duration: {
    fast: 0.3,
    normal: 0.6,
    slow: 0.9,
    hero: 1,
  },
  ease: {
    out: 'power2.out',
    inOut: 'power2.inOut',
  },
  distance: {
    sm: 20,
    md: 40,
    lg: 60,
  },
  stagger: {
    fast: 0.08,
    normal: 0.12,
    slow: 0.18,
  },
};
```

Jarak gerak pada layar hingga 600 px dikurangi menjadi 55%.

### 2.2 index.js (entry point)

Entry point memeriksa `prefers-reduced-motion`, memuat core dan ScrollTrigger lokal secara berurutan, mendaftarkan plugin, lalu mengimpor modul sesuai halaman. Jika preferensi berubah menjadi reduced motion, semua konteks GSAP dikembalikan dan elemen terlihat normal.

### 2.3 Pemuatan di layout

```html
<!-- Di akhir layouts/public.php, sebelum </body> -->
<script type="module" src="<?= asset('js/motion/index.js') ?>"></script>
```

Entry point memuat `vendor/gsap/gsap.min.js` dan `vendor/gsap/ScrollTrigger.min.js` dari origin yang sama hanya ketika animasi diizinkan. Versi vendor saat ini 3.15.0; lisensi dan sumber tercatat di `public/assets/js/vendor/gsap/README.md`.

Untuk admin (`layouts/admin.php`), hanya muat `admin.js` yang ringan, tanpa ScrollTrigger.

## 3. Fallback CSS (no-JS)

Tidak perlu menyembunyikan elemen dengan CSS. Konten dirender terlihat normal. Modul animasi memberi keadaan awal `opacity`/`transform` melalui GSAP hanya setelah library lokal berhasil dimuat dan preferensi pengguna mengizinkan animasi. Jika JavaScript mati, file gagal dimuat, atau reduced motion aktif, konten tetap terlihat.

`js-flag.js` tidak digunakan untuk menyembunyikan konten animasi. Jangan menambahkan aturan CSS global yang membuat teks, kartu, formulir, atau foto bergantung pada GSAP agar terlihat.

## 4. Animasi per halaman publik

### 4.1 Beranda (`/`)

| Section | Elemen | Animasi | Trigger | Detail |
| --- | --- | --- | --- | --- |
| Hero | Teks "Papua Future Project" | Fade in + slide up | Otomatis saat load | delay 0.2s, `y: MOTION.distance.sm` |
| Hero | "Every Child Matters." | Fade in + slide up | Otomatis | delay 0.4s, `y: MOTION.distance.md` |
| Hero | Subjudul | Fade in | Otomatis | delay 0.6s |
| Sekilas PFP | Foto kiri | Fade in + slide right | ScrollTrigger `top: 80%` | `x: -MOTION.distance.md` |
| Sekilas PFP | Teks kanan (tag, judul, divider, body) | Stagger fade in + slide up | ScrollTrigger `top: 80%` | stagger `MOTION.stagger.normal` |
| Founder | Foto | Fade in + scale (0.95 ke 1) | ScrollTrigger `top: 84%` | duration `MOTION.duration.normal` |
| Founder | Teks cerita | Stagger fade in + slide up | ScrollTrigger | stagger per `<p>` |
| Penghargaan | Judul section | Fade in + slide up | ScrollTrigger | — |
| Penghargaan | 3 award card | Stagger fade in + slide up | ScrollTrigger | stagger `MOTION.stagger.normal` |
| Impact | Angka (700+, 13, 250+, 2020) | **Count up** dari 0 ke nilai akhir | ScrollTrigger `top: 75%` | Durasi 2s, easing `power2.inOut`. Angka bulat. Suffix (+) tetap. |
| Impact | Angka, label, dan sub | Fade up | ScrollTrigger `top: 88%` | Angka menghitung saat masuk viewport |
| Program | 3 card | Stagger fade in + slide up | ScrollTrigger | stagger `MOTION.stagger.normal` |
| Berita | 3 card | Stagger fade in + slide up | ScrollTrigger | — |
| Peta | Teks kiri | Stagger fade in | ScrollTrigger | — |
| Peta | Peta (iframe) kanan | Fade in + slide left | ScrollTrigger | `x: MOTION.distance.md` |

### 4.2 Tentang Kami (`/tentang`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Hero | Judul + subjudul | Fade in + slide up | Load |
| Sejarah | Narasi kiri | Stagger fade in per paragraf | ScrollTrigger |
| Sejarah | Timeline kanan | Stagger fade-in + slide-up per item | ScrollTrigger batch |
| Foto masonry | Tiap foto | Fade in + slide up | ScrollTrigger, stagger `MOTION.stagger.fast` |
| Visi & Misi | Kartu visi (kiri) | Fade in + slide right | ScrollTrigger |
| Visi & Misi | Kartu misi (kanan) | Fade in + slide left | ScrollTrigger |
| Tim Penggerak | 4 card | Stagger fade in + slide up | ScrollTrigger |
| Internship | Card per batch | Stagger fade in + slide up | ScrollTrigger per batch group |
| Volunteer | Sama dengan Internship | — | — |

### 4.3 Program (`/program`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Hero | Judul | Fade in + slide up | Load |
| Daftar program | Card program | Stagger fade in + slide up | ScrollTrigger |

### 4.4 Detail Program (`/program/{slug}`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Breadcrumb | — | Fade in | Load |
| Konten utama | Sampul, ringkasan, fakta, kegiatan, mitra | Fade in + slide up | ScrollTrigger |
| Dokumentasi | Grid foto | Stagger fade in + scale (0.95 → 1) | ScrollTrigger |

### 4.5 Berita (`/berita`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Berita terbaru | Card besar | Fade in + slide up | ScrollTrigger |
| Daftar berita | Card | Stagger fade in + slide up | ScrollTrigger |
| Diakui dan Diliput | List items | Stagger fade in | ScrollTrigger |

### 4.6 Detail Berita (`/berita/{slug}`)

Sama polanya dengan Detail Program.

### 4.7 Bergabung (`/bergabung`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Hero ajakan | Judul + tombol | Fade in + slide up | Load |
| Manfaat | 3 card | Stagger fade in + slide up | ScrollTrigger |
| Langkah | Item timeline | Stagger fade in + slide up | ScrollTrigger batch |
| Form | Form container | Fade in + slide up | ScrollTrigger |

### 4.8 Kontak (`/kontak`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Info kontak | Card/item | Stagger fade in + slide up | ScrollTrigger |
| Peta | Iframe | Fade in | ScrollTrigger |
| Form | Container | Fade in + slide up | ScrollTrigger |
| Logo mitra | Logo | Stagger fade in | ScrollTrigger |
| FAQ | Tiap item | Stagger fade in | ScrollTrigger |

### 4.9 Halaman 404

- Angka "404" dan teks: fade in + slide up.


## 5. Modul `reveal-group.js`

Modul menggunakan peta selector berdasarkan `body[data-page]`, sehingga View tidak perlu diberi class untuk menyembunyikan konten. Grup daftar seperti kartu program, berita, tim, galeri, dan FAQ memakai `ScrollTrigger.batch()`; section lain memakai satu trigger untuk grupnya.

Selector halaman didefinisikan di `PAGE_GROUPS` pada `reveal-group.js`. Setiap target mendapatkan keadaan awal hanya setelah GSAP termuat dan `prefers-reduced-motion` tidak aktif.

```js
ScrollTrigger.batch(targets, {
  start: 'top 88%',
  once: true,
  onEnter: (batch) => gsap.to(batch, {
    opacity: 1,
    y: 0,
    duration: MOTION.duration.normal,
    stagger: MOTION.stagger.fast,
    ease: MOTION.ease.out,
  }),
});
```

Variasi animasi yang digunakan:
- `fade-up` — opacity 0, y +distance
- `fade-left` — opacity 0, x +distance
- `fade-right` — opacity 0, x -distance
- `scale-in` — opacity 0, scale 0.95

## 6. Modul `count-up.js`

```js
import { MOTION } from './tokens.js';

document.querySelectorAll('[data-count]').forEach((element) => {
  const output = element.querySelector('[data-count-value]');
  const target = Number.parseInt(element.dataset.count, 10);
  const value = { current: 0 };
  if (!output || !Number.isFinite(target)) return;

  gsap.to(value, {
    current: target,
    duration: 2,
    ease: MOTION.ease.inOut,
    scrollTrigger: {
      trigger: element,
      start: 'top 75%',
      once: true,
    },
    onUpdate: () => { output.textContent = Math.round(value.current).toLocaleString('id-ID'); },
    onComplete: () => { output.textContent = target.toLocaleString('id-ID'); },
  });
});
```

Contoh markup counter:
```html
<div class="impact-num" data-count="700"><span data-count-value>700</span><sup>+</sup></div>
```

Angka awal ditulis di HTML sebagai fallback tanpa JS.

## 7. Modul `hero-intro.js`

Modul mendeteksi label, judul, subjudul, dan tautan CTA di dalam `.hero-bg`, lalu mengurutkan animasi saat halaman dimuat. Header artikel memakai `.dynamic-article-heading`. Jarak gerak otomatis diperkecil di layar ponsel.

## 8. Modul `admin.js`

Animasi admin sangat ringan, tanpa ScrollTrigger:

| Elemen | Animasi | Detail |
| --- | --- | --- |
| Sidebar laci (mobile) | Slide dari kiri | translateX(-100%) → 0, overlay fade in |
| Notifikasi flash | Slide dari bawah + fade in | 240 ms; tetap terlihat sebagai status/form feedback |
| Dialog konfirmasi | Dialog native browser | Tidak ada animasi khusus |
| Toggle switch | CSS transition bila kontrol memakai switch kustom | 200 ms, bukan GSAP |

Admin tidak membutuhkan gsap.min.js dan ScrollTrigger. Cukup CSS transition. GSAP hanya dipakai di admin jika ada kebutuhan animasi kompleks di masa depan.

## 9. Catatan performa

1. **`once: true`** pada semua ScrollTrigger. Animasi hanya jalan sekali, tidak diulang saat scroll balik.
2. **`will-change: transform, opacity`** hanya dipasang pada target dari peta selector halaman ketika GSAP mulai; dihapus setelah animasi selesai atau konteks dibersihkan.
3. **Batch ScrollTrigger.** Untuk daftar panjang (berita, pendaftar), gunakan `ScrollTrigger.batch()` alih-alih satu trigger per elemen.
4. **Lazy load gambar tetap pakai `loading="lazy"`** di HTML. GSAP tidak mengubah ini.
5. **Debounce resize.** ScrollTrigger otomatis menangani resize, tapi pastikan tidak ada listener tambahan yang bentrok.

## 10. Testing animasi

1. Buka DevTools → Rendering → centang "Emulate CSS prefers-reduced-motion: reduce". Pastikan semua konten terlihat tanpa animasi.
2. Matikan JavaScript di browser. Pastikan halaman tetap fungsional dan semua konten terlihat.
3. Periksa di perangkat lambat (throttle CPU 4× di DevTools). Animasi harus tetap halus (minimal 30 fps).
4. Periksa di viewport 360 px. Animasi slide harus disesuaikan (jarak lebih kecil) agar tidak menyebabkan overflow horizontal.
