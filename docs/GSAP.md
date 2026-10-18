# GSAP: Panduan Implementasi Animasi

Panduan animasi GSAP 3 + ScrollTrigger untuk seluruh halaman publik dan panel admin Papua Future Project. Animasi ditambahkan di Step 11 ([STEP.md](STEP.md)) setelah semua halaman berfungsi.

Dokumen terkait: [PRD.md](PRD.md), [ARCHITECTURE.md](ARCHITECTURE.md) bagian 3 (letak file), [RULES.md](RULES.md) bagian aksesibilitas.

## 1. Prinsip

1. **Halaman harus berfungsi tanpa JavaScript.** Semua konten sudah ada di HTML. Animasi hanya menambah kesan, bukan menyembunyikan informasi. Tidak ada konten yang "baru muncul saat di-scroll" tanpa fallback CSS.
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
    fast:    0.3,
    normal:  0.6,
    slow:    0.9,
    hero:    1.0,
  },
  ease: {
    out:     'power2.out',
    inOut:   'power2.inOut',
    back:    'back.out(1.4)',
    elastic: 'elastic.out(1, 0.5)',
  },
  distance: {
    sm: 20,   // px
    md: 40,
    lg: 60,
  },
  stagger: {
    fast:   0.08,
    normal: 0.12,
    slow:   0.18,
  },
};
```

### 2.2 index.js (entry point)

```js
import { MOTION } from './tokens.js';

// Cek preferensi pengguna
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!prefersReduced) {
  gsap.registerPlugin(ScrollTrigger);

  // Deteksi halaman dari atribut data-page di <body>
  const page = document.body.dataset.page;

  // Hero ada di banyak halaman
  if (document.querySelector('.hero-bg, [data-animate="hero"]')) {
    import('./hero-intro.js');
  }

  // Per halaman
  if (page === 'home') {
    import('./count-up.js');
  }

  // Reveal group universal
  import('./reveal-group.js');
}
```

### 2.3 Pemuatan di layout

```html
<!-- Di akhir layouts/public.php, sebelum </body> -->
<script src="<?= asset('js/vendor/gsap/gsap.min.js') ?>" defer></script>
<script src="<?= asset('js/vendor/gsap/ScrollTrigger.min.js') ?>" defer></script>
<script type="module" src="<?= asset('js/motion/index.js') ?>" defer></script>
```

Untuk admin (`layouts/admin.php`), hanya muat `admin.js` yang ringan, tanpa ScrollTrigger.

## 3. Fallback CSS (no-JS)

```css
/* Di base.css */
/* Saat JS belum jalan, semua elemen terlihat normal */
[data-animate] {
  opacity: 1;
  transform: none;
}

