<main>
<!-- HERO -->
<section class="mx-auto max-w-6xl px-6 pt-16 pb-12 grid grid-cols-2 gap-12 items-center">
  <div data-reveal>
    <span class="inline-flex items-center gap-2 rounded-full bg-brand-soft px-3 py-1 text-xs font-bold text-brand">⚡ Proses otomatis 24 jam</span>
    <h1 class="mt-4 font-display text-5xl font-extrabold leading-[1.1] tracking-tight">Top up game favoritmu, <span class="text-brand">masuk dalam 10 detik.</span></h1>
    <p class="mt-4 text-lg text-slate-600 max-w-md">Diamond, UC, voucher, dan akun game. Harga jujur, bayar pakai QRIS atau e-wallet.</p>
    <div class="mt-8 flex items-center gap-3">
      <a href="#game" class="btn-primary">Top Up Sekarang</a>
      <a href="#promo" class="rounded-xl border border-slate-300 px-6 py-3 font-bold hover:border-brand hover:text-brand transition">Lihat Promo</a>
    </div>
    <dl class="mt-10 flex gap-10">
      <div><dt class="text-xs text-slate-500">Transaksi</dt><dd class="font-display text-2xl font-extrabold"><span data-count="250000">0</span>+</dd></div>
      <div><dt class="text-xs text-slate-500">Rata-rata proses</dt><dd class="font-display text-2xl font-extrabold"><span data-count="10">0</span> detik</dd></div>
      <div><dt class="text-xs text-slate-500">Rating</dt><dd class="font-display text-2xl font-extrabold">4.9 ★</dd></div>
    </dl>
  </div>
  <div class="grid grid-cols-3 gap-3" data-reveal>
    <?php foreach (array_slice($games, 0, 6) as $g): ?>
      <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card">
        <div class="h-20 rounded-xl mb-3" style="background:<?= $g['color'] ?>"></div>
        <div class="text-sm font-bold"><?= html_escape($g['name']) ?></div>
        <div class="text-xs text-slate-500"><?= html_escape($g['currency']) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- TRUST BAR -->
<section class="border-y border-slate-200 bg-paper">
  <div class="mx-auto max-w-6xl px-6 py-5 flex items-center justify-between text-sm font-semibold text-slate-600">
    <span>🔒 Pembayaran aman</span><span>⚡ Proses instan</span><span>💬 CS 24 jam</span><span>🏷️ Harga jujur</span>
    <span class="text-slate-400">QRIS · DANA · OVO · GoPay · Bank</span>
  </div>
</section>

<!-- GAME GRID -->
<section id="game" class="mx-auto max-w-6xl px-6 pt-20">
  <div class="flex items-end justify-between">
    <div><h2 class="font-display text-3xl font-extrabold">Pilih game kamu</h2>
    <p class="mt-1 text-slate-600">Klik game, isi ID, pilih nominal, bayar.</p></div>
    <a href="<?= site_url('games') ?>" class="text-sm font-bold text-brand hover:underline">Lihat semua game →</a>
  </div>
  <div class="mt-8 grid grid-cols-6 gap-4">
    <?php foreach ($games as $g): ?>
      <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card" data-reveal>
        <div class="h-24 rounded-xl mb-3" style="background:<?= $g['color'] ?>"></div>
        <div class="text-sm font-bold"><?= html_escape($g['name']) ?></div>
        <div class="text-xs text-slate-500">mulai <?= rupiah($g['packs'][0][1]) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- PROMO -->
<section id="promo" class="mx-auto max-w-6xl px-6 pt-20">
  <div class="rounded-3xl bg-brand text-white p-10 flex items-center justify-between" data-reveal>
    <div>
      <div class="text-sm font-bold text-indigo-200">FLASH SALE</div>
      <h2 class="font-display text-3xl font-extrabold mt-1">Diskon hingga 15% semua Diamond</h2>
      <p class="text-indigo-100 mt-2">Berakhir dalam</p>
      <div class="mt-2 flex gap-2 font-display text-2xl font-extrabold" data-countdown="3600">
        <span class="rounded-lg bg-white/15 px-3 py-2" data-h>00</span><span>:</span>
        <span class="rounded-lg bg-white/15 px-3 py-2" data-m>00</span><span>:</span>
        <span class="rounded-lg bg-white/15 px-3 py-2" data-s>00</span>
      </div>
    </div>
    <a href="#game" class="rounded-xl bg-white px-6 py-3 font-bold text-brand hover:scale-105 transition">Ambil Promo</a>
  </div>
</section>

<!-- CARA ORDER -->
<section id="cara" class="mx-auto max-w-6xl px-6 pt-20">
  <h2 class="font-display text-3xl font-extrabold text-center">Order dalam 3 langkah</h2>
  <div class="mt-10 grid grid-cols-3 gap-6">
    <?php foreach ([['1','Pilih game','Cari game dan pilih nominal.'],['2','Isi ID & bayar','Masukkan ID akun, bayar via QRIS/e-wallet.'],['3','Terima otomatis','Item masuk ke akun dalam hitungan detik.']] as $s): ?>
      <div class="rounded-2xl border border-slate-200 p-6" data-reveal>
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-brand-soft font-display font-extrabold text-brand"><?= $s[0] ?></div>
        <div class="mt-4 font-display font-bold text-lg"><?= $s[1] ?></div>
        <p class="mt-1 text-sm text-slate-600"><?= $s[2] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- FAQ -->
<section id="faq" class="mx-auto max-w-3xl px-6 pt-20">
  <h2 class="font-display text-3xl font-extrabold text-center">Pertanyaan umum</h2>
  <div class="mt-8 divide-y divide-slate-200 rounded-2xl border border-slate-200">
    <?php foreach ([['Berapa lama prosesnya?','Umumnya kurang dari 1 menit setelah pembayaran terkonfirmasi.'],['Apakah aman?','Pembayaran lewat gateway resmi. Kami tidak pernah meminta password akun.'],['Salah ID, bagaimana?','Segera hubungi CS. Pesanan yang sudah masuk ke ID tidak dapat dibatalkan.']] as $f): ?>
      <details class="group p-5"><summary class="flex cursor-pointer items-center justify-between font-semibold"><?= $f[0] ?><span class="text-brand group-open:rotate-45 transition">＋</span></summary><p class="mt-2 text-sm text-slate-600"><?= $f[1] ?></p></details>
    <?php endforeach; ?>
  </div>
</section>
</main>
