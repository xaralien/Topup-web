<header class="flex flex-wrap items-end justify-between gap-3">
  <div><h1 class="a-title">Game &amp; Produk</h1><p class="a-sub mt-1"><?= count($games) ?> game · kelola gambar, nominal, dan harga</p></div>
  <a href="<?= site_url('admin/games/create') ?>" class="a-btn"><?= icon('plus', 'h-4 w-4') ?>Tambah game</a>
</header>
<div class="a-card mt-6 overflow-x-auto">
  <table class="a-table">
    <thead><tr><th>Game</th><th>Kategori</th><th class="text-right">Nominal</th><th class="text-right">Pesanan</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($games as $g): ?>
      <tr>
        <td><div class="flex items-center gap-3"><?= game_thumb($g, 'h-11 w-11 shrink-0 rounded-xl') ?><div><div class="font-semibold"><?= html_escape($g['name']) ?></div><div class="text-xs text-slate-400">/game/<?= html_escape($g['slug']) ?></div></div></div></td>
        <td class="text-slate-600"><?= html_escape($g['category']) ?></td>
        <td class="text-right"><?= (int) $g['pack_count'] ?></td>
        <td class="text-right"><?= (int) $g['order_count'] ?></td>
        <td><?= $g['is_active'] ? '<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Aktif</span>' : '<span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600">Nonaktif</span>' ?><?= $g['is_hot'] ? ' <span title="Populer">🔥</span>' : '' ?></td>
        <td class="text-right"><a class="a-btn-soft" href="<?= site_url('admin/games/edit/' . $g['id']) ?>">Edit</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
