<?php
$h = (int) date('G'); $sapa = $h < 11 ? 'Selamat pagi' : ($h < 15 ? 'Selamat siang' : ($h < 19 ? 'Selamat sore' : 'Selamat malam'));
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; $bulan = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$max = max(1, max(array_column($chart, 'sum')));
$total14 = array_sum(array_column($chart, 'sum'));
$cards = [
  ['Pendapatan hari ini', rupiah($stats['revenue_today']), 'wallet', 'bg-emerald-50 text-emerald-600', $stats['orders_today'] . ' pesanan masuk'],
  ['Pendapatan 30 hari', rupiah($stats['revenue_30d']), 'trend', 'bg-indigo-50 text-indigo-600', 'dari pembayaran sukses'],
  ['Menunggu pembayaran', $stats['pending'], 'clock', 'bg-amber-50 text-amber-600', 'belum dibayar pembeli'],
  ['Perlu dikirim', $stats['to_fulfill'], 'bolt', 'bg-rose-50 text-rose-600', 'sudah dibayar, tunggu item'],
];
$labels = ['pending' => ['Menunggu', '#F59E0B'], 'paid' => ['Dibayar', '#6366F1'], 'processing' => ['Diproses', '#8B5CF6'], 'success' => ['Berhasil', '#10B981'], 'failed' => ['Gagal', '#F43F5E'], 'expired' => ['Kedaluwarsa', '#94A3B8']];
$sum_status = max(1, array_sum($status_counts));
?>
<header class="flex flex-wrap items-end justify-between gap-3">
  <div><h1 class="a-title"><?= $sapa ?>, <?= html_escape(explode(' ', $admin['name'])[0]) ?> 👋</h1>
    <p class="a-sub mt-1"><?= $hari[(int) date('w')] ?>, <?= (int) date('j') ?> <?= $bulan[(int) date('n')] ?> <?= date('Y') ?> · ringkasan toko hari ini</p></div>
  <a href="<?= site_url('admin/games/create') ?>" class="a-btn"><?= icon('plus', 'h-4 w-4') ?>Tambah game</a>
</header>

<section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
  <?php foreach ($cards as $c): ?>
    <div class="a-card a-stat">
      <div class="flex items-center justify-between"><span class="text-xs font-semibold text-slate-500"><?= $c[0] ?></span><span class="a-chip <?= $c[3] ?>"><?= icon($c[2], 'h-5 w-5') ?></span></div>
      <div class="mt-2 font-display text-[1.45rem] font-extrabold leading-tight tracking-tight"><?= $c[1] ?></div>
      <div class="mt-0.5 text-xs text-slate-400"><?= $c[4] ?></div>
    </div>
  <?php endforeach; ?>
</section>

<?php if ($stats['to_fulfill'] > 0): ?>
<a href="<?= site_url('admin/orders?status=processing') ?>" class="mt-4 flex items-center gap-3 rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-white p-4 transition hover:border-indigo-300">
  <span class="a-chip bg-brand text-white"><?= icon('bolt') ?></span>
  <span class="flex-1 text-sm"><b><?= $stats['to_fulfill'] ?> pesanan</b> sudah dibayar dan menunggu pengiriman item.</span>
  <span class="text-sm font-bold text-brand">Proses →</span>
</a>
<?php endif; ?>

<section class="mt-4 grid gap-4 lg:grid-cols-3">
  <div class="a-card p-5 lg:col-span-2">
    <div class="flex items-start justify-between"><div><h2 class="font-display text-base font-extrabold">Pendapatan 14 hari</h2><p class="a-sub">Total <?= rupiah($total14) ?></p></div></div>
    <svg viewBox="0 0 560 190" class="mt-4 w-full" role="img" aria-label="Grafik pendapatan 14 hari terakhir">
      <?php foreach ([0.25, 0.5, 0.75, 1] as $g): $y = 150 - 130 * $g; ?><line x1="0" x2="560" y1="<?= $y ?>" y2="<?= $y ?>" stroke="#EEF0F7" stroke-width="1"/><?php endforeach; ?>
      <line x1="0" x2="560" y1="150" y2="150" stroke="#E2E6F2"/>
      <?php $n = count($chart); $bw = 24; $step = 560 / $n; foreach ($chart as $i => $d): $hgt = $d['sum'] > 0 ? max(3, 130 * $d['sum'] / $max) : 0; $x = $i * $step + ($step - $bw) / 2; ?>
        <rect class="a-bar <?= $i === $n - 1 ? 'is-today' : '' ?>" x="<?= round($x, 1) ?>" y="<?= round(150 - $hgt, 1) ?>" width="<?= $bw ?>" height="<?= round($hgt, 1) ?>" rx="6"><title><?= date('j/n', strtotime($d['date'])) ?> · <?= rupiah($d['sum']) ?> · <?= $d['n'] ?> pesanan</title></rect>
        <?php if ($i % 2 === 0 || $i === $n - 1): ?><text x="<?= round($x + $bw / 2, 1) ?>" y="172" text-anchor="middle" font-size="11" fill="#94A3B8"><?= date('j/n', strtotime($d['date'])) ?></text><?php endif; ?>
      <?php endforeach; ?>
    </svg>
    <?php if ($total14 === 0): ?><p class="-mt-24 mb-14 text-center text-sm text-slate-400">Belum ada pembayaran dalam 14 hari terakhir.</p><?php endif; ?>
  </div>

  <div class="a-card p-5">
    <h2 class="font-display text-base font-extrabold">Status pesanan</h2><p class="a-sub">Semua waktu</p>
    <div class="mt-4 space-y-3">
      <?php foreach ($labels as $k => $l): $v = isset($status_counts[$k]) ? (int) $status_counts[$k] : 0; ?>
        <a href="<?= site_url('admin/orders?status=' . $k) ?>" class="block">
          <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2 font-semibold text-slate-600"><span class="h-2.5 w-2.5 rounded-full" style="background:<?= $l[1] ?>"></span><?= $l[0] ?></span><span class="font-bold"><?= $v ?></span></div>
          <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full" style="width:<?= round(100 * $v / $sum_status) ?>%;background:<?= $l[1] ?>"></div></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="mt-4">
  <div class="mb-3 flex items-center justify-between"><h2 class="font-display text-base font-extrabold">Pesanan terbaru</h2><a href="<?= site_url('admin/orders') ?>" class="text-sm font-bold text-brand hover:underline">Lihat semua →</a></div>
  <?php $this->load->view('admin/_order_table', ['rows' => $recent]); ?>
</section>
