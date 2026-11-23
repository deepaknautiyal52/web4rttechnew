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

  // Domain availability search (see partials/domain-search.blade.php).
  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (text) node.textContent = text;
    return node;
  }

  function registerLink(result, label) {
    var a = el('a', 'domain-result-btn', label);
    a.href = result.register_url;
    return a;
  }

  var STATUS_LABEL = { available: 'Available', taken: 'Taken', unknown: 'Ask us' };

  function renderDomainResults(box, data) {
    var q = data.query;
    box.innerHTML = '';

    var main = el('div', 'domain-result-main domain-result-' + q.status);
    var copy = el('div', 'domain-result-copy');
    var headline = el('p', 'domain-result-headline');
    var name = el('strong', null, q.domain);
    if (q.status === 'available') {
      headline.appendChild(document.createTextNode('🎉 Congratulations! '));
      headline.appendChild(name);
      headline.appendChild(document.createTextNode(' is available.'));
      copy.appendChild(headline);
      copy.appendChild(el('p', 'domain-result-sub', q.price + '/year for the first year (+ GST). Register it before someone else does.'));
    } else if (q.status === 'taken') {
      headline.appendChild(document.createTextNode('Sorry, '));
      headline.appendChild(name);
      headline.appendChild(document.createTextNode(' is already taken.'));
      copy.appendChild(headline);
      copy.appendChild(el('p', 'domain-result-sub', 'Try another name, or pick one of the extensions below.'));
    } else {
      headline.appendChild(document.createTextNode('We could not check '));
      headline.appendChild(name);
      headline.appendChild(document.createTextNode(' automatically.'));
      copy.appendChild(headline);
      copy.appendChild(el('p', 'domain-result-sub', 'Send us an enquiry and we will confirm its availability for you.'));
    }
    main.appendChild(copy);
    if (q.status !== 'taken') {
      main.appendChild(registerLink(q, q.status === 'available' ? 'Register now →' : 'Ask us →'));
    }
    box.appendChild(main);

    if (data.suggestions.length) {
      box.appendChild(el('p', 'domain-result-more', 'Other extensions for this name'));
      var list = el('ul', 'domain-result-list');
      data.suggestions.forEach(function (s) {
        var li = el('li', 'domain-result-row domain-result-' + s.status);
        li.appendChild(el('span', 'domain-result-name', s.domain));
        li.appendChild(el('span', 'domain-result-pill', STATUS_LABEL[s.status]));
        li.appendChild(el('span', 'domain-result-price', s.status === 'taken' ? '' : s.price + '/yr'));
        if (s.status === 'taken') {
          li.appendChild(el('span', 'domain-result-btn domain-result-btn-disabled', 'Unavailable'));
        } else {
          li.appendChild(registerLink(s, s.status === 'available' ? 'Register' : 'Ask us'));
        }
        list.appendChild(li);
      });
      box.appendChild(list);
    }
    box.hidden = false;
  }

  document.querySelectorAll('[data-domain-search]').forEach(function (widget) {
    var form = widget.querySelector('form');
    var input = widget.querySelector('[data-domain-input]');
    var ext = widget.querySelector('[data-domain-ext]');
    var btn = widget.querySelector('[data-domain-btn]');
    var box = widget.querySelector('[data-domain-results]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var value = input.value.trim();
      if (!value) {
        input.focus();
        return;
      }
      btn.disabled = true;
      btn.textContent = 'Searching...';
      box.hidden = false;
      box.innerHTML = '';
      box.appendChild(el('p', 'domain-result-loading', 'Checking availability with the registry...'));

      var url = widget.getAttribute('data-endpoint') + '?domain=' + encodeURIComponent(value) + '&ext=' + encodeURIComponent(ext.value);
      fetch(url, { headers: { Accept: 'application/json' } })
        .then(function (res) {
          return res.json().catch(function () { return {}; }).then(function (body) {
            if (res.status === 429) throw new Error('Too many searches. Please wait a minute and try again.');
            if (!res.ok) throw new Error(body.error || 'Something went wrong. Please try again.');
            return body;
          });
        })
        .then(function (data) { renderDomainResults(box, data); })
        .catch(function (err) {
          box.innerHTML = '';
          // fetch() itself rejects with a TypeError when the network is down.
          box.appendChild(el('p', 'domain-result-error', err instanceof TypeError ? 'Could not reach the server. Check your connection and try again.' : err.message));
        })
        .then(function () {
          btn.disabled = false;
          btn.textContent = 'Search';
        });
    });
  });

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
