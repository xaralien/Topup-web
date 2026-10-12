<main>
<!-- HERO -->
<section class="px-4 pt-6" data-reveal>
  <span class="inline-flex rounded-full bg-brand-soft px-3 py-1 text-[11px] font-bold text-brand">⚡ Proses otomatis 24 jam</span>
  <h1 class="mt-3 font-display text-3xl font-extrabold leading-tight tracking-tight">Top up game favoritmu, <span class="text-brand">masuk dalam 10 detik.</span></h1>
  <p class="mt-2 text-sm text-slate-600">Harga jujur, bayar pakai QRIS atau e-wallet.</p>
  <a href="#game" class="btn-primary mt-5 w-full text-center">Top Up Sekarang</a>
  <div class="mt-5 grid grid-cols-3 text-center text-xs text-slate-500">
    <div><div class="font-display text-lg font-extrabold text-ink"><span data-count="250000">0</span>+</div>Transaksi</div>
    <div><div class="font-display text-lg font-extrabold text-ink"><span data-count="10">0</span> dtk</div>Proses</div>
    <div><div class="font-display text-lg font-extrabold text-ink">4.9 ★</div>Rating</div>
  </div>
</section>

<!-- POPULER (scroll horizontal) -->
<section class="mt-8">
  <h2 class="px-4 font-display text-lg font-extrabold">🔥 Populer</h2>
  <div class="mt-3 flex gap-3 overflow-x-auto px-4 pb-2 snap-x scroll-px-4 no-scrollbar">
    <?php foreach ($popular as $g): ?>
      <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card w-32 shrink-0 snap-start">
        <?= game_thumb($g, 'h-20 rounded-xl mb-2') ?>
        <div class="text-xs font-bold"><?= html_escape($g['name']) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- PROMO -->
<section id="promo" class="mx-4 mt-8 rounded-2xl bg-brand p-5 text-white" data-reveal>
  <div class="text-[11px] font-bold text-indigo-200">FLASH SALE</div>
  <div class="font-display text-lg font-extrabold">Diskon s/d 15% Diamond</div>
  <div class="mt-2 flex items-center gap-1.5 font-display font-extrabold" data-countdown="3600">
    <span class="rounded-md bg-white/15 px-2 py-1" data-h>00</span>:<span class="rounded-md bg-white/15 px-2 py-1" data-m>00</span>:<span class="rounded-md bg-white/15 px-2 py-1" data-s>00</span>
  </div>
</section>

<!-- SEMUA GAME -->
<section id="game" class="mt-8 px-4">
  <div class="flex items-center justify-between"><h2 class="font-display text-lg font-extrabold">Semua game</h2>
    <a href="<?= site_url('games') ?>" class="text-xs font-bold text-brand">Lihat semua →</a></div>
  <div class="mt-3 grid grid-cols-3 gap-3">
    <?php foreach ($games as $g): ?>
      <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card p-2" data-reveal>
        <?= game_thumb($g, 'h-14 rounded-lg mb-2') ?>
        <div class="text-[11px] font-bold leading-tight"><?= html_escape($g['name']) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- CARA ORDER -->
<section id="cara" class="mt-10 px-4">
  <h2 class="font-display text-lg font-extrabold">Order dalam 3 langkah</h2>
  <ol class="mt-3 space-y-3">
    <?php foreach ([['Pilih game','Cari game dan nominal.'],['Isi ID & bayar','QRIS / e-wallet.'],['Terima otomatis','Masuk dalam detik.']] as $i => $s): ?>
      <li class="flex items-center gap-3 rounded-xl border border-slate-200 p-3" data-reveal>
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-soft font-display font-extrabold text-brand"><?= $i + 1 ?></span>
        <div><div class="text-sm font-bold"><?= $s[0] ?></div><div class="text-xs text-slate-500"><?= $s[1] ?></div></div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>

<!-- FAQ -->
<section id="faq" class="mt-10 px-4">
  <h2 class="font-display text-lg font-extrabold">FAQ</h2>
  <div class="mt-3 divide-y divide-slate-200 rounded-xl border border-slate-200">
    <?php foreach ([['Berapa lama prosesnya?','Biasanya kurang dari 1 menit.'],['Apakah aman?','Pembayaran via gateway resmi.'],['Salah ID?','Hubungi CS segera.']] as $f): ?>
      <details class="p-4"><summary class="cursor-pointer text-sm font-semibold"><?= $f[0] ?></summary><p class="mt-2 text-xs text-slate-600"><?= $f[1] ?></p></details>
    <?php endforeach; ?>
  </div>
</section>
</main>
