/**
 * Papua Future Project - Admin Panel UI Interaction
 * According to DESIGN-ADMIN.md sections 3, 4.7, 4.8, 4.13
 */

(function () {
  'use strict';

  // ==========================================================================
  // 1. Rail & Flyout Interaction (Section 3.2 & 3.3)
  // ==========================================================================
  const rail = document.querySelector('[data-admin-rail]');
  const flyoutWraps = document.querySelectorAll('.rail-item-wrap');
  let openFlyoutTimer = null;
  let closeFlyoutTimer = null;
  let currentOpenWrap = null;
  let isAnyFlyoutOpen = false;

  function closeAllFlyouts() {
    flyoutWraps.forEach((wrap) => {
      wrap.classList.remove('is-open');
      const flyout = wrap.querySelector('.admin-flyout');
      if (flyout) flyout.classList.remove('is-visible');
      const trigger = wrap.querySelector('[aria-haspopup]');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
    currentOpenWrap = null;
    isAnyFlyoutOpen = false;
  }

  function openFlyout(wrap) {
    if (!wrap) return;
    if (currentOpenWrap === wrap && isAnyFlyoutOpen) return;

    // Position flyout relative to trigger button
    const trigger = wrap.querySelector('[aria-haspopup]');
    const flyout = wrap.querySelector('.admin-flyout');

    if (!flyout) return;

    // Close any previous without animation lag
    flyoutWraps.forEach((w) => {
      if (w !== wrap) {
        w.classList.remove('is-open');
        const f = w.querySelector('.admin-flyout');
        if (f) f.classList.remove('is-visible');
        const t = w.querySelector('[aria-haspopup]');
        if (t) t.setAttribute('aria-expanded', 'false');
      }
    });

    // Check viewport boundary to prevent overflowing bottom
    if (trigger && !flyout.classList.contains('flyout-profile')) {
      const triggerRect = trigger.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      const flyoutHeight = flyout.offsetHeight || 260; // Estimated height
      let topPos = triggerRect.top;

      if (topPos + flyoutHeight > viewportHeight - 16) {
        topPos = Math.max(16, viewportHeight - flyoutHeight - 16);
      }
      flyout.style.top = `${topPos}px`;
      flyout.style.position = 'fixed';
    }

    wrap.classList.add('is-open');
    flyout.classList.add('is-visible');
    if (trigger) trigger.setAttribute('aria-expanded', 'true');

    currentOpenWrap = wrap;
    isAnyFlyoutOpen = true;
  }

  flyoutWraps.forEach((wrap) => {
    const trigger = wrap.querySelector('[aria-haspopup]');
    const flyout = wrap.querySelector('.admin-flyout');
    if (!trigger || !flyout) return;

    // Hover Enter
    wrap.addEventListener('mouseenter', () => {
      clearTimeout(closeFlyoutTimer);
      if (isAnyFlyoutOpen) {
        // Instant switch if another flyout was already open
        openFlyout(wrap);
      } else {
        // Open delay: 80ms
        clearTimeout(openFlyoutTimer);
        openFlyoutTimer = setTimeout(() => {
          openFlyout(wrap);
        }, 80);
      }
    });

    // Hover Leave
    wrap.addEventListener('mouseleave', () => {
      clearTimeout(openFlyoutTimer);
      // Close delay: 150ms
      closeFlyoutTimer = setTimeout(() => {
        closeAllFlyouts();
      }, 150);
    });

    // Keyboard Focus: Tab to trigger opens flyout
    trigger.addEventListener('focus', () => {
      clearTimeout(closeFlyoutTimer);
      openFlyout(wrap);
    });

    // Keyboard Navigation inside flyout
    wrap.addEventListener('keydown', (e) => {
      const items = Array.from(flyout.querySelectorAll('a[role="menuitem"], button[role="menuitem"]'));
      const currentIndex = items.indexOf(document.activeElement);

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        items[nextIndex]?.focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        items[prevIndex]?.focus();
      } else if (e.key === 'Escape') {
        e.preventDefault();
        closeAllFlyouts();
        trigger.focus();
      } else if (e.key === 'Tab') {
        // Tab out closes flyout
        setTimeout(() => {
          if (!wrap.contains(document.activeElement)) {
            closeAllFlyouts();
          }
        }, 10);
      }
    });

    // Touch devices & click handling: first tap opens flyout instead of navigating
    trigger.addEventListener('click', (e) => {
      const hasFlyout = Boolean(flyout);
      const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || window.matchMedia('(pointer: coarse)').matches;

      if (hasFlyout && (isTouch || !wrap.classList.contains('is-open'))) {
        if (!wrap.classList.contains('is-open')) {
          e.preventDefault();
          openFlyout(wrap);
        }
      }
    });
  });

  // Touch outside closes flyouts
  document.addEventListener('click', (e) => {
    if (rail && !rail.contains(e.target)) {
      closeAllFlyouts();
    }
  });

  // ==========================================================================
  // 2. Mobile Drawer (< 768px) (Section 3.6)
  // ==========================================================================
  const drawer = document.querySelector('[data-mobile-drawer]');
  const drawerBackdrop = document.querySelector('[data-drawer-backdrop]');
  const drawerOpenBtn = document.querySelector('[data-drawer-open]');
  const drawerCloseBtn = document.querySelector('[data-drawer-close]');

  function openDrawer() {
    if (!drawer) return;
    drawer.classList.add('is-open');
    if (drawerBackdrop) {
      drawerBackdrop.classList.add('is-open');
      drawerBackdrop.removeAttribute('hidden');
    }
    drawerOpenBtn?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';

    // Focus first link in drawer
    const firstLink = drawer.querySelector('a, button');
    firstLink?.focus();
  }

  function closeDrawer() {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    if (drawerBackdrop) {
      drawerBackdrop.classList.remove('is-open');
      drawerBackdrop.setAttribute('hidden', '');
    }
    drawerOpenBtn?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  drawerOpenBtn?.addEventListener('click', openDrawer);
  drawerCloseBtn?.addEventListener('click', closeDrawer);
  drawerBackdrop?.addEventListener('click', closeDrawer);

  // Close drawer on link click in mobile view
  drawer?.querySelectorAll('.drawer-link').forEach((link) => {
    link.addEventListener('click', () => {
      if (window.innerWidth < 768) closeDrawer();
    });
  });

  // Mobile Drawer Accordion
  document.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const content = trigger.nextElementSibling;
      const isExpanded = trigger.getAttribute('aria-expanded') === 'true';

      trigger.setAttribute('aria-expanded', String(!isExpanded));
      content?.classList.toggle('is-open', !isExpanded);
    });
  });

  // Close drawer on resize >= 768px
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768 && drawer?.classList.contains('is-open')) {
      closeDrawer();
    }
  });

  // ==========================================================================
  // 3. Confirm Delete Dialog (Section 4.8)
  // Modal z-index 100, overlay black 60%, item name, Secondary & Danger buttons
  // ==========================================================================
  const confirmDialog = document.getElementById('confirmDialog');
  const confirmDesc = document.getElementById('confirmDialogDesc');
  const confirmCancelBtn = document.querySelector('[data-confirm-cancel]');
  const confirmProceedBtn = document.querySelector('[data-confirm-proceed]');
  let pendingForm = null;
  let activeElementBeforeDialog = null;

  function showConfirmDialog(form, message) {
    if (!confirmDialog) return;
    pendingForm = form;
    activeElementBeforeDialog = document.activeElement;

    if (confirmDesc && message) {
      confirmDesc.textContent = message;
    }

    confirmDialog.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';

    // Focus cancel button by default
    confirmCancelBtn?.focus();
  }

  function hideConfirmDialog() {
    if (!confirmDialog) return;
    confirmDialog.setAttribute('hidden', '');
    document.body.style.overflow = '';
    pendingForm = null;

    if (activeElementBeforeDialog && typeof activeElementBeforeDialog.focus === 'function') {
      activeElementBeforeDialog.focus();
    }
  }

  confirmCancelBtn?.addEventListener('click', hideConfirmDialog);
  confirmProceedBtn?.addEventListener('click', () => {
    if (pendingForm) {
      const formToSubmit = pendingForm;
      hideConfirmDialog();
      formToSubmit.submit();
    }
  });

  // Close on backdrop click
  confirmDialog?.addEventListener('click', (e) => {
    if (e.target === confirmDialog) {
      hideConfirmDialog();
    }
  });

  // Trap focus in dialog and close on Escape
  confirmDialog?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      e.preventDefault();
      hideConfirmDialog();
    } else if (e.key === 'Tab') {
      const focusables = confirmDialog.querySelectorAll('button:not([disabled])');
      const first = focusables[0];
      const last = focusables[focusables.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  // Intercept form submissions with data-confirm
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-confirm]');
    if (form) {
      e.preventDefault();
      const message = form.getAttribute('data-confirm') || 'Apakah kamu yakin ingin menghapus data ini?';
      showConfirmDialog(form, message);
    }
  });

  // ==========================================================================
  // 4. Toast Notifications (Section 4.7)
  // Auto-dismiss 4 seconds (4000ms), manual close button
  // ==========================================================================
  document.querySelectorAll('[data-toast]').forEach((toast) => {
    const closeBtn = toast.querySelector('[data-toast-close]');

    function dismissToast() {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(16px)';
      setTimeout(() => {
        toast.remove();
      }, 200);
    }

    closeBtn?.addEventListener('click', dismissToast);

    // Auto-dismiss after 4000ms
    setTimeout(dismissToast, 4000);
  });

  // Global helper to show toast programmatically if needed
  window.showAdminToast = function (message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

    toast.innerHTML = `
      <div class="toast-body">${message}</div>
      <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
    `;

    container.appendChild(toast);
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn?.addEventListener('click', () => toast.remove());
    setTimeout(() => toast.remove(), 4000);
  };

  // ==========================================================================
  // 5. Global Escape Key Handler
  // ==========================================================================
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (confirmDialog && !confirmDialog.hasAttribute('hidden')) {
        hideConfirmDialog();
      } else if (drawer && drawer.classList.contains('is-open')) {
        closeDrawer();
      } else if (isAnyFlyoutOpen) {
        closeAllFlyouts();
      }
    }
  });

  // ==========================================================================
  // 6. Generic Table Search, Filter & Auto-Submit (CSP Compliant)
  // ==========================================================================
  const founderToggle = document.querySelector('[data-founder-toggle]');
  const founderBioField = document.querySelector('[data-founder-bio-field]');
  const founderBioInput = founderBioField?.querySelector('textarea[name="long_bio"]');
  const founderPhotoField = document.querySelector('[data-founder-photo-field]');
  const founderPhotoInput = founderPhotoField?.querySelector('input[name="home_photo"]');
  if (founderToggle) {
    const syncFounderBio = () => {
      if (founderBioField && founderBioInput) {
        founderBioField.hidden = !founderToggle.checked;
        founderBioInput.disabled = !founderToggle.checked;
      }
      if (founderPhotoField && founderPhotoInput) {
        founderPhotoField.hidden = !founderToggle.checked;
        founderPhotoInput.disabled = !founderToggle.checked;
      }
    };
    founderToggle.addEventListener('change', syncFounderBio);
    syncFounderBio();
  }

  // Auto-submit inputs & filter selects
  document.addEventListener('change', (e) => {
    if (e.target && e.target.matches('[data-auto-submit]')) {
      e.target.form?.submit();
    }
    if (e.target && e.target.matches('[data-period-select]')) {
      const baseUrl = e.target.getAttribute('data-period-base') || window.location.pathname;
      window.location.href = baseUrl + '?periode=' + encodeURIComponent(e.target.value);
    }
    if (e.target && e.target.matches('[data-question-type-select]')) {
      const val = e.target.value;
      const optionsGroup = document.getElementById('optionsGroup');
      if (optionsGroup) {
        const needsOptions = ['RADIO', 'CHECKBOX', 'SELECT'].includes(val);
        optionsGroup.style.display = needsOptions ? '' : 'none';
        const textarea = optionsGroup.querySelector('textarea');
        if (textarea) textarea.required = needsOptions;
      }
    }
    if (e.target && e.target.matches('[data-filter-target]')) {
      const targetSelector = e.target.getAttribute('data-filter-target');
      const filterKey = e.target.getAttribute('data-filter-key') || 'type';
      const val = e.target.value.toLowerCase();
      document.querySelectorAll(targetSelector).forEach((row) => {
        const rowVal = (row.dataset[filterKey] || '').toLowerCase();
        row.style.display = (!val || rowVal === val) ? '' : 'none';
      });
    }
  });

  // Live search inputs
  document.addEventListener('input', (e) => {
    if (e.target && e.target.matches('[data-search-target]')) {
      const targetSelector = e.target.getAttribute('data-search-target');
      const query = e.target.value.toLowerCase().trim();
      document.querySelectorAll(targetSelector).forEach((row) => {
        const text = (row.textContent || '').toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    }
  });
})();