/* Saat JS aktif, tambah kelas .js di <html> */
html.js [data-animate] {
  opacity: 0;
}
```

File `js-flag.js` (dimuat di `<head>` tanpa defer):
```js
document.documentElement.classList.add('js');
```

Dengan ini, konten tetap terlihat tanpa JS. Hanya saat JS aktif, elemen disembunyikan dan GSAP yang menampilkannya.

## 4. Animasi per halaman publik

### 4.1 Beranda (`/`)

| Section | Elemen | Animasi | Trigger | Detail |
| --- | --- | --- | --- | --- |
| Hero | Teks "Papua Future Project" | Fade in + slide up | Otomatis saat load | delay 0.2s, `y: MOTION.distance.sm` |
| Hero | "Every Child Matters." | Fade in + slide up | Otomatis | delay 0.4s, `y: MOTION.distance.md` |
| Hero | Subjudul | Fade in | Otomatis | delay 0.6s |
| Sekilas PFP | Foto kiri | Fade in + slide right | ScrollTrigger `top: 80%` | `x: -MOTION.distance.md` |
| Sekilas PFP | Teks kanan (tag, judul, divider, body) | Stagger fade in + slide up | ScrollTrigger `top: 80%` | stagger `MOTION.stagger.normal` |
| Founder | Foto | Fade in + scale (0.95 → 1) | ScrollTrigger `top: 80%` | duration `MOTION.duration.slow` |
| Founder | Teks cerita | Stagger fade in + slide up | ScrollTrigger | stagger per `<p>` |
| Penghargaan | Judul section | Fade in + slide up | ScrollTrigger | — |
| Penghargaan | 3 award card | Stagger fade in + slide up | ScrollTrigger | stagger `MOTION.stagger.normal` |
| Impact | Angka (700+, 13, 250+, 2020) | **Count up** dari 0 ke nilai akhir | ScrollTrigger `top: 70%` | Durasi 2s, easing `power2.inOut`. Angka bulat. Suffix (+) tetap. |
| Impact | Label dan sub | Fade in | Bersamaan dengan count up | — |
| Program | 3 card | Stagger fade in + slide up | ScrollTrigger | stagger `MOTION.stagger.normal` |
| Berita | 3 card | Stagger fade in + slide up | ScrollTrigger | — |
| Peta | Teks kiri | Stagger fade in | ScrollTrigger | — |
| Peta | Peta (iframe) kanan | Fade in + slide left | ScrollTrigger | `x: MOTION.distance.md` |

### 4.2 Tentang Kami (`/tentang`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Hero | Judul + subjudul | Fade in + slide up | Load |
| Sejarah | Narasi kiri | Stagger fade in per paragraf | ScrollTrigger |
| Sejarah | Timeline kanan | Stagger per item: dot scale in, teks fade in | ScrollTrigger, stagger `MOTION.stagger.slow` |
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
| Strip angka | 4 angka | Count up (sederhana) | ScrollTrigger |
| Daftar program | Card program | Stagger fade in + slide up | ScrollTrigger |
| Dari Lapangan | 6 foto | Stagger fade in | ScrollTrigger |

### 4.4 Detail Program (`/program/{slug}`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Breadcrumb | — | Fade in | Load |
| Konten utama | Judul, tagline, paragraf | Stagger fade in + slide up | Load (di atas fold) |
| Kegiatan, Mitra | List items | Stagger fade in | ScrollTrigger |
| Dokumentasi | Grid foto | Stagger fade in + scale (0.95 → 1) | ScrollTrigger |

### 4.5 Berita (`/berita`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Berita terbaru | Card besar | Fade in + slide up | Load |
| Filter kategori | Tombol filter | Fade in | Load |
| Daftar berita | Card | Stagger fade in + slide up | ScrollTrigger |
| Diakui dan Diliput | List items | Stagger fade in | ScrollTrigger |

### 4.6 Detail Berita (`/berita/{slug}`)

Sama polanya dengan Detail Program.

### 4.7 Bergabung (`/bergabung`)

| Section | Elemen | Animasi | Trigger |
| --- | --- | --- | --- |
| Hero ajakan | Judul + tombol | Fade in + slide up | Load |
| Manfaat | 3 card | Stagger fade in + slide up | ScrollTrigger |
| Langkah | 4 step | Stagger fade in, garis penghubung draw-in | ScrollTrigger |
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

- Angka "404" besar: scale dari 0.8 → 1 dengan bounce.
- Teks: fade in.

## 5. Modul `reveal-group.js`

Modul generik yang menangani sebagian besar animasi "fade in + slide up saat scroll". Elemen ditandai dengan atribut `data-animate`:

```html
<!-- Satu elemen -->
<div data-animate="fade-up">...</div>

<!-- Kelompok dengan stagger -->
<div data-animate-group>
  <div data-animate="fade-up">Card 1</div>
  <div data-animate="fade-up">Card 2</div>
  <div data-animate="fade-up">Card 3</div>
</div>
```

Implementasi:

```js
import { MOTION } from './tokens.js';

// Elemen tunggal
gsap.utils.toArray('[data-animate="fade-up"]').forEach(el => {
  if (el.closest('[data-animate-group]')) return; // ditangani di bawah

  gsap.from(el, {
    y: MOTION.distance.sm,
    opacity: 0,
    duration: MOTION.duration.normal,
    ease: MOTION.ease.out,
    scrollTrigger: {
      trigger: el,
      start: 'top 85%',
      once: true,
    },
  });
});

