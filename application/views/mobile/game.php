<main data-checkout class="pb-24">
  <div class="flex items-center gap-3 px-4 pt-4">
    <a href="<?= site_url() ?>" class="text-slate-500">←</a>
    <div class="h-12 w-12 rounded-xl" style="background:<?= $game['color'] ?>"></div>
    <div><h1 class="font-display text-lg font-extrabold leading-tight"><?= html_escape($game['name']) ?></h1><p class="text-xs text-slate-500">Proses otomatis 24 jam</p></div>
  </div>

  <section class="mx-4 mt-5 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>1</span> ID akun</h2>
    <input type="text" inputmode="numeric" placeholder="User ID" class="field mt-3" data-userid>
  </section>

  <section class="mx-4 mt-4 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>2</span> Nominal</h2>
    <div class="mt-3 grid grid-cols-2 gap-2">
      <?php foreach ($game['packs'] as $p): ?>
        <button type="button" class="pack" data-pack data-label="<?= $p[0] ?> <?= html_escape($game['currency']) ?>" data-price="<?= $p[1] ?>">
          <span class="font-display font-extrabold"><?= $p[0] ?> <small class="font-sans text-xs font-normal text-slate-500"><?= html_escape($game['currency']) ?></small></span>
          <span class="text-sm font-bold text-brand"><?= rupiah($p[1]) ?></span>
        </button>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="mx-4 mt-4 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>3</span> Pembayaran</h2>
    <div class="mt-3 grid grid-cols-4 gap-2 text-xs">
      <?php foreach (['QRIS','DANA','OVO','GoPay'] as $m): ?>
        <button type="button" class="pack py-3" data-pay><?= $m ?></button>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- sticky checkout bar di atas bottom-nav -->
  <div class="fixed inset-x-0 bottom-14 z-30 flex items-center justify-between bg-ink px-4 py-3 text-white">
    <div><div class="text-[11px] text-slate-400" data-summary>Pilih nominal</div><div class="font-display font-extrabold" data-total>Rp0</div></div>
    <button type="button" class="btn-primary !py-2 opacity-50" data-buy disabled>Beli</button>
  </div>
</main>
