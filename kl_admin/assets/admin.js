// Admin sidebar hamburger toggle
(function () {
  const sidebar = document.querySelector('.sidebar');
  const toggleBtn = document.querySelector('.sidebar-toggle');
  if (!sidebar || !toggleBtn) return;

  const toggle = () => {
    sidebar.classList.toggle('active');
    const expanded = sidebar.classList.contains('active');
    toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  };

  toggleBtn.addEventListener('click', (e) => {
    e.preventDefault();
    toggle();
  });

  // Optional: collapse when navigating on small screens
  const links = sidebar.querySelectorAll('a');
  links.forEach((a) => {
    a.addEventListener('click', () => {
      if (window.matchMedia('(max-width: 768px)').matches) {
        sidebar.classList.remove('active');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  });

  // Login success GIF overlay: auto-hide after 5 seconds
  const gifOverlay = document.getElementById('loginGifOverlay');
  if (gifOverlay) {
    setTimeout(() => {
      gifOverlay.classList.add('hidden');
      // Remove from DOM after fade-out completes
      setTimeout(() => {
        if (gifOverlay.parentNode) {
          gifOverlay.parentNode.removeChild(gifOverlay);
        }
      }, 450);
    }, 5000);
  }
})();