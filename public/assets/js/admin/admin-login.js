const loginSlides = Array.from(document.querySelectorAll('.login-slide'));
const loginSlideButtons = Array.from(document.querySelectorAll('[data-slide-to]'));
const loginSlideHeading = document.querySelector('[data-login-heading]');
const slideHeadings = [
  'Setiap anak berhak atas masa depan yang cerah.',
  'Pojok baca untuk berantas buta aksara di pelosok Papua.',
  'Bergerak bersama masyarakat lokal dan generasi muda.',
];
const reducedLoginMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
let currentLoginSlide = 0;
let loginSlideInterval = null;

const showLoginSlide = (index) => {
  if (loginSlides.length === 0) return;

  currentLoginSlide = (index + loginSlides.length) % loginSlides.length;
  loginSlides.forEach((slide, slideIndex) => {
    const active = slideIndex === currentLoginSlide;
    slide.classList.toggle('is-active', active);
    slide.setAttribute('aria-hidden', String(!active));
  });

  loginSlideButtons.forEach((button, buttonIndex) => {
    const active = buttonIndex === currentLoginSlide;
    button.classList.toggle('is-active', active);
    button.setAttribute('aria-pressed', String(active));
  });

  if (loginSlideHeading) loginSlideHeading.textContent = slideHeadings[currentLoginSlide] || slideHeadings[0];
};

const stopLoginSlider = () => {
  if (loginSlideInterval !== null) {
    window.clearInterval(loginSlideInterval);
    loginSlideInterval = null;
  }
};

const startLoginSlider = () => {
  stopLoginSlider();
  if (reducedLoginMotion.matches || loginSlides.length < 2 || document.hidden) return;
  loginSlideInterval = window.setInterval(() => showLoginSlide(currentLoginSlide + 1), 5000);
};

loginSlideButtons.forEach((button) => {
  button.addEventListener('click', () => {
    showLoginSlide(Number(button.dataset.slideTo));
    startLoginSlider();
  });
});

const loginSlider = document.querySelector('[data-login-slider]');
if (loginSlider) {
  loginSlider.addEventListener('pointerenter', stopLoginSlider);
  loginSlider.addEventListener('pointerleave', startLoginSlider);
  loginSlider.addEventListener('focusin', stopLoginSlider);
  loginSlider.addEventListener('focusout', (event) => {
    if (!loginSlider.contains(event.relatedTarget)) startLoginSlider();
  });
}

document.addEventListener('visibilitychange', () => {
  if (document.hidden) stopLoginSlider();
  else startLoginSlider();
});

reducedLoginMotion.addEventListener('change', startLoginSlider);
showLoginSlide(0);
startLoginSlider();

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
  button.addEventListener('click', () => {
    const passwordField = document.getElementById(button.dataset.passwordToggle);
    if (!passwordField) return;

    const visible = passwordField.type === 'text';
    passwordField.type = visible ? 'password' : 'text';
    button.setAttribute('aria-pressed', String(!visible));
    button.setAttribute('aria-label', visible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
    passwordField.focus();
  });
});

const forgotModal = document.querySelector('[data-forgot-modal]');
let forgotModalTrigger = null;

const closeForgotModal = () => {
  if (!forgotModal) return;
  forgotModal.hidden = true;
  forgotModalTrigger?.focus();
};

document.querySelectorAll('[data-open-forgot]').forEach((button) => {
  button.addEventListener('click', () => {
    if (!forgotModal) return;
    forgotModalTrigger = button;
    forgotModal.hidden = false;
    forgotModal.querySelector('[data-close-forgot]')?.focus();
  });
});

document.querySelectorAll('[data-close-forgot]').forEach((button) => {
  button.addEventListener('click', closeForgotModal);
});

if (forgotModal) {
  forgotModal.addEventListener('click', (event) => {
    if (event.target === forgotModal) closeForgotModal();
  });
}

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && forgotModal && !forgotModal.hidden) closeForgotModal();
});

const toastContainer = document.querySelector('[data-login-toast]');
document.querySelectorAll('[data-google-notice]').forEach((button) => {
  button.addEventListener('click', () => {
    if (!toastContainer) return;
    const toast = document.createElement('div');
    toast.className = 'login-toast';
    toast.textContent = 'Integrasi masuk dengan Google belum tersedia. Silakan gunakan email dan kata sandi admin.';
    toastContainer.replaceChildren(toast);
    window.setTimeout(() => toast.remove(), 4500);
  });
});
