<main class="px-4 pt-4">
  <h1 class="font-display text-xl font-extrabold"><?= $q !== '' ? 'Hasil “' . html_escape($q) . '”' : 'Semua Game' ?></h1>

  <?php $qs = $q !== '' ? 'q=' . urlencode($q) . '&' : ''; ?>
  <div class="mt-3 -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 text-xs font-semibold no-scrollbar">
    <a href="<?= site_url('games') . ($q !== '' ? '?q=' . urlencode($q) : '') ?>" class="chip shrink-0 <?= $cat === '' ? 'is-active' : '' ?>">Semua</a>
    <?php foreach ($categories as $c): ?>
      <a href="<?= site_url('games') . '?' . $qs . 'kategori=' . urlencode($c) ?>" class="chip shrink-0 <?= $cat === $c ? 'is-active' : '' ?>"><?= html_escape($c) ?></a>
    <?php endforeach; ?>
  </div>

  <h2 class="mt-5 text-sm font-bold">Game &amp; Voucher <span class="font-normal text-slate-500">(<?= count($games) ?>)</span></h2>
  <?php if ($games): ?>
    <div class="mt-3 grid grid-cols-3 gap-3">
      <?php foreach ($games as $g): ?>
        <a href="<?= site_url('game/' . $g['slug']) ?>" class="game-card p-2">
          <?= game_thumb($g, 'h-14 rounded-lg mb-2') ?>
          <div class="text-[11px] font-bold leading-tight"><?= html_escape($g['name']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="mt-3 rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Tidak ada game yang cocok.</p>
  <?php endif; ?>

  <?php if ($q !== ''): ?>
    <h2 class="mt-6 text-sm font-bold">Produk <span class="font-normal text-slate-500">(<?= count($products) ?>)</span></h2>
    <div class="mt-3">
      <?php if ($products): $this->load->view('partials/product_list', ['products' => $products]); else: ?>
        <p class="text-sm text-slate-500">Tidak ada produk yang cocok.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>
