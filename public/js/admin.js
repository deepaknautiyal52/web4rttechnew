(function () {
  'use strict';

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var INTERACTIVE = 'a, button, input, select, textarea, label, form';

  // Mobile sidebar toggle
  var shell = $('#admin-shell');
  var navToggle = $('#admin-nav-toggle');
  if (shell && navToggle) {
    navToggle.addEventListener('click', function () {
      var open = shell.classList.toggle('admin-nav-open');
      navToggle.textContent = open ? '×' : '☰';
    });
    $$('.admin-nav a').forEach(function (a) {
      a.addEventListener('click', function () { shell.classList.remove('admin-nav-open'); });
    });
  }

  // Buttons that navigate: <button data-href="..." [data-target="_blank"]>
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-href]');
    if (!btn || btn.disabled) return;
    var href = btn.getAttribute('data-href');
    if (!href) return;
    e.preventDefault();
    if (btn.getAttribute('data-target') === '_blank') window.open(href, '_blank');
    else window.location.href = href;
  });

  // Clickable table rows: open a page, or expand the detail row below.
  $$('tr[data-row-href]').forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest(INTERACTIVE)) return;
      window.location.href = row.getAttribute('data-row-href');
    });
  });
  $$('tr[data-expand]').forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest(INTERACTIVE)) return;
      var detail = document.getElementById(row.getAttribute('data-expand'));
      if (!detail) return;
      var opening = detail.hidden;
      // Only one detail row open at a time, like the original admin.
      $$('tr[data-expand]').forEach(function (r) {
        var d = document.getElementById(r.getAttribute('data-expand'));
        if (d) d.hidden = true;
      });
      detail.hidden = !opening;
    });
  });

  // Confirm before destructive submits: <form data-confirm="...">
  $$('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // Flash messages that fade away.
  $$('[data-autohide]').forEach(function (el) {
    setTimeout(function () { el.hidden = true; }, el.classList.contains('deal-saved-check') ? 2000 : 3000);
  });

  // "Saving..." label while a form submits.
  $$('form[data-submitting-label]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (e.defaultPrevented) return;
      var btn = form.querySelector('button[type="submit"]');
      if (btn) {
        setTimeout(function () {
          btn.disabled = true;
          btn.textContent = form.getAttribute('data-submitting-label');
        }, 0);
      }
    });
  });

  // Filter toolbars: dropdowns apply immediately, search re-queries after a pause.
  $$('form[data-filter-form], form').forEach(function (form) {
    $$('select[data-autosubmit]', form).forEach(function (select) {
      select.addEventListener('change', function () { form.submit(); });
    });
  });
  $$('form[data-filter-form]').forEach(function (form) {
    var input = $('[data-search-input]', form);
    var timer = null;
    if (input) {
      if (input.hasAttribute('autofocus')) {
        var len = input.value.length;
        input.focus();
        input.setSelectionRange(len, len);
      }
      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { form.submit(); }, 400);
      });
    }
    var clear = $('[data-search-clear]', form);
    if (clear) {
      clear.addEventListener('click', function () {
        input.value = '';
        form.submit();
      });
    }
  });

  // Dependent dropdowns (e.g. project list filtered by the chosen client).
  $$('select[data-depends-on]').forEach(function (select) {
    var parent = document.getElementById(select.getAttribute('data-depends-on'));
    if (!parent) return;
    var strict = select.hasAttribute('data-strict');
    var apply = function () {
      var pv = parent.value;
      $$('option[data-parent]', select).forEach(function (opt) {
        var show = strict ? opt.getAttribute('data-parent') === pv : (!pv || opt.getAttribute('data-parent') === pv);
        opt.hidden = !show;
        opt.disabled = !show;
        if (!show && opt.selected) select.value = '';
      });
    };
    parent.addEventListener('change', apply);
    apply();
  });

  // Scroll the add/edit form into view when it opens.
  var resourceForm = $('#resource-form');
  if (resourceForm) resourceForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

  // Money formatting shared with the charts: "₹1,23,457" (Indian grouping).
  function formatMoney(amount, currency) {
    var symbol = currency === 'USD' ? '$' : '₹';
    return symbol + Math.round(Number(amount) || 0).toLocaleString('en-IN');
  }

  // Line-items editor for quotations and invoices.
  $$('[data-line-items]').forEach(function (box) {
    var rows = $('[data-line-rows]', box);
    var currencyEl = document.getElementById(box.getAttribute('data-currency-field'));
    var taxEl = document.getElementById(box.getAttribute('data-tax-field'));
    var pkgSelect = $('[data-package-select]', box);
    var pkgAdd = $('[data-package-add]', box);
    var template = $('[data-line-row]', rows).cloneNode(true);
    $$('.field-error', template).forEach(function (el) { el.remove(); });

    var currency = function () { return currencyEl ? currencyEl.value : 'INR'; };

    function renumber() {
      $$('[data-line-row]', rows).forEach(function (tr, i) {
        $$('[data-field]', tr).forEach(function (input) {
          input.name = 'items[' + i + '][' + input.getAttribute('data-field') + ']';
        });
      });
    }

    function recalc() {
      var subtotal = 0;
      var list = $$('[data-line-row]', rows);
      list.forEach(function (tr) {
        var q = parseFloat($('[data-field="quantity"]', tr).value) || 0;
        var p = parseFloat($('[data-field="unit_price"]', tr).value) || 0;
        subtotal += q * p;
        $('[data-line-amount]', tr).textContent = formatMoney(q * p, currency());
        $('[data-line-remove]', tr).disabled = list.length === 1;
      });
      var rate = taxEl ? (parseFloat(taxEl.value) || 0) : 0;
      var tax = Math.round(subtotal * rate) / 100;
      $('[data-total="subtotal"]', box).textContent = formatMoney(subtotal, currency());
      $('[data-total="tax"]', box).textContent = formatMoney(tax, currency());
      $('[data-total="total"]', box).textContent = formatMoney(subtotal + tax, currency());
      $('[data-tax-label]', box).textContent = 'GST / tax (' + rate + '%)';
    }

    function refreshPackageLabels() {
      $$('option[data-label]', pkgSelect).forEach(function (opt) {
        var price = parseFloat(opt.getAttribute(currency() === 'USD' ? 'data-usd' : 'data-inr')) || 0;
        opt.textContent = opt.getAttribute('data-label') + (price ? ' (' + formatMoney(price, currency()) + ')' : ' (custom quote)');
      });
    }

    function addRow(values) {
      var tr = template.cloneNode(true);
      $('[data-field="description"]', tr).value = values.description || '';
      $('[data-field="quantity"]', tr).value = values.quantity != null ? values.quantity : 1;
      $('[data-field="unit_price"]', tr).value = values.unit_price != null ? values.unit_price : '';
      rows.appendChild(tr);
      renumber();
      recalc();
    }

    rows.addEventListener('input', recalc);
    rows.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-line-remove]');
      if (!btn || btn.disabled) return;
      btn.closest('[data-line-row]').remove();
      renumber();
      recalc();
    });
    $('[data-line-add]', box).addEventListener('click', function () { addRow({}); });

    pkgSelect.addEventListener('change', function () { pkgAdd.disabled = !pkgSelect.value; });
    pkgAdd.addEventListener('click', function () {
      var opt = pkgSelect.selectedOptions[0];
      if (!opt || !opt.value) return;
      var price = parseFloat(opt.getAttribute(currency() === 'USD' ? 'data-usd' : 'data-inr')) || 0;
      var label = opt.getAttribute('data-label');
      var existing = $$('[data-line-row]', rows);
      var values = { description: label + ' package', quantity: 1, unit_price: price || '' };
      // Use the full package description from the pricing guide.
      values.description = opt.getAttribute('data-description') || values.description;
      if (existing.length === 1 && !$('[data-field="description"]', existing[0]).value && !$('[data-field="unit_price"]', existing[0]).value) {
        existing[0].remove();
      }
      addRow(values);
      pkgSelect.value = '';
      pkgAdd.disabled = true;
    });

    if (currencyEl) currencyEl.addEventListener('change', function () { refreshPackageLabels(); recalc(); });
    if (taxEl) taxEl.addEventListener('input', recalc);
    refreshPackageLabels();
    recalc();
  });

  // Leads: show the fields that apply to the chosen deal status.
  $$('form[data-deal-form]').forEach(function (form) {
    var status = $('[data-deal-status]', form);
    var apply = function () {
      var v = status.value;
      $$('[data-show-for]', form).forEach(function (el) {
        var f = el.getAttribute('data-show-for');
        el.hidden = f === 'open' ? (v === 'won' || v === 'lost') : f !== v;
      });
    };
    status.addEventListener('change', apply);
  });

  // Leads: "Log" only enabled once something is typed.
  $$('form[data-activity-form]').forEach(function (form) {
    var body = $('input[name="body"]', form);
    var btn = $('button[type="submit"]', form);
    var apply = function () { btn.disabled = !body.value.trim(); };
    body.addEventListener('input', apply);
    apply();
  });

  /* ------------------------------------------------------------------
   * Charts (Chart.js), styled like the original Recharts dashboards.
   * Buckets data into trailing week/month/quarter/half-year/year periods.
   * ------------------------------------------------------------------ */
  var PERIOD_COUNT = { week: 12, month: 12, quarter: 8, 'half-year': 6, year: 5 };

  function startOfWeek(date) {
    var d = new Date(date);
    var day = d.getDay();
    d.setDate(d.getDate() + ((day === 0 ? -6 : 1) - day)); // Monday first
    d.setHours(0, 0, 0, 0);
    return d;
  }

  function bucketStart(date, period) {
    var d = new Date(date);
    switch (period) {
      case 'week': return startOfWeek(d);
      case 'quarter': return new Date(d.getFullYear(), Math.floor(d.getMonth() / 3) * 3, 1);
      case 'half-year': return new Date(d.getFullYear(), d.getMonth() < 6 ? 0 : 6, 1);
      case 'year': return new Date(d.getFullYear(), 0, 1);
      default: return new Date(d.getFullYear(), d.getMonth(), 1);
    }
  }

  function addPeriod(date, period, n) {
    var d = new Date(date);
    switch (period) {
      case 'week': d.setDate(d.getDate() + n * 7); break;
      case 'quarter': d.setMonth(d.getMonth() + n * 3); break;
      case 'half-year': d.setMonth(d.getMonth() + n * 6); break;
      case 'year': d.setFullYear(d.getFullYear() + n); break;
      default: d.setMonth(d.getMonth() + n);
    }
    return d;
  }

  function bucketLabel(start, period) {
    var yy = String(start.getFullYear()).slice(-2);
    switch (period) {
      case 'week': return start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      case 'quarter': return 'Q' + (Math.floor(start.getMonth() / 3) + 1) + " '" + yy;
      case 'half-year': return (start.getMonth() < 6 ? 'H1' : 'H2') + " '" + yy;
      case 'year': return String(start.getFullYear());
      default: return start.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
    }
  }

  function buildBuckets(period) {
    var count = PERIOD_COUNT[period] || 12;
    var current = bucketStart(new Date(), period);
    var buckets = [];
    for (var i = count - 1; i >= 0; i -= 1) {
      var start = addPeriod(current, period, -i);
      buckets.push({ start: start, end: addPeriod(start, period, 1), label: bucketLabel(start, period) });
    }
    return buckets;
  }

  // "2026-09-05" as local midnight (not UTC).
  function parseDate(value) {
    if (!value) return new Date(NaN);
    return value.length === 10 ? new Date(value + 'T00:00:00') : new Date(value);
  }

  function tooltip(color, format) {
    return {
      enabled: false,
      external: function (ctx) {
        var chart = ctx.chart;
        var tip = ctx.tooltip;
        var el = chart.canvas.parentNode.querySelector('.chart-tooltip');
        if (!el) {
          el = document.createElement('div');
          el.className = 'chart-tooltip';
          el.style.position = 'absolute';
          el.style.pointerEvents = 'none';
          el.style.whiteSpace = 'nowrap';
          el.style.zIndex = 5;
          chart.canvas.parentNode.appendChild(el);
        }
        if (tip.opacity === 0 || !tip.dataPoints || !tip.dataPoints.length || tip.dataPoints[0].raw === null) {
          el.style.opacity = 0;
          return;
        }
        var point = tip.dataPoints[0];
        el.innerHTML = '';
        var value = document.createElement('div');
        value.className = 'chart-tooltip-value';
        var swatch = document.createElement('span');
        swatch.className = 'chart-tooltip-swatch';
        swatch.style.backgroundColor = color;
        value.appendChild(swatch);
        value.appendChild(document.createTextNode(format(point.raw)));
        var label = document.createElement('div');
        label.className = 'chart-tooltip-label';
        label.textContent = point.label;
        el.appendChild(value);
        el.appendChild(label);
        el.style.opacity = 1;
        var left = tip.caretX + 12;
        if (left + el.offsetWidth > chart.width) left = tip.caretX - el.offsetWidth - 12;
        el.style.left = left + 'px';
        el.style.top = Math.max(0, tip.caretY - el.offsetHeight / 2) + 'px';
      }
    };
  }

  var GRID = '#EEF0F5';
  var AXIS = '#E2E6EF';
  var TICK = { color: '#94A3B8', font: { size: 12 } };

  function prepareBox(id) {
    var canvas = document.getElementById(id);
    if (!canvas || !window.Chart) return null;
    Chart.defaults.font.family = "'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif";
    canvas.parentNode.style.position = 'relative';
    return canvas;
  }

  // '#4F46E5' + 0.2 -> 'rgba(79, 70, 229, 0.2)'
  function withAlpha(hex, alpha) {
    var n = parseInt(hex.slice(1), 16);
    return 'rgba(' + (n >> 16) + ', ' + ((n >> 8) & 255) + ', ' + (n & 255) + ', ' + alpha + ')';
  }

  // Vertical fade under a line, sized to the plot area once it is known.
  function areaFill(color) {
    return function (ctx) {
      var area = ctx.chart.chartArea;
      if (!area) return withAlpha(color, 0.1);
      var g = ctx.chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
      g.addColorStop(0, withAlpha(color, 0.22));
      g.addColorStop(1, withAlpha(color, 0));
      return g;
    };
  }

  function lineChart(id, color, format, tickFormat, integer) {
    var canvas = prepareBox(id);
    if (!canvas) return null;
    return new Chart(canvas, {
      type: 'line',
      data: { labels: [], datasets: [{
        data: [],
        borderColor: color,
        borderWidth: 2.5,
        tension: 0.35,
        cubicInterpolationMode: 'monotone',
        fill: 'origin',
        backgroundColor: areaFill(color),
        spanGaps: false,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointBackgroundColor: color,
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 300 },
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false }, tooltip: tooltip(color, format) },
        scales: {
          x: { grid: { display: false }, border: { color: AXIS }, ticks: Object.assign({}, TICK, { maxRotation: 0, autoSkip: true, autoSkipPadding: 12 }) },
          y: {
            beginAtZero: true,
            grid: { color: GRID },
            border: { display: false },
            ticks: Object.assign({}, TICK, {
              precision: integer ? 0 : undefined,
              maxTicksLimit: 5,
              callback: tickFormat ? function (v) { return tickFormat(v); } : undefined
            })
          }
        }
      }
    });
  }

  function barChart(id, color, labels, values, format, tickFormat, labelWidth, integer) {
    var canvas = prepareBox(id);
    if (!canvas) return null;
    return new Chart(canvas, {
      type: 'bar',
      data: { labels: labels, datasets: [{
        data: values,
        backgroundColor: color,
        hoverBackgroundColor: color,
        borderRadius: { topRight: 6, bottomRight: 6 },
        borderSkipped: 'left',
        maxBarThickness: 22
      }] },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 300 },
        plugins: { legend: { display: false }, tooltip: tooltip(color, format) },
        scales: {
          x: {
            beginAtZero: true,
            grid: { color: GRID },
            border: { display: false },
            ticks: Object.assign({}, TICK, {
              precision: integer ? 0 : undefined,
              maxTicksLimit: 5,
              callback: tickFormat ? function (v) { return tickFormat(v); } : undefined
            })
          },
          y: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: '#475569', font: { size: 12 } }
          }
        }
      }
    });
  }

  function setData(chart, labels, values) {
    if (!chart) return;
    chart.data.labels = labels;
    chart.data.datasets[0].data = values;
    chart.update();
  }

  // Period tabs: calls fn(period) now and whenever a tab is clicked.
  function onPeriod(fn) {
    var toggle = $('[data-period-toggle]');
    var current = 'month';
    if (toggle) {
      $$('[data-period]', toggle).forEach(function (btn) {
        btn.addEventListener('click', function () {
          current = btn.getAttribute('data-period');
          $$('[data-period]', toggle).forEach(function (b) {
            var on = b === btn;
            b.classList.toggle('active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
          });
          fn(current);
        });
      });
    }
    fn(current);
  }

  window.AdminCharts = {
    buildBuckets: buildBuckets,
    parseDate: parseDate,
    formatMoney: formatMoney,
    lineChart: lineChart,
    barChart: barChart,
    setData: setData,
    onPeriod: onPeriod
  };
})();
