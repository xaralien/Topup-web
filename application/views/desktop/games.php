<main class="mx-auto max-w-6xl px-6 pt-10">
  <h1 class="font-display text-3xl font-extrabold"><?= $q !== '' ? 'Hasil untuk “' . html_escape($q) . '”' : 'Semua Game &amp; Voucher' ?></h1>

  <div class="mt-6 flex items-center justify-between gap-6">
    <div class="flex flex-wrap gap-2 text-sm font-semibold">
      <?php $qs = $q !== '' ? 'q=' . urlencode($q) . '&' : ''; ?>
      <a href="<?= site_url('games') . ($q !== '' ? '?q=' . urlencode($q) : '') ?>" class="chip <?= $cat === '' ? 'is-active' : '' ?>">Semua</a>
      <?php foreach ($categories as $c): ?>
        <a href="<?= site_url('games') . '?' . $qs . 'kategori=' . urlencode($c) ?>" class="chip <?= $cat === $c ? 'is-active' : '' ?>"><?= html_escape($c) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <h2 class="mt-8 font-display text-lg font-extrabold">Game &amp; Voucher <span class="font-sans text-sm font-normal text-slate-500">(<?= count($games) ?>)</span></h2>
  <?php if ($games): ?>
    <div class="mt-4 grid grid-cols-6 gap-4">
      <?php foreach ($games as $g): ?>
        <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card">
          <?= game_thumb($g, 'h-24 rounded-xl mb-3') ?>
          <div class="text-sm font-bold"><?= html_escape($g['name']) ?></div>
          <div class="text-xs text-slate-500">mulai <?= rupiah($g['packs'][0][1]) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="mt-4 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">Tidak ada game yang cocok. Coba kata kunci lain, atau <a href="<?= site_url('games') ?>" class="font-semibold text-brand underline">lihat semua game</a>.</p>
  <?php endif; ?>

  <?php if ($q !== ''): ?>
    <h2 class="mt-10 font-display text-lg font-extrabold">Produk <span class="font-sans text-sm font-normal text-slate-500">(<?= count($products) ?>)</span></h2>
    <div class="mt-4 max-w-3xl">
      <?php if ($products): $this->load->view('partials/product_list', ['products' => $products]); else: ?>
        <p class="text-slate-500">Tidak ada produk yang cocok.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>
