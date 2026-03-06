import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

// ─── Header: scroll blur + active nav link ───────────────────
(function initHeader() {
  const header = document.getElementById('sv-header');
  if (!header) return;

  // Scroll-aware blur
  const onScroll = () => {
    header.classList.toggle('is-scrolled', window.scrollY > 50);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mark active nav link by URL prefix
  const current = window.location.href.split('?')[0].replace(/\/$/, '');
  header.querySelectorAll('.sv-nav a').forEach(a => {
    const href = a.href.split('?')[0].replace(/\/$/, '');
    if (href && href !== window.location.origin && current.startsWith(href)) {
      a.classList.add('active');
    }
  });
})();

// ─── Mobile nav toggle ───────────────────────────────────────
(function initMobileNav() {
  const toggle  = document.getElementById('sv-mobile-toggle');
  const wrapper = document.getElementById('sv-nav-wrapper');
  if (!toggle || !wrapper) return;

  toggle.addEventListener('click', () => {
    const isOpen = wrapper.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });

  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!toggle.contains(e.target) && !wrapper.contains(e.target)) {
      wrapper.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
})();

// ─── Property gallery ────────────────────────────────────────
(function initGallery() {
  const mainImg  = document.getElementById('sv-gallery-main-img');
  const thumbs   = document.querySelectorAll('#sv-gallery-thumbs .sv-gallery__thumb');
  const counter  = document.getElementById('sv-gallery-counter');
  const prevBtn  = document.getElementById('sv-gallery-prev');
  const nextBtn  = document.getElementById('sv-gallery-next');

  if (!mainImg || thumbs.length === 0) return;

  let currentIndex = 0;
  const images = Array.from(thumbs).map(t => ({
    url: t.querySelector('img')?.src || '',
    alt: t.querySelector('img')?.alt || '',
  }));

  const goTo = (index) => {
    currentIndex = (index + images.length) % images.length;
    mainImg.src = images[currentIndex].url;
    mainImg.alt = images[currentIndex].alt;

    thumbs.forEach((t, i) => t.classList.toggle('active', i === currentIndex));

    if (counter) counter.textContent = currentIndex + 1;

    // Smooth scroll thumb into view
    thumbs[currentIndex]?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  };

  thumbs.forEach((thumb, i) => {
    thumb.addEventListener('click', () => goTo(i));
  });

  if (prevBtn) prevBtn.addEventListener('click', () => goTo(currentIndex - 1));
  if (nextBtn) nextBtn.addEventListener('click', () => goTo(currentIndex + 1));

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (!document.getElementById('sv-gallery')) return;
    if (e.key === 'ArrowLeft')  goTo(currentIndex - 1);
    if (e.key === 'ArrowRight') goTo(currentIndex + 1);
  });

  // Touch/swipe support
  let touchStartX = 0;
  mainImg.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
  mainImg.addEventListener('touchend', (e) => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) > 50) goTo(dx < 0 ? currentIndex + 1 : currentIndex - 1);
  });
})();

// ─── Filter sidebar auto-submit ──────────────────────────────
(function initFilterAutoSubmit() {
  const selects = document.querySelectorAll('.sv-filter-sidebar select');
  selects.forEach(select => {
    select.addEventListener('change', () => {
      select.closest('form')?.submit();
    });
  });
})();

// ─── Stat counter animation ──────────────────────────────────
(function initCounters() {
  const elements = document.querySelectorAll('[data-count]');
  if (elements.length === 0) return;

  const animateCounter = (el) => {
    const target = parseInt(el.dataset.count, 10);
    if (isNaN(target)) return;

    const duration = 1500;
    const start    = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased    = 1 - Math.pow(1 - progress, 3); // ease-out-cubic
      el.textContent = Math.round(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  elements.forEach(el => observer.observe(el));
})();

// ─── WhatsApp float show/hide on scroll ──────────────────────
(function initWhatsAppVisibility() {
  const waFloat = document.getElementById('sv-whatsapp-float');
  if (!waFloat) return;

  // Show after 300px scroll
  let shown = false;
  window.addEventListener('scroll', () => {
    const should = window.scrollY > 300;
    if (should !== shown) {
      shown = should;
      waFloat.style.opacity    = shown ? '1' : '0';
      waFloat.style.transform  = shown ? 'translateY(0)' : 'translateY(20px)';
      waFloat.style.pointerEvents = shown ? 'auto' : 'none';
    }
  }, { passive: true });

  // Initial state
  waFloat.style.transition   = 'opacity 0.3s ease, transform 0.3s ease';
  waFloat.style.opacity      = '0';
  waFloat.style.transform    = 'translateY(20px)';
  waFloat.style.pointerEvents = 'none';
})();

// ─── Search bar select styling (add arrow) ───────────────────
(function styleSelects() {
  const selects = document.querySelectorAll('.sv-search-field select, .sv-filter-input');
  selects.forEach(sel => {
    sel.style.backgroundImage = `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%231B3A8A' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E")`;
    sel.style.backgroundRepeat   = 'no-repeat';
    sel.style.backgroundPosition = 'right 0.875rem center';
    sel.style.paddingRight       = '2.5rem';
  });
})();

// ─── Google Maps initializer (called by Maps API callback) ───
window.initSvMap = function () {
  const mapEl = document.getElementById('sv-map');
  if (!mapEl || !window.google) return;

  const lat   = parseFloat(mapEl.dataset.lat);
  const lng   = parseFloat(mapEl.dataset.lng);
  const title = mapEl.dataset.title || '';

  const map = new google.maps.Map(mapEl, {
    center: { lat, lng },
    zoom: 15,
    mapTypeControl: false,
    streetViewControl: true,
    fullscreenControl: true,
    styles: [
      { featureType: 'water',       elementType: 'geometry', stylers: [{ color: '#A8D5F5' }] },
      { featureType: 'road',        elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
      { featureType: 'landscape',   elementType: 'geometry', stylers: [{ color: '#f2f2f2' }] },
      { featureType: 'poi.park',    elementType: 'geometry', stylers: [{ color: '#c5e8c5' }] },
    ],
  });

  new google.maps.Marker({
    position: { lat, lng },
    map,
    title,
    icon: {
      path: google.maps.SymbolPath.CIRCLE,
      fillColor:    '#1B3A8A',
      fillOpacity:  1,
      strokeColor:  '#F0A500',
      strokeWeight: 3,
      scale:        12,
    },
  });
};

// ─── Smooth anchor scroll ────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
