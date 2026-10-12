<?php $i = order_status_info($order['status']); ?>
<a href="<?= site_url('admin/orders') ?>" class="text-sm text-slate-500 hover:text-brand">← Semua pesanan</a>
<div class="mt-2 flex flex-wrap items-center gap-3"><h1 class="a-title"><?= html_escape($order['order_no']) ?></h1>
  <span class="rounded-full px-3 py-1 text-xs font-bold <?= $i[1] ?>"><?= $i[0] ?></span></div>

<?php if (in_array($order['status'], ['paid', 'processing'], true)): ?>
<div class="mt-4 rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
  <p class="text-sm font-semibold text-indigo-900">Kirim <b><?= html_escape($order['item_label']) ?></b> ke ID <b><?= html_escape($order['target_user_id'] . ($order['target_zone_id'] ? ' (' . $order['target_zone_id'] . ')' : '')) ?></b>, lalu tandai selesai.</p>
  <div class="mt-3 flex gap-2">
    <?= form_open('admin/orders/set/' . $order['order_no'] . '/success', ['onsubmit' => "return confirm('Tandai BERHASIL (item sudah dikirim)?')"]) ?><button class="a-btn">✔ Tandai berhasil</button></form>
    <?= form_open('admin/orders/set/' . $order['order_no'] . '/failed', ['onsubmit' => "return confirm('Tandai GAGAL? Pastikan refund diurus manual.')"]) ?><button class="a-btn-danger">✖ Tandai gagal</button></form>
  </div>
</div>
<?php endif; ?>

<dl class="mt-5 grid max-w-2xl divide-y divide-slate-100 a-card text-sm">
  <?php foreach ([
    'Produk' => $order['game_name'] . ' · ' . $order['item_label'], 'SKU supplier' => $order['sku'] ?: '-',
    'ID akun' => $order['target_user_id'] . ($order['target_zone_id'] ? ' (' . $order['target_zone_id'] . ')' : ''),
    'Email' => $order['email'], 'WhatsApp' => $order['whatsapp'] ?: '-', 'Total' => rupiah($order['amount']),
    'Metode' => $order['payment_name'] . ' (' . $order['payment_method'] . ')', 'Ref. Duitku' => $order['duitku_ref'] ?: '-',
    'Dibuat' => $order['created_at'], 'Dibayar' => $order['paid_at'] ?: '-', 'Selesai' => $order['fulfilled_at'] ?: '-', 'Kedaluwarsa' => $order['expires_at'],
  ] as $k => $v): ?>
    <div class="flex justify-between gap-4 p-3"><dt class="text-slate-500"><?= $k ?></dt><dd class="text-right font-semibold break-all"><?= html_escape($v) ?></dd></div>
  <?php endforeach; ?>
</dl>

<h2 class="mt-8 font-display text-lg font-extrabold">Log pembayaran</h2>
<div class="mt-3 max-w-3xl space-y-2">
  <?php foreach ($logs as $l): ?>
    <details class="a-card !rounded-xl p-3 text-xs"><summary class="cursor-pointer font-semibold"><?= html_escape($l['type']) ?> · <span class="font-normal text-slate-500"><?= html_escape($l['created_at']) ?></span></summary>
      <pre class="mt-2 overflow-x-auto whitespace-pre-wrap text-slate-600"><?= html_escape($l['payload']) ?></pre></details>
  <?php endforeach; ?>
  <?php if (!$logs): ?><p class="text-sm text-slate-500">Belum ada log.</p><?php endif; ?>
</div>
