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

  // Checkout: pilih nominal & metode bayar
  var co = document.querySelector('[data-checkout]');
  if (co) {
    var packs = co.querySelectorAll('[data-pack]'), pays = co.querySelectorAll('[data-pay]');
    var total = co.querySelector('[data-total]'), summary = co.querySelector('[data-summary]');
    var buy = co.querySelector('[data-buy]'), uid = co.querySelector('[data-userid]');
    var pack = null, pay = null;

    function pick(list, btn) { list.forEach(function (b) { b.classList.toggle('is-active', b === btn); }); }
    function refresh() {
      var ok = pack && pay && uid.value.trim().length >= 4;
      buy.disabled = !ok;
      buy.classList.toggle('opacity-50', !ok);
    }
    packs.forEach(function (b) {
      b.addEventListener('click', function () {
        pack = b; pick(packs, b);
        total.textContent = rp(+b.dataset.price);
        summary.textContent = b.dataset.label;
        refresh();
      });
    });
    pays.forEach(function (b) { b.addEventListener('click', function () { pay = b; pick(pays, b); refresh(); }); });
    uid.addEventListener('input', refresh);
    buy.addEventListener('click', function () {
      // TODO: POST ke controller order -> payment gateway
      alert('Prototipe: pesanan ' + pack.dataset.label + ' via ' + pay.textContent.trim() + ' (' + total.textContent + ')');
    });
  }
})();
