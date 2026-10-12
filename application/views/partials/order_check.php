<h1 class="font-display text-2xl font-extrabold">Cek Pesanan</h1>
<p class="mt-1 text-sm text-slate-600">Masukkan nomor pesanan (contoh: MT2610…) yang dikirim ke emailmu.</p>
<form action="<?= site_url('order/check') ?>" method="get" class="mt-5 flex gap-2">
  <input type="text" name="no" value="<?= html_escape($no) ?>" placeholder="Nomor pesanan" class="field" maxlength="24" required>
  <button class="btn-primary shrink-0">Cari</button>
</form>
<?php if ($error): ?><p class="err mt-3"><?= html_escape($error) ?></p><?php endif; ?>
