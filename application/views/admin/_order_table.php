<div class="a-card overflow-x-auto">
  <table class="a-table">
    <thead><tr><th>No. Pesanan</th><th>Produk</th><th>ID Akun</th><th class="text-right">Total</th><th>Status</th><th>Waktu</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $o): $i = order_status_info($o['status']); ?>
      <tr>
        <td class="font-semibold"><a class="text-brand hover:underline" href="<?= site_url('admin/orders/view/' . $o['order_no']) ?>"><?= html_escape($o['order_no']) ?></a></td>
        <td><?= html_escape($o['game_name']) ?> <span class="text-slate-400">·</span> <?= html_escape($o['item_label']) ?></td>
        <td class="text-slate-600"><?= html_escape($o['target_user_id'] . ($o['target_zone_id'] ? ' (' . $o['target_zone_id'] . ')' : '')) ?></td>
        <td class="text-right font-bold"><?= rupiah($o['amount']) ?></td>
        <td><span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $i[1] ?>"><?= $i[0] ?></span></td>
        <td class="whitespace-nowrap text-slate-500"><?= html_escape(substr($o['created_at'], 0, 16)) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" class="!py-10 text-center text-slate-400">Belum ada pesanan.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
