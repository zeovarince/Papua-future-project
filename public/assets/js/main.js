const navbar = document.getElementById('navbar');

if (navbar) {
  const updateNavbar = () => {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
  };

  updateNavbar();
  window.addEventListener('scroll', updateNavbar, { passive: true });
}

const navToggle = document.querySelector('[data-nav-toggle]');
const publicNavigation = document.getElementById('public-navigation');

function setPublicNavigation(open) {
  if (!navbar || !navToggle) return;

  navbar.classList.toggle('menu-open', open);
  navToggle.setAttribute('aria-expanded', String(open));
  navToggle.setAttribute('aria-label', open ? 'Tutup navigasi' : 'Buka navigasi');
}

navToggle?.addEventListener('click', () => {
  setPublicNavigation(navToggle.getAttribute('aria-expanded') !== 'true');
});

publicNavigation?.querySelectorAll('a').forEach((link) => {
  link.addEventListener('click', () => setPublicNavigation(false));
});

document.addEventListener('click', (event) => {
  if (navbar?.classList.contains('menu-open') && !navbar.contains(event.target)) {
    setPublicNavigation(false);
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && navbar?.classList.contains('menu-open')) {
    setPublicNavigation(false);
    navToggle?.focus();
  }
});

const desktopNavigation = window.matchMedia('(min-width: 1025px)');
desktopNavigation.addEventListener('change', () => setPublicNavigation(false));

const normalizePath = (path) => path.replace(/\/$/, '') || '/';
const currentPath = normalizePath(window.location.pathname);
const homeLink = document.querySelector('.nav-brand');
const homePath = normalizePath(new URL(homeLink?.href || '/', window.location.origin).pathname);

document.querySelectorAll('.nav-link').forEach((link) => {
  const linkPath = normalizePath(new URL(link.href, window.location.origin).pathname);
  // The home URL can be a directory prefix (for example /PFP/public).
  // Match it exactly so it never stays active on child pages.
  const isHomeLink = linkPath === homePath;
  const isActive = isHomeLink
    ? currentPath === homePath
    : linkPath === currentPath || (linkPath !== '/' && currentPath.startsWith(`${linkPath}/`));
  link.classList.toggle('active', isActive);
});

document.querySelectorAll('[data-action="copy-link"]').forEach((button) => {
  button.addEventListener('click', () => {
    if (typeof copyLink === 'function') copyLink();
  });
});
