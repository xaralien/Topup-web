<div class="flex items-center justify-between"><h1 class="font-display text-2xl font-extrabold">Game &amp; Produk</h1>
  <a href="<?= site_url('admin/games/create') ?>" class="btn-primary !py-2">+ Tambah game</a></div>
<div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
  <table class="w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3">Game</th><th class="p-3">Kategori</th><th class="p-3">Nominal</th><th class="p-3">Pesanan</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
    <tbody class="divide-y divide-slate-100">
    <?php foreach ($games as $g): ?>
      <tr class="hover:bg-slate-50">
        <td class="p-3"><div class="flex items-center gap-3"><?= game_thumb($g, 'h-10 w-10 shrink-0 rounded-lg') ?><div><div class="font-semibold"><?= html_escape($g['name']) ?></div><div class="text-xs text-slate-500">/game/<?= html_escape($g['slug']) ?></div></div></div></td>
        <td class="p-3"><?= html_escape($g['category']) ?></td>
        <td class="p-3"><?= (int) $g['pack_count'] ?></td>
        <td class="p-3"><?= (int) $g['order_count'] ?></td>
        <td class="p-3"><?= $g['is_active'] ? '<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Aktif</span>' : '<span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600">Nonaktif</span>' ?><?= $g['is_hot'] ? ' <span title="Populer">🔥</span>' : '' ?></td>
        <td class="p-3 text-right"><a class="font-semibold text-brand hover:underline" href="<?= site_url('admin/games/edit/' . $g['id']) ?>">Edit</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
