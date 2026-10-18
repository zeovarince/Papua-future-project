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

document.querySelectorAll('[data-hover-enter], [data-hover-leave]').forEach((element) => {
  const applyStyles = (rules) => {
    if (!rules) return;
    rules.split(';').forEach((rule) => {
      const separator = rule.indexOf(':');
      if (separator < 1) return;
      const property = rule.slice(0, separator).replace(/[A-Z]/g, (letter) => `-${letter.toLowerCase()}`);
      element.style.setProperty(property, rule.slice(separator + 1));
    });
  };

  element.addEventListener('mouseenter', () => applyStyles(element.dataset.hoverEnter));
  element.addEventListener('mouseleave', () => applyStyles(element.dataset.hoverLeave));
});

document.querySelectorAll('[data-action="copy-link"]').forEach((button) => {
  button.addEventListener('click', () => {
    if (typeof copyLink === 'function') copyLink();
  });
});
