// Sticky navbar
    // Toggle kategori tujuan form
    document.querySelectorAll('.kategori-pill').forEach(pill => {
      pill.addEventListener('click', () => {
        document.querySelectorAll('.kategori-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
      });
    });

    // Accordion FAQ — one-at-a-time
    document.querySelectorAll('.faq-item').forEach(item => {
      item.querySelector('.faq-header').addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      });
    });
