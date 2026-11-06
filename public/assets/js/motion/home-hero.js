const DEFAULT_INTERVAL_MS = 3500;
const CROSSFADE_SECONDS = 1.1;

export function initHomeHero({ gsap }) {
  const hero = document.querySelector('[data-home-hero]');
  if (!hero) return () => {};

  const slides = [...hero.querySelectorAll('[data-home-slide]')];
  if (slides.length < 2) return () => {};

  const intervalMs = Number.parseInt(hero.dataset.homeInterval || '', 10) || DEFAULT_INTERVAL_MS;
  const zoomSeconds = intervalMs / 1000;
  const activeIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
  let currentIndex = activeIndex;
  let activeZoom = null;
  let fadeTimeline = null;

  slides.forEach((slide, index) => {
    slide.style.transition = 'none';
    gsap.set(slide, {
      autoAlpha: index === activeIndex ? 1 : 0,
      scale: index === activeIndex ? 1.055 : 1,
    });
  });

  activeZoom = gsap.to(slides[currentIndex], {
    scale: 1.11,
    duration: zoomSeconds,
    ease: 'power1.out',
  });

  const handleSlideChange = (event) => {
    const nextIndex = Number(event.detail?.index);
    if (!Number.isInteger(nextIndex) || nextIndex < 0 || nextIndex >= slides.length || nextIndex === currentIndex) return;

    activeZoom?.kill();
    fadeTimeline?.kill();

    const previousSlide = slides[currentIndex];
    const nextSlide = slides[nextIndex];
    currentIndex = nextIndex;

    fadeTimeline = gsap.timeline();
    fadeTimeline
      .set(nextSlide, { autoAlpha: 0, scale: 1.035 })
      .to(previousSlide, {
        autoAlpha: 0,
        scale: 1,
        duration: CROSSFADE_SECONDS,
        ease: 'power2.inOut',
      }, 0)
      .to(nextSlide, {
        autoAlpha: 1,
        scale: 1.11,
        duration: zoomSeconds,
        ease: 'power1.out',
      }, 0);

    activeZoom = fadeTimeline;
  };

  hero.addEventListener('pfp:home-slide-change', handleSlideChange);

  return () => {
    hero.removeEventListener('pfp:home-slide-change', handleSlideChange);
    activeZoom?.kill();
    fadeTimeline?.kill();
    slides.forEach((slide) => {
      slide.style.removeProperty('transition');
      gsap.set(slide, { clearProps: 'opacity,visibility,transform' });
    });
  };
}
