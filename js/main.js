/* ============================================================
   ALIGATOR — main.js
   ============================================================ */

(function () {
  'use strict';

  /* ── Marca body como JS carregado (ativa animações reveal) ─ */
  document.documentElement.classList.add('js-loaded');

  /* ── Navbar scroll ─────────────────────────────────────── */
  const navbar = document.getElementById('navbar');
  if (navbar) {
    const onScroll = () => {
      navbar.classList.toggle('scrolled', window.scrollY > 30);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ── Mobile menu ───────────────────────────────────────── */
  const ham  = document.getElementById('hamburger');
  const menu = document.getElementById('mobileMenu');
  if (ham && menu) {
    ham.addEventListener('click', () => {
      const open = ham.classList.toggle('open');
      menu.classList.toggle('open', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
    menu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        ham.classList.remove('open');
        menu.classList.remove('open');
        document.body.style.overflow = '';
      });
    });
  }

  /* ── Scroll reveal ─────────────────────────────────────── */
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          revealObserver.unobserve(e.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );
  document.querySelectorAll('[data-reveal]').forEach(el => revealObserver.observe(el));

  /* ── Counter animation ─────────────────────────────────── */
  function animateCounter(el) {
    const target = parseFloat(el.dataset.target);
    const suffix = el.dataset.suffix || '';
    const duration = 1800;
    const start = performance.now();
    const isFloat = target % 1 !== 0;

    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const value = target * eased;
      el.textContent = (isFloat ? value.toFixed(1) : Math.round(value)) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  }

  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          animateCounter(e.target);
          counterObserver.unobserve(e.target);
        }
      });
    },
    { threshold: 0.5 }
  );
  document.querySelectorAll('[data-target]').forEach(el => counterObserver.observe(el));

  /* ── Dashboard bar animation ───────────────────────────── */
  const bars = document.querySelectorAll('.dash-chart-bars .bar');
  if (bars.length) {
    const heights = [35, 55, 42, 70, 58, 85, 62, 90, 75, 100, 80, 95];
    bars.forEach((b, i) => {
      b.style.height = '0';
      setTimeout(() => {
        b.style.height = (heights[i % heights.length] || 60) + '%';
      }, 300 + i * 60);
    });
  }

  /* ── Form submission ───────────────────────────────────── */
  document.querySelectorAll('[data-lead-form]').forEach(form => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const btn = form.querySelector('[type=submit]');
      const feedback = form.querySelector('.form-feedback');
      const originalText = btn.innerHTML;

      btn.innerHTML = '<span class="loading"><span></span><span></span><span></span></span>';
      btn.disabled = true;

      const data = new FormData(form);
      // Add UTM from sessionStorage if available
      ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'].forEach(k => {
        const v = sessionStorage.getItem(k);
        if (v) data.append(k, v);
      });

      try {
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        const res  = await fetch(baseUrl + '/ajax/lead.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.ok) {
          // GA4 event
          if (typeof gtag !== 'undefined') {
            gtag('event', 'generate_lead', { form_id: form.dataset.leadForm });
          }
          // Redirect to thank you page
          const base = document.querySelector('meta[name="base-url"]')?.content || '';
          window.location.href = base + '/obrigado';
        } else {
          throw new Error(json.message || 'Erro ao enviar');
        }
      } catch (err) {
        if (feedback) {
          feedback.textContent = err.message || 'Ocorreu um erro. Tente novamente.';
          feedback.className = 'form-feedback error';
        }
      } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    });
  });

  /* ── UTM persistence ───────────────────────────────────── */
  (function captureUtms() {
    const params = new URLSearchParams(window.location.search);
    const keys   = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'];
    keys.forEach(k => {
      if (params.has(k)) sessionStorage.setItem(k, params.get(k));
    });
    if (!sessionStorage.getItem('landing_page')) {
      sessionStorage.setItem('landing_page', window.location.href);
    }
  })();

  /* ── Smooth anchor ─────────────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = 80;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  });

  /* ── Active nav link ───────────────────────────────────── */
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-links a').forEach(a => {
    const aPath = new URL(a.href, document.baseURI).pathname;
    if (aPath === currentPath ||
        (currentPath.length > 1 && aPath.length > 1 &&
         currentPath.startsWith(aPath))) {
      a.classList.add('active');
    }
  });

  /* ── Tabs (soluções) ───────────────────────────────────── */
  document.querySelectorAll('[data-tabs]').forEach(container => {
    const triggers = container.querySelectorAll('[data-tab]');
    const panels   = container.querySelectorAll('[data-panel]');
    triggers.forEach(trigger => {
      trigger.addEventListener('click', () => {
        triggers.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));
        trigger.classList.add('active');
        const target = container.querySelector(`[data-panel="${trigger.dataset.tab}"]`);
        if (target) target.classList.add('active');
      });
    });
  });

})();
