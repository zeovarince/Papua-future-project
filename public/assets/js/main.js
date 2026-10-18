const navbar = document.getElementById('navbar');

if (navbar) {
  const updateNavbar = () => {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
  };

  updateNavbar();
  window.addEventListener('scroll', updateNavbar, { passive: true });
}

const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
document.querySelectorAll('.nav-link').forEach((link) => {
  const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '') || '/';
  const isActive = linkPath === currentPath || (linkPath !== '/' && currentPath.startsWith(`${linkPath}/`));
  link.classList.toggle('active', isActive);
});

document.querySelectorAll('[data-action="copy-link"]').forEach((button) => {
  button.addEventListener('click', () => {
    if (typeof copyLink === 'function') copyLink();
  });
});