// Kelompok dengan stagger
gsap.utils.toArray('[data-animate-group]').forEach(group => {
  const children = group.querySelectorAll('[data-animate]');

  gsap.from(children, {
    y: MOTION.distance.sm,
    opacity: 0,
    duration: MOTION.duration.normal,
    ease: MOTION.ease.out,
    stagger: MOTION.stagger.normal,
    scrollTrigger: {
      trigger: group,
      start: 'top 85%',
      once: true,
    },
  });
});
```

Variasi `data-animate` lain:
- `fade-up` — opacity 0, y +distance
- `fade-left` — opacity 0, x +distance
- `fade-right` — opacity 0, x -distance
- `scale-in` — opacity 0, scale 0.95

## 6. Modul `count-up.js`

```js
import { MOTION } from './tokens.js';

gsap.utils.toArray('[data-count]').forEach(el => {
  const target = parseInt(el.dataset.count, 10);
  const obj = { val: 0 };

  gsap.to(obj, {
    val: target,
    duration: 2,
    ease: MOTION.ease.inOut,
    scrollTrigger: {
      trigger: el,
      start: 'top 75%',
      once: true,
    },
    onUpdate() {
      el.textContent = Math.round(obj.val).toLocaleString('id-ID');
    },
  });
});
```

HTML:
```html
<div class="impact-num" data-count="700" data-animate="count">700</div>
```

Angka awal ditulis di HTML sebagai fallback tanpa JS.

## 7. Modul `hero-intro.js`

```js
import { MOTION } from './tokens.js';

const tl = gsap.timeline({ defaults: { ease: MOTION.ease.out } });

tl.from('[data-hero="label"]', {
    y: MOTION.distance.sm,
    opacity: 0,
    duration: MOTION.duration.normal,
  })
  .from('[data-hero="title"]', {
    y: MOTION.distance.md,
    opacity: 0,
    duration: MOTION.duration.hero,
  }, '-=0.3')
  .from('[data-hero="subtitle"]', {
    y: MOTION.distance.sm,
    opacity: 0,
    duration: MOTION.duration.normal,
  }, '-=0.4')
  .from('[data-hero="cta"]', {
    y: MOTION.distance.sm,
    opacity: 0,
    duration: MOTION.duration.fast,
  }, '-=0.3');
```

## 8. Modul `admin.js`

Animasi admin sangat ringan, tanpa ScrollTrigger:

| Elemen | Animasi | Detail |
| --- | --- | --- |
| Sidebar laci (mobile) | Slide dari kiri | translateX(-100%) → 0, overlay fade in |
| Toast notification | Slide dari bawah + fade in | translateY(20px) → 0, auto-dismiss 4s |
| Dialog konfirmasi | Fade in + scale (0.95 → 1) | Overlay fade in, dialog scale |
| Toggle switch | Lingkaran geser | CSS transition 200ms, bukan GSAP |

Admin tidak membutuhkan gsap.min.js dan ScrollTrigger. Cukup CSS transition. GSAP hanya dipakai di admin jika ada kebutuhan animasi kompleks di masa depan.

## 9. Catatan performa

1. **`once: true`** pada semua ScrollTrigger. Animasi hanya jalan sekali, tidak diulang saat scroll balik.
2. **`will-change: transform, opacity`** ditambahkan CSS pada elemen `[data-animate]` saat JS aktif (`.js [data-animate]`). Dihapus setelah animasi selesai dengan `onComplete`.
3. **Batch ScrollTrigger.** Untuk daftar panjang (berita, pendaftar), gunakan `ScrollTrigger.batch()` alih-alih satu trigger per elemen.
4. **Lazy load gambar tetap pakai `loading="lazy"`** di HTML. GSAP tidak mengubah ini.
5. **Debounce resize.** ScrollTrigger otomatis menangani resize, tapi pastikan tidak ada listener tambahan yang bentrok.

## 10. Testing animasi

1. Buka DevTools → Rendering → centang "Emulate CSS prefers-reduced-motion: reduce". Pastikan semua konten terlihat tanpa animasi.
2. Matikan JavaScript di browser. Pastikan halaman tetap fungsional dan semua konten terlihat.
3. Periksa di perangkat lambat (throttle CPU 4× di DevTools). Animasi harus tetap halus (minimal 30 fps).
4. Periksa di viewport 360 px. Animasi slide harus disesuaikan (jarak lebih kecil) agar tidak menyebabkan overflow horizontal.
