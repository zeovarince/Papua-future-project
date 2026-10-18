const sidebar = document.querySelector('[data-admin-sidebar]');
const toggle = document.querySelector('[data-sidebar-toggle]');
const overlay = document.querySelector('[data-sidebar-overlay]');

function closeSidebar() {
  sidebar?.classList.remove('is-open');
  overlay?.setAttribute('hidden', '');
  toggle?.setAttribute('aria-expanded', 'false');
}

toggle?.addEventListener('click', () => {
  const isOpen = sidebar?.classList.toggle('is-open') ?? false;
  if (overlay) overlay.hidden = !isOpen;
  toggle.setAttribute('aria-expanded', String(isOpen));
});

overlay?.addEventListener('click', closeSidebar);
document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') closeSidebar();
});

document.addEventListener('submit', (event) => {
  const form = event.target.closest('form[data-confirm]');
  if (form && !window.confirm(form.dataset.confirm || 'Lanjutkan aksi ini?')) {
    event.preventDefault();
  }
});
