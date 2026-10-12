<div class="flex items-center gap-4">
  <span class="grid h-14 w-14 place-items-center rounded-full bg-brand text-xl font-bold text-white"><?= html_escape(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
  <div class="min-w-0"><h1 class="truncate font-display text-2xl font-extrabold"><?= html_escape($user['name']) ?></h1><p class="truncate text-sm text-slate-500"><?= html_escape($user['email']) ?></p></div>
</div>
<?php if (!empty($flash)): ?><p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800"><?= html_escape($flash) ?></p><?php endif; ?>
<?php $fe = $this->session->flashdata('flash_error'); if ($fe): ?><p class="mt-4 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"><?= html_escape($fe) ?></p><?php endif; ?>

<h2 class="mt-8 font-display text-lg font-extrabold">Riwayat pesanan</h2>
<div class="mt-3 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
  <?php foreach ($orders as $o): $i = order_status_info($o['status']); ?>
    <a href="<?= site_url('order/' . $o['order_no']) ?>" class="flex items-center justify-between gap-3 p-4 hover:bg-brand-soft/50">
      <div class="min-w-0"><div class="truncate text-sm font-bold"><?= html_escape($o['game_name'] . ' · ' . $o['item_label']) ?></div>
        <div class="text-xs text-slate-500"><?= html_escape($o['order_no']) ?> · <?= html_escape(substr($o['created_at'], 0, 16)) ?></div></div>
      <div class="shrink-0 text-right"><div class="text-sm font-bold"><?= rupiah($o['amount']) ?></div>
        <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-bold <?= $i[1] ?>"><?= $i[0] ?></span></div>
    </a>
  <?php endforeach; ?>
  <?php if (!$orders): ?><p class="p-6 text-center text-sm text-slate-500">Belum ada pesanan. <a class="font-semibold text-brand underline" href="<?= site_url('games') ?>">Mulai top up</a></p><?php endif; ?>
</div>

<h2 class="mt-8 font-display text-lg font-extrabold">Ganti password</h2>
<?= form_open('account/password', ['class' => 'mt-3 grid gap-2 sm:grid-cols-3']) ?>
  <input class="field" type="password" name="old" placeholder="Password lama" autocomplete="current-password" required>
  <input class="field" type="password" name="password" placeholder="Password baru (min 8)" autocomplete="new-password" required>
  <input class="field" type="password" name="password2" placeholder="Ulangi password baru" autocomplete="new-password" required>
  <div class="sm:col-span-3"><button class="btn-primary !py-2">Simpan password</button></div>
</form>
<?= form_open('auth/logout', ['class' => 'mt-8']) ?><button class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:border-rose-300 hover:text-rose-700">Keluar</button></form>
