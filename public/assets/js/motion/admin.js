const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!prefersReducedMotion) {
  document.querySelectorAll('.flash-message').forEach((notice) => {
    notice.classList.add('admin-motion-notice');
  });
}
