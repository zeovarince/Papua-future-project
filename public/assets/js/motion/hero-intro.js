import { MOTION, motionDistance } from './tokens.js';

export function initHeroIntro({ gsap, page }) {
  const cleanupTargets = [];
  const context = gsap.context(() => {
    const hero = document.querySelector('.hero-bg');
    const content = hero?.querySelector('.relative.z-10') ?? hero?.firstElementChild;

    if (content) {
      const paragraphs = [...content.querySelectorAll('p')];
      const label = paragraphs[0] ?? null;
      const title = content.querySelector('h1');
      const subtitle = paragraphs.at(-1) !== label ? paragraphs.at(-1) : null;
      const cta = [...content.querySelectorAll('a[href^="#"]')];

      const steps = [
        { target: label, y: motionDistance(MOTION.distance.sm), duration: MOTION.duration.normal, position: 0.15 },
        { target: title, y: motionDistance(MOTION.distance.md), duration: MOTION.duration.hero, position: 0.35 },
        { target: subtitle, y: motionDistance(MOTION.distance.sm), duration: MOTION.duration.normal, position: 0.65 },
        { target: cta, y: motionDistance(MOTION.distance.sm), duration: MOTION.duration.fast, position: 0.8 },
      ].filter((step) => step.target && (Array.isArray(step.target) ? step.target.length : true));

      const timeline = gsap.timeline({ defaults: { ease: MOTION.ease.out } });
      steps.forEach(({ target, y, duration, position }) => {
        const elements = Array.isArray(target) ? target : [target];
        cleanupTargets.push(...elements);
        elements.forEach((element) => { element.style.willChange = 'transform, opacity'; });
        timeline.from(elements, { y, opacity: 0, duration, stagger: 0.06 }, position);
      });

      if (cleanupTargets.length) {
        timeline.eventCallback('onComplete', () => {
          gsap.set(cleanupTargets, { clearProps: 'transform,opacity,willChange' });
        });
      }
    }

    // The news article detail uses a compact title header instead of .hero-bg.
    if (!hero && page === 'news-detail') {
      const heading = document.querySelector('.dynamic-article-heading');
      const targets = heading ? [...heading.children] : [];
      if (targets.length) {
        cleanupTargets.push(...targets);
        targets.forEach((element) => { element.style.willChange = 'transform, opacity'; });
        const timeline = gsap.timeline({ defaults: { ease: MOTION.ease.out } });
        timeline.from(targets, {
          y: motionDistance(MOTION.distance.sm),
          opacity: 0,
          duration: MOTION.duration.normal,
          stagger: MOTION.stagger.fast,
        });
        timeline.eventCallback('onComplete', () => {
          gsap.set(targets, { clearProps: 'transform,opacity,willChange' });
        });
      }
    }
  });

  return () => {
    context.revert();
    cleanupTargets.forEach((element) => element.style.removeProperty('will-change'));
  };
}
