(function () {
  'use strict';
  var rp = function (n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); };

  // Scroll reveal
  var els = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    els.forEach(function (el, i) { el.style.transitionDelay = (i % 6) * 60 + 'ms'; io.observe(el); });
  } else {
    els.forEach(function (el) { el.classList.add('is-in'); });
  }

  // Counter angka
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = +el.dataset.count, start = null, dur = 1200;
    function step(t) {
      if (!start) start = t;
      var p = Math.min((t - start) / dur, 1);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  });

  // Hitung mundur flash sale
  document.querySelectorAll('[data-countdown]').forEach(function (box) {
    var left = +box.dataset.countdown;
    var h = box.querySelector('[data-h]'), m = box.querySelector('[data-m]'), s = box.querySelector('[data-s]');
    var pad = function (n) { return String(n).padStart(2, '0'); };
    function tick() {
      h.textContent = pad(Math.floor(left / 3600));
      m.textContent = pad(Math.floor(left % 3600 / 60));
      s.textContent = pad(left % 60);
      if (left-- > 0) setTimeout(tick, 1000);
    }
    tick();
  });

  // Checkout: pilih nominal & metode bayar -> field tersembunyi (harga final selalu dihitung ulang di server)
  var co = document.querySelector('[data-checkout]');
  if (co) {
    var packs = co.querySelectorAll('[data-pack]'), pays = co.querySelectorAll('[data-pay]');
    var total = co.querySelector('[data-total]'), summary = co.querySelector('[data-summary]');
    var buy = co.querySelector('[data-buy]'), uid = co.querySelector('[data-userid]'), mail = co.querySelector('[data-email]');
    var packIn = co.querySelector('[name="pack_id"]'), payIn = co.querySelector('[name="payment_method"]');

    function pick(list, btn) { list.forEach(function (b) { b.classList.toggle('is-active', b === btn); }); }
    function refresh() {
      var ok = packIn.value && payIn.value && uid.value.trim().length >= 3 && /.+@.+\..+/.test(mail.value.trim());
      buy.disabled = !ok;
      buy.classList.toggle('opacity-50', !ok);
    }
    packs.forEach(function (b) {
      b.addEventListener('click', function () {
        packIn.value = b.dataset.id; pick(packs, b);
        total.textContent = rp(+b.dataset.price);
        summary.textContent = b.dataset.label;
        refresh();
      });
      if (packIn.value && packIn.value === b.dataset.id) b.click();
    });
    pays.forEach(function (b) {
      b.addEventListener('click', function () { payIn.value = b.dataset.code; pick(pays, b); refresh(); });
      if (payIn.value && payIn.value === b.dataset.code) b.click();
    });
    uid.addEventListener('input', refresh);
    mail.addEventListener('input', refresh);
    co.addEventListener('submit', function () { buy.disabled = true; buy.textContent = 'Memproses…'; });
    refresh();
  }

  // Halaman pesanan: timer kedaluwarsa, polling status, salin nomor VA
  var od = document.querySelector('[data-order]');
  if (od) {
    var left = +od.dataset.expires, timer = od.querySelector('[data-timer]');
    if (timer) {
      (function tick() {
        var m = Math.floor(left / 60), s = left % 60;
        timer.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        if (left-- > 0) setTimeout(tick, 1000);
      })();
    }
    var cur = od.dataset.status;
    if (cur === 'pending' || cur === 'paid' || cur === 'processing') {
      var poll = setInterval(function () {
        fetch(od.dataset.statusUrl, { headers: { Accept: 'application/json' } })
          .then(function (r) { return r.json(); })
          .then(function (d) { if (d.status && d.status !== cur) { clearInterval(poll); location.reload(); } })
          .catch(function () {});
      }, 5000);
    }
    var cp = od.querySelector('[data-copy]');
    if (cp) cp.addEventListener('click', function () {
      var t = od.querySelector('[data-copy-text]').textContent.trim();
      (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).then(function () { cp.textContent = 'Tersalin ✓'; }).catch(function () { cp.textContent = 'Salin manual'; });
    });
  }
})();

// Pencarian: dropdown saran (debounce) – dibangun dengan DOM agar aman dari XSS
(function () {
  'use strict';
  document.querySelectorAll('[data-search]').forEach(function (form) {
    var input = form.querySelector('input[name="q"]');
    var box = form.querySelector('[data-suggest-box]');
    var url = form.dataset.suggest, timer = null, seq = 0;

    function hide() { box.classList.add('hidden'); box.textContent = ''; }
    function group(title, items) {
      if (!items.length) return;
      var h = document.createElement('div');
      h.className = 'px-3 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wide text-slate-400';
      h.textContent = title; box.appendChild(h);
      items.forEach(function (it) {
        var a = document.createElement('a');
        a.href = it.url; a.className = 'flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-indigo-50';
        var sw = document.createElement('span'); sw.className = 'h-8 w-8 shrink-0 rounded-lg'; sw.style.background = /^#[0-9a-f]{3,8}$/i.test(it.color) ? it.color : '#E2E8F0';
        var t = document.createElement('span'); t.className = 'flex-1 truncate text-sm font-semibold'; t.textContent = it.name;
        var m = document.createElement('span'); m.className = 'text-xs text-slate-500'; m.textContent = it.meta;
        a.appendChild(sw); a.appendChild(t); a.appendChild(m); box.appendChild(a);
      });
    }
    function render(d) {
      box.textContent = '';
      if (!d.games.length && !d.products.length) {
        var e = document.createElement('div'); e.className = 'px-3 py-4 text-sm text-slate-500'; e.textContent = 'Tidak ada hasil.'; box.appendChild(e);
      } else { group('Game', d.games); group('Produk', d.products); }
      var all = document.createElement('button'); all.type = 'submit';
      all.className = 'mt-1 w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-brand hover:bg-indigo-50';
      all.textContent = 'Lihat semua hasil →'; box.appendChild(all);
      box.classList.remove('hidden');
    }
    input.addEventListener('input', function () {
      clearTimeout(timer);
      var q = input.value.trim();
      if (q.length < 2) { hide(); return; }
      timer = setTimeout(function () {
        var my = ++seq;
        fetch(url + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } })
          .then(function (r) { return r.json(); })
          .then(function (d) { if (my === seq) render(d); })
          .catch(hide);
      }, 200);
    });
    document.addEventListener('click', function (e) { if (!form.contains(e.target)) hide(); });
    input.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); });
  });
})();
