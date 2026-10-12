<main class="mx-auto max-w-6xl px-6 pt-10" data-checkout>
  <a href="<?= site_url() ?>" class="text-sm text-slate-500 hover:text-brand">← Kembali</a>
  <div class="mt-4 grid grid-cols-3 gap-8">
    <aside class="col-span-1">
      <div class="sticky top-24 rounded-2xl border border-slate-200 p-6">
        <div class="h-40 rounded-xl" style="background:<?= $game['color'] ?>"></div>
        <h1 class="mt-4 font-display text-2xl font-extrabold"><?= html_escape($game['name']) ?></h1>
        <p class="text-sm text-slate-600">Top up <?= html_escape($game['currency']) ?> instan, proses otomatis 24 jam.</p>
      </div>
    </aside>

    <div class="col-span-2 space-y-6">
      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>1</span> Masukkan ID akun</h2>
        <input type="text" inputmode="numeric" placeholder="User ID" class="field mt-4" data-userid>
      </section>

      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>2</span> Pilih nominal</h2>
        <div class="mt-4 grid grid-cols-3 gap-3">
          <?php foreach ($game['packs'] as $p): ?>
            <button type="button" class="pack" data-pack data-label="<?= $p[0] ?> <?= html_escape($game['currency']) ?>" data-price="<?= $p[1] ?>">
              <span class="font-display font-extrabold"><?= $p[0] ?></span>
              <span class="text-xs text-slate-500"><?= html_escape($game['currency']) ?></span>
              <span class="mt-2 text-sm font-bold text-brand"><?= rupiah($p[1]) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>3</span> Metode pembayaran</h2>
        <div class="mt-4 grid grid-cols-4 gap-3">
          <?php foreach (['QRIS','DANA','OVO','GoPay'] as $m): ?>
            <button type="button" class="pack py-4" data-pay><?= $m ?></button>
          <?php endforeach; ?>
        </div>
      </section>

      <div class="flex items-center justify-between rounded-2xl bg-ink p-6 text-white">
        <div><div class="text-xs text-slate-400" data-summary>Pilih nominal</div><div class="font-display text-2xl font-extrabold" data-total>Rp0</div></div>
        <button type="button" class="btn-primary opacity-50" data-buy disabled>Beli Sekarang</button>
      </div>
    </div>
  </div>
</main>
