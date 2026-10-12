<h1 class="font-display text-2xl font-extrabold">Pesanan <span class="text-sm font-normal text-slate-500">(<?= $total ?>)</span></h1>
<form method="get" action="<?= site_url('admin/orders') ?>" class="mt-4 flex flex-wrap gap-2">
  <select name="status" class="field !w-auto"><option value="">Semua status</option>
    <?php foreach ($statuses as $s): ?><option value="<?= $s ?>" <?= $s === $status ? 'selected' : '' ?>><?= order_status_info($s)[0] ?></option><?php endforeach; ?></select>
  <input name="q" value="<?= html_escape($q) ?>" placeholder="No. pesanan / email / ID akun" class="field !w-72">
  <button class="btn-primary !py-2">Cari</button>
</form>
<div class="mt-4"><?php $this->load->view('admin/_order_table', ['rows' => $rows]); ?></div>
<?php if ($pages > 1): $qs = http_build_query(['status' => $status, 'q' => $q]); ?>
  <div class="mt-4 flex items-center gap-2 text-sm">
    <?php if ($page > 1): ?><a class="chip" href="?<?= $qs ?>&page=<?= $page - 1 ?>">← Sebelumnya</a><?php endif; ?>
    <span class="text-slate-500">Hal. <?= $page ?> / <?= $pages ?></span>
    <?php if ($page < $pages): ?><a class="chip" href="?<?= $qs ?>&page=<?= $page + 1 ?>">Berikutnya →</a><?php endif; ?>
  </div>
<?php endif; ?>
