import { MOTION } from './tokens.js';

export function initCountUp({ gsap }) {
  const counters = [...document.querySelectorAll('[data-count]')];
  const context = gsap.context(() => {
    counters.forEach((element) => {
      const output = element.querySelector('[data-count-value]');
      const target = Number.parseInt(element.dataset.count, 10);
      if (!output || !Number.isFinite(target)) return;

      const value = { current: 0 };
      gsap.to(value, {
        current: target,
        duration: 2,
        ease: MOTION.ease.inOut,
        onUpdate: () => { output.textContent = Math.round(value.current).toLocaleString('id-ID'); },
        onComplete: () => { output.textContent = target.toLocaleString('id-ID'); },
        scrollTrigger: { trigger: element, start: 'top 75%', once: true },
      });
    });
  });

  return () => context.revert();
}
