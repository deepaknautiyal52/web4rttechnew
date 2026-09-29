(function () {
  'use strict';

  // Mobile navigation menu
  var toggle = document.getElementById('menu-toggle');
  var nav = document.getElementById('site-nav');
  var backdrop = document.getElementById('nav-backdrop');

  function setMenu(open) {
    if (!toggle || !nav) return;
    nav.classList.toggle('active', open);
    toggle.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (backdrop) backdrop.hidden = !open;
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      setMenu(!nav.classList.contains('active'));
    });
  }
  if (backdrop) backdrop.addEventListener('click', function () { setMenu(false); });
  if (nav) {
    nav.addEventListener('click', function (e) {
      if (e.target.closest('a')) setMenu(false);
    });
  }

  // Home page hero slider: autoplay every 6s, paused while hovered.
  var slider = document.querySelector('[data-hero-slider]');
  if (slider) {
    var slides = slider.querySelectorAll('.hero-slide');
    var dots = slider.querySelectorAll('.hero-slider-dot');
    var active = 0;
    var timer = null;
    var paused = false;

    var goTo = function (index) {
      active = (index + slides.length) % slides.length;
      slides.forEach(function (s, i) {
        s.classList.toggle('active', i === active);
        s.setAttribute('aria-hidden', i === active ? 'false' : 'true');
      });
      dots.forEach(function (d, i) { d.classList.toggle('active', i === active); });
      restart();
    };

    var restart = function () {
      clearInterval(timer);
      if (!paused) timer = setInterval(function () { goTo(active + 1); }, 6000);
    };

    slider.querySelector('[data-slide-prev]').addEventListener('click', function () { goTo(active - 1); });
    slider.querySelector('[data-slide-next]').addEventListener('click', function () { goTo(active + 1); });
    dots.forEach(function (d) {
      d.addEventListener('click', function () { goTo(parseInt(d.getAttribute('data-slide-to'), 10)); });
    });
    slider.addEventListener('mouseenter', function () { paused = true; clearInterval(timer); });
    slider.addEventListener('mouseleave', function () { paused = false; restart(); });
    restart();
  }

  // Service FAQ accordion
  document.querySelectorAll('[data-faq]').forEach(function (item) {
    var button = item.querySelector('.faq-question');
    var answer = item.querySelector('.faq-answer');
    var sign = item.querySelector('.faq-toggle');
    button.addEventListener('click', function () {
      var open = !item.classList.contains('open');
      item.classList.toggle('open', open);
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      answer.hidden = !open;
      sign.textContent = open ? '−' : '+';
    });
  });

  // Ventures page: footer links point at /ventures#<id>; scroll that card into view.
  if (location.hash && document.querySelector('.venture-grid')) {
    var target = document.getElementById(location.hash.slice(1));
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // "Sending..." label while a form is submitting.
  document.querySelectorAll('form[data-submitting-label]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.textContent = form.getAttribute('data-submitting-label');
      }
    });
  });
})();
