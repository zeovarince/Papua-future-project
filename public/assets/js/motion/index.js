const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
const cleanups = [];
let active = false;
let generation = 0;

function loadLocalScript(filename) {
  return new Promise((resolve, reject) => {
    const source = new URL(`../vendor/gsap/${filename}`, import.meta.url).href;
    const existing = [...document.scripts].find((script) => script.src === source);
    if (existing?.dataset.loaded === 'true') {
      resolve();
      return;
    }

    const script = existing ?? document.createElement('script');
    script.src = source;
    script.async = true;
    script.addEventListener('load', () => {
      script.dataset.loaded = 'true';
      resolve();
    }, { once: true });
    script.addEventListener('error', () => {
      script.remove();
      reject(new Error(`Could not load local animation file: ${filename}`));
    }, { once: true });
    if (!existing) document.head.append(script);
  });
}

function disableMotion() {
  generation += 1;
  active = false;
  while (cleanups.length) {
    try { cleanups.pop()?.(); } catch (error) { console.error('Motion cleanup failed.', error); }
  }
}

async function enableMotion() {
  if (active || motionPreference.matches) return;
  active = true;
  const currentGeneration = ++generation;

  try {
    if (!window.gsap) await loadLocalScript('gsap.min.js');
    if (!window.ScrollTrigger) await loadLocalScript('ScrollTrigger.min.js');
    if (currentGeneration !== generation || motionPreference.matches || !window.gsap || !window.ScrollTrigger) return;

    const { gsap, ScrollTrigger } = window;
    gsap.registerPlugin(ScrollTrigger);
    const page = document.body.dataset.page;

    const moduleLoaders = [];
    if (document.querySelector('.hero-bg, .dynamic-article-heading')) {
      moduleLoaders.push(async () => (await import('./hero-intro.js')).initHeroIntro({ gsap, page }));
    }
    if (page === 'home' && document.querySelector('[data-count]')) {
      moduleLoaders.push(async () => (await import('./count-up.js')).initCountUp({ gsap }));
    }
    moduleLoaders.push(async () => (await import('./reveal-group.js')).initRevealGroups({ gsap, ScrollTrigger, page }));

    for (const loadModule of moduleLoaders) {
      if (currentGeneration !== generation || motionPreference.matches) {
        disableMotion();
        return;
      }
      const cleanup = await loadModule();
      if (currentGeneration !== generation || motionPreference.matches) {
        cleanup?.();
        disableMotion();
        return;
      }
      if (typeof cleanup === 'function') cleanups.push(cleanup);
    }

    requestAnimationFrame(() => {
      if (currentGeneration === generation && !motionPreference.matches) ScrollTrigger.refresh();
    });
    window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
  } catch (error) {
    if (currentGeneration === generation) {
      disableMotion();
      console.error('GSAP animation was skipped; page content remains visible.', error);
    }
  }
}

function handlePreferenceChange(event) {
  if (event.matches) disableMotion();
  else enableMotion();
}

if (motionPreference.addEventListener) motionPreference.addEventListener('change', handlePreferenceChange);
else motionPreference.addListener(handlePreferenceChange);

enableMotion();
