<?php list($label, $cls, $msg) = order_status_info($order['status']); $pending = $order['status'] === 'pending'; ?>
<div data-order data-status-url="<?= site_url('order/status/' . $order['order_no']) ?>" data-status="<?= html_escape($order['status']) ?>"
     data-expires="<?= max(0, strtotime($order['expires_at']) - time()) ?>">
  <div class="flex items-center justify-between gap-3">
    <h1 class="font-display text-2xl font-extrabold">Pesanan <span class="text-brand"><?= html_escape($order['order_no']) ?></span></h1>
    <span class="rounded-full px-3 py-1 text-xs font-bold <?= $cls ?>" data-badge><?= html_escape($label) ?></span>
  </div>
  <p class="mt-1 text-sm text-slate-600"><?= html_escape($msg) ?></p>

  <?php if ($pending): ?>
  <div class="mt-5 rounded-2xl bg-ink p-5 text-white">
    <div class="text-xs text-slate-400">Total pembayaran · <?= html_escape($order['payment_name']) ?></div>
    <div class="font-display text-3xl font-extrabold"><?= rupiah($order['amount']) ?></div>
    <?php if ($order['va_number']): ?>
      <div class="mt-4 text-xs text-slate-400">Nomor Virtual Account</div>
      <div class="flex items-center gap-3"><code class="text-xl font-bold tracking-wider" data-copy-text><?= html_escape($order['va_number']) ?></code>
        <button type="button" class="rounded-lg bg-white/15 px-3 py-1 text-xs font-bold" data-copy>Salin</button></div>
    <?php endif; ?>
    <div class="mt-4 flex items-center justify-between gap-3">
      <a href="<?= html_escape($order['payment_url']) ?>" class="btn-primary">Bayar Sekarang</a>
      <div class="text-right text-xs text-slate-400">Bayar dalam<div class="font-display text-lg font-extrabold text-white" data-timer>--:--</div></div>
    </div>
  </div>
  <p class="mt-2 text-xs text-slate-500">Halaman ini diperbarui otomatis setelah pembayaran diterima.</p>
  <?php endif; ?>

  <dl class="mt-5 divide-y divide-slate-200 rounded-2xl border border-slate-200 text-sm">
    <?php foreach ([
        'Produk' => $order['game_name'] . ' · ' . $order['item_label'],
        'ID Akun' => $order['target_user_id'] . ($order['target_zone_id'] ? ' (' . $order['target_zone_id'] . ')' : ''),
        'Email' => $order['email'],
        'Metode' => $order['payment_name'],
        'Total' => rupiah($order['amount']),
        'Dibuat' => $order['created_at'],
    ] as $k => $v): ?>
      <div class="flex justify-between gap-4 p-3"><dt class="text-slate-500"><?= $k ?></dt><dd class="text-right font-semibold"><?= html_escape($v) ?></dd></div>
    <?php endforeach; ?>
  </dl>

  <?php if (in_array($order['status'], ['expired', 'failed'], true)): ?>
    <a href="<?= site_url('game/' . $this->game_model->slug_by_id($order['game_id'])) ?>" class="btn-primary mt-5">Pesan lagi</a>
  <?php endif; ?>
</div>
