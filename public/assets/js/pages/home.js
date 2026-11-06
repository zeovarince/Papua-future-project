(() => {
  const hero = document.querySelector('[data-home-hero]');
  if (!hero) return;

  const slides = Array.from(hero.querySelectorAll('[data-home-slide]'));
  const controls = Array.from(hero.querySelectorAll('[data-home-slide-to]'));
  if (slides.length < 2 || controls.length !== slides.length) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const intervalMs = Number.parseInt(hero.dataset.homeInterval || '', 10) || 3500;
  const caption = hero.querySelector('[data-home-caption]');
  let activeIndex = 0;
  let timer = null;
  let paused = false;

  const showSlide = (nextIndex) => {
    const previousIndex = activeIndex;
    activeIndex = (nextIndex + slides.length) % slides.length;
    slides.forEach((slide, index) => slide.classList.toggle('is-active', index === activeIndex));
    if (caption && slides[activeIndex].dataset.caption) {
      caption.textContent = slides[activeIndex].dataset.caption;
    }
    controls.forEach((control, index) => {
      const active = index === activeIndex;
      control.classList.toggle('is-active', active);
      control.setAttribute('aria-pressed', String(active));
    });
    if (previousIndex !== activeIndex) {
      hero.dispatchEvent(new CustomEvent('pfp:home-slide-change', {
        detail: { previousIndex, index: activeIndex },
      }));
    }
  };

  const stopTimer = () => {
    if (timer !== null) window.clearInterval(timer);
    timer = null;
  };

  const startTimer = () => {
    stopTimer();
    if (paused || reducedMotion.matches || document.hidden) return;
    timer = window.setInterval(() => showSlide(activeIndex + 1), intervalMs);
  };

  controls.forEach((control) => {
    control.addEventListener('click', () => {
      const index = Number.parseInt(control.dataset.homeSlideTo || '', 10);
      if (Number.isInteger(index)) showSlide(index);
      startTimer();
    });
  });

  hero.addEventListener('mouseenter', () => { paused = true; stopTimer(); });
  hero.addEventListener('mouseleave', () => { paused = false; startTimer(); });
  hero.addEventListener('focusin', () => { paused = true; stopTimer(); });
  hero.addEventListener('focusout', (event) => {
    if (!hero.contains(event.relatedTarget)) { paused = false; startTimer(); }
  });
  document.addEventListener('visibilitychange', startTimer);
  reducedMotion.addEventListener?.('change', startTimer);

  showSlide(0);
  startTimer();
})();
