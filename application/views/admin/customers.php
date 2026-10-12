<header><h1 class="a-title">Pelanggan</h1><p class="a-sub mt-1"><?= count($customers) ?> akun terdaftar</p></header>
<div class="a-card mt-6 overflow-x-auto">
  <table class="a-table">
    <thead><tr><th>Nama</th><th>Email</th><th class="text-right">Pesanan</th><th class="text-right">Total belanja</th><th>Daftar</th><th>Login terakhir</th></tr></thead>
    <tbody>
    <?php foreach ($customers as $c): ?>
      <tr>
        <td><div class="flex items-center gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-brand-soft text-xs font-bold text-brand"><?= html_escape(mb_strtoupper(mb_substr($c['name'], 0, 1))) ?></span><span class="font-semibold"><?= html_escape($c['name']) ?></span></div></td>
        <td class="text-slate-600"><?= html_escape($c['email']) ?></td>
        <td class="text-right"><?= (int) $c['orders'] ?></td>
        <td class="text-right font-bold"><?= rupiah($c['spent']) ?></td>
        <td class="whitespace-nowrap text-slate-500"><?= html_escape(substr($c['created_at'], 0, 10)) ?></td>
        <td class="whitespace-nowrap text-slate-500"><?= html_escape($c['last_login_at'] ? substr($c['last_login_at'], 0, 16) : '-') ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$customers): ?><tr><td colspan="6" class="!py-10 text-center text-slate-400">Belum ada pelanggan terdaftar.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
