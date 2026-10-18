import { MOTION, motionDistance } from './tokens.js';

const PAGE_GROUPS = {
  home: [
    ['.about-grid', '.about-photo-wrap', 'fade-right'],
    ['.about-grid > div:last-child', '.pfp-tag,.pfp-title,.pfp-divider,.pfp-body', 'fade-up'],
    ['.founder-grid', '.founder-photo-wrap', 'scale-in'],
    ['.founder-story', ':scope > *', 'fade-up'],
    ['#penghargaan .awards-grid', '.award-card', 'fade-up', true],
    ['#dampak .impact-header', ':scope > *', 'fade-up'],
    ['#dampak .impact-grid', '.impact-item', 'fade-up', true],
    ['#program .prog-grid', '.prog-card', 'fade-up', true],
    ['#berita .news-grid', '.news-card', 'fade-up', true],
    ['#wilayah .map-info', '.pfp-tag,.pfp-title,.pfp-divider,p,.village-item,.village-more', 'fade-up'],
    ['#wilayah .map-grid', '.map-frame-wrap', 'fade-left'],
  ],
  about: [
    ['.history-grid > div:first-child', '.pfp-tag,.pfp-title,.pfp-divider,.pfp-body', 'fade-up'],
    ['.history-grid .timeline', '.tl-item', 'fade-up', true],
    ['.masonry-grid', '.masonry-item', 'scale-in', true],
    ['.about-section-heading', ':scope > *', 'fade-up'],
    ['.visimisi-grid', '.visi-card', 'fade-right'],
    ['.visimisi-grid', '.misi-card', 'fade-left'],
    ['#tim .team-grid', '.team-card', 'fade-up', true],
    ['.team-batch-group .team-grid', '.team-card', 'fade-up', true],
  ],
  program: [
    ['.prog-grid-full', '.prog-card', 'fade-up', true],
  ],
  'program-detail': [
    ['.program-detail-content', ':scope > .program-detail-cover,:scope > .program-summary,:scope > .program-copy,:scope > .program-facts,:scope > h2,:scope > ul,:scope > .program-gallery,:scope > .prog-link', 'fade-up'],
    ['.program-gallery-grid', 'figure', 'scale-in', true],
    ['.detail-body', '.kegiatan-list li', 'fade-up', true],
  ],
  news: [
    ['.featured-card', ':scope > *', 'fade-up'],
    ['.news-grid', '.news-card', 'fade-up', true],
    ['.news-grid', '.media-item', 'fade-up', true],
  ],
  'news-detail': [
    ['.dynamic-article-content', ':scope > p,:scope > .dynamic-article-cover', 'fade-up'],
    ['.dynamic-article-aside', ':scope > *', 'fade-left'],
    ['.dynamic-article-gallery .program-gallery-grid', 'figure', 'scale-in', true],
    ['.dynamic-related .news-grid', '.news-card', 'fade-up', true],
  ],
  join: [
    ['.benefit-grid', '.benefit-card', 'fade-up', true],
    ['.timeline-wrap', '.timeline-item', 'fade-up', true],
    ['#form-daftar .form-wrap-join', ':scope > *', 'fade-up'],
  ],
  'join-success': [
    ['.application-success-section .pfp-container', ':scope > *', 'fade-up'],
  ],
  contact: [
    ['.contact-grid > div:first-child', '.channel-card', 'fade-up', true],
    ['.contact-grid', '.map-frame-wrap', 'fade-left'],
    ['#contact-form .form-wrap', ':scope > *', 'fade-up'],
    ['.partners-marquee-group:first-child', '.partner-card', 'fade-up', true],
    ['.contact-faq-list', '.faq-item', 'fade-up', true],
    ['main > section:last-child .pfp-container', ':scope > *', 'fade-up'],
  ],
  'not-found': [
    ['main > section > div', ':scope > *', 'fade-up'],
  ],
};

function fromState(variant) {
  const distance = motionDistance(MOTION.distance.sm);
  const states = {
    'fade-up': { y: distance },
    'fade-left': { x: distance },
    'fade-right': { x: -distance },
    'scale-in': { scale: 0.95 },
  };

  return { ...states[variant], opacity: 0 };
}

function revealTargets({ gsap, ScrollTrigger, root, selector, variant, batched, context, cleanupTargets }) {
  const targets = [...root.querySelectorAll(selector)];
  if (!targets.length) return;

  const initial = fromState(variant);
  const distanceProps = { x: 0, y: 0, scale: 1, opacity: 1 };
  targets.forEach((element) => {
    element.style.willChange = 'transform, opacity';
    cleanupTargets.add(element);
  });

  context.add(() => {
    gsap.set(targets, initial);

    const animate = (elements) => {
      gsap.to(elements, {
        ...distanceProps,
        duration: MOTION.duration.normal,
        ease: MOTION.ease.out,
        stagger: batched ? MOTION.stagger.fast : MOTION.stagger.normal,
        overwrite: 'auto',
        onComplete: () => gsap.set(elements, { clearProps: 'transform,opacity,willChange' }),
        scrollTrigger: batched ? undefined : {
          trigger: root,
          start: 'top 84%',
          once: true,
        },
      });
    };

    if (batched) {
      ScrollTrigger.batch(targets, {
        start: 'top 88%',
        once: true,
        onEnter: (elements) => context.add(() => animate(elements)),
      });
    } else {
      animate(targets);
    }
  });
}

export function initRevealGroups({ gsap, ScrollTrigger, page }) {
  const context = gsap.context(() => {});
  const cleanupTargets = new Set();
  try {
    for (const [containerSelector, selector, variant, batched = false] of PAGE_GROUPS[page] ?? []) {
      document.querySelectorAll(containerSelector).forEach((root) => {
        revealTargets({ gsap, ScrollTrigger, root, selector, variant, batched, context, cleanupTargets });
      });
    }
  } catch (error) {
    context.revert();
    cleanupTargets.forEach((element) => element.style.removeProperty('will-change'));
    throw error;
  }

  return () => {
    context.revert();
    cleanupTargets.forEach((element) => element.style.removeProperty('will-change'));
  };
}
