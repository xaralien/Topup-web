<?php /* $products: hasil Game_model::products() */ ?>
<ul class="divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
  <?php foreach ($products as $p): ?>
    <li>
      <a href="<?= site_url('game/' . $p['game']['slug']) ?>" class="flex items-center gap-3 p-3 hover:bg-brand-soft transition">
        <span class="h-10 w-10 shrink-0 rounded-lg" style="background:<?= $p['game']['color'] ?>"></span>
        <span class="flex-1 min-w-0">
          <span class="block truncate text-sm font-bold"><?= html_escape($p['game']['name']) ?> · <?= html_escape($p['qty']) ?> <?= html_escape($p['game']['currency']) ?></span>
          <span class="block text-xs text-slate-500"><?= html_escape($p['game']['category']) ?></span>
        </span>
        <span class="text-sm font-bold text-brand"><?= rupiah($p['price']) ?></span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
