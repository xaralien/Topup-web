<h1 class="font-display text-2xl font-extrabold">Dashboard</h1>
<div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-5">
  <?php foreach ([
    ['Pesanan hari ini', $stats['orders_today'], 'text-ink'],
    ['Pendapatan hari ini', rupiah($stats['revenue_today']), 'text-emerald-600'],
    ['Pendapatan 30 hari', rupiah($stats['revenue_30d']), 'text-emerald-600'],
    ['Menunggu bayar', $stats['pending'], 'text-amber-600'],
    ['Perlu dikirim', $stats['to_fulfill'], 'text-brand'],
  ] as $c): ?>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><div class="text-xs text-slate-500"><?= $c[0] ?></div><div class="mt-1 font-display text-xl font-extrabold <?= $c[2] ?>"><?= $c[1] ?></div></div>
  <?php endforeach; ?>
</div>
<?php if ($stats['to_fulfill'] > 0): ?>
  <a href="<?= site_url('admin/orders?status=processing') ?>" class="mt-4 block rounded-xl bg-indigo-50 p-3 text-sm font-semibold text-indigo-800">⚡ <?= $stats['to_fulfill'] ?> pesanan sudah dibayar dan menunggu pengiriman item → lihat</a>
<?php endif; ?>
<h2 class="mt-8 font-display text-lg font-extrabold">Pesanan terbaru</h2>
<div class="mt-3"><?php $this->load->view('admin/_order_table', ['rows' => $recent]); ?></div>
