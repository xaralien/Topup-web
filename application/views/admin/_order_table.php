<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
  <table class="w-full text-left text-sm">
    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3">No. Pesanan</th><th class="p-3">Produk</th><th class="p-3">ID Akun</th><th class="p-3 text-right">Total</th><th class="p-3">Status</th><th class="p-3">Waktu</th></tr></thead>
    <tbody class="divide-y divide-slate-100">
    <?php foreach ($rows as $o): $i = order_status_info($o['status']); ?>
      <tr class="hover:bg-slate-50">
        <td class="p-3 font-semibold"><a class="text-brand hover:underline" href="<?= site_url('admin/orders/view/' . $o['order_no']) ?>"><?= html_escape($o['order_no']) ?></a></td>
        <td class="p-3"><?= html_escape($o['game_name'] . ' · ' . $o['item_label']) ?></td>
        <td class="p-3"><?= html_escape($o['target_user_id'] . ($o['target_zone_id'] ? ' (' . $o['target_zone_id'] . ')' : '')) ?></td>
        <td class="p-3 text-right font-semibold"><?= rupiah($o['amount']) ?></td>
        <td class="p-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $i[1] ?>"><?= $i[0] ?></span></td>
        <td class="p-3 whitespace-nowrap text-slate-500"><?= html_escape($o['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada pesanan.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
