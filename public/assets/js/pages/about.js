// ── 1. Sticky Navbar ──
    // ── 3. Lightbox ──
    const items     = Array.from(document.querySelectorAll('.masonry-item'));
    const lightbox  = document.getElementById('lightbox');
    const lbImg     = document.getElementById('lb-img');
    const lbCaption = document.getElementById('lb-caption');
    const lbCounter = document.getElementById('lb-counter');
    const lbClose   = document.getElementById('lb-close');
    const lbPrev    = document.getElementById('lb-prev');
    const lbNext    = document.getElementById('lb-next');
    let current = 0;

    function openLightbox(index) {
      current = index;
      const item = items[current];
      lbImg.src         = item.dataset.src;
      lbImg.alt         = item.dataset.caption;
      lbCaption.textContent = item.dataset.caption;
      lbCounter.textContent = (current + 1) + ' / ' + items.length;
      lightbox.classList.add('is-open');
      document.body.classList.add('lightbox-open');
    }

    function closeLightbox() {
      lightbox.classList.remove('is-open');
      document.body.classList.remove('lightbox-open');
    }

    function showPrev() {
      current = (current - 1 + items.length) % items.length;
      openLightbox(current);
    }

    function showNext() {
      current = (current + 1) % items.length;
      openLightbox(current);
    }

    // Klik tiap item buka lightbox
    items.forEach((item, i) => {
      item.addEventListener('click', () => openLightbox(i));
    });

    lbClose.addEventListener('click', closeLightbox);
    lbPrev.addEventListener('click', (e) => { e.stopPropagation(); showPrev(); });
    lbNext.addEventListener('click', (e) => { e.stopPropagation(); showNext(); });

    // Klik backdrop tutup
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) closeLightbox();
    });

    // Keyboard: Esc tutup, ← → navigasi
    document.addEventListener('keydown', (e) => {
      if (!lightbox.classList.contains('is-open')) return;
      if (e.key === 'Escape')     closeLightbox();
      if (e.key === 'ArrowLeft')  showPrev();
      if (e.key === 'ArrowRight') showNext();
    });

