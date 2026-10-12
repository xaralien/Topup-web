<?php /* Form pencarian + dropdown saran. $class = kelas wrapper, $q = nilai awal */ ?>
<form action="<?= site_url('games') ?>" method="get" role="search" class="relative <?= $class ?>" data-search data-suggest="<?= site_url('search/suggest') ?>">
  <input type="search" name="q" value="<?= html_escape(isset($q) ? $q : '') ?>" placeholder="Cari game atau produk…" autocomplete="off" aria-label="Cari game atau produk"
         class="field !rounded-full !py-2 !pl-10 text-sm bg-paper">
  <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
  <div data-suggest-box class="hidden absolute left-0 right-0 top-full mt-2 z-50 max-h-96 overflow-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"></div>
</form>
