const sidebar = document.querySelector('[data-admin-sidebar]');
const toggle = document.querySelector('[data-sidebar-toggle]');
const closeButton = document.querySelector('[data-sidebar-close]');
const overlay = document.querySelector('[data-sidebar-overlay]');

function closeSidebar(restoreFocus = false) {
  sidebar?.classList.remove('is-open');
  overlay?.setAttribute('hidden', '');
  toggle?.setAttribute('aria-expanded', 'false');
  toggle?.setAttribute('aria-label', 'Buka menu');
  toggle?.classList.remove('is-active');
  document.body.classList.remove('admin-sidebar-open');
  if (restoreFocus) toggle?.focus();
}

toggle?.addEventListener('click', () => {
  const isOpen = sidebar?.classList.toggle('is-open') ?? false;
  if (overlay) overlay.hidden = !isOpen;
  toggle.setAttribute('aria-expanded', String(isOpen));
  toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
  toggle.classList.toggle('is-active', isOpen);
  document.body.classList.toggle('admin-sidebar-open', isOpen);
  if (isOpen) sidebar?.querySelector('.sidebar-link')?.focus();
});

closeButton?.addEventListener('click', () => closeSidebar(true));
overlay?.addEventListener('click', () => closeSidebar(true));
sidebar?.querySelectorAll('.sidebar-link').forEach((link) => {
  link.addEventListener('click', () => {
    if (window.matchMedia('(max-width: 767.98px)').matches) closeSidebar();
  });
});

window.matchMedia('(min-width: 768px)').addEventListener('change', () => closeSidebar());
document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && sidebar?.classList.contains('is-open')) {
    closeSidebar(true);
    return;
  }

  if (event.key === 'Tab' && sidebar?.classList.contains('is-open')) {
    const focusable = [...sidebar.querySelectorAll('a[href], button:not([disabled])')];
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last?.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first?.focus();
    }
  }
});

document.addEventListener('submit', (event) => {
  const form = event.target.closest('form[data-confirm]');
  if (form && !window.confirm(form.dataset.confirm || 'Lanjutkan aksi ini?')) {
    event.preventDefault();
  }
});
