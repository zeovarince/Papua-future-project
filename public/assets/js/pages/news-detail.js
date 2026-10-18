document.querySelector('[data-action=\"copy-link\"]')?.addEventListener('click', copyLink);


// ── Sticky Navbar ──
    // ── Copy Link ──
    function copyLink() {
      const url = window.location.href;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => showCopyToast());
      } else {
        const el = document.createElement('textarea');
        el.value = url;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        showCopyToast();
      }
    }

    function showCopyToast() {
      const toast = document.getElementById('copy-toast');
      toast.style.opacity = '1';
      setTimeout(() => { toast.style.opacity = '0'; }, 2000);
    }
