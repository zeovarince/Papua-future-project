export const MOTION = Object.freeze({
  duration: Object.freeze({ fast: 0.3, normal: 0.6, slow: 0.9, hero: 1 }),
  ease: Object.freeze({ out: 'power2.out', inOut: 'power2.inOut' }),
  distance: Object.freeze({ sm: 20, md: 40, lg: 60 }),
  stagger: Object.freeze({ fast: 0.08, normal: 0.12, slow: 0.18 }),
});

export function motionDistance(value) {
  return window.matchMedia('(max-width: 600px)').matches ? value * 0.55 : value;
}
