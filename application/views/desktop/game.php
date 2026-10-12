<?= form_open('order/create', ['data-checkout' => '', 'class' => 'mx-auto max-w-6xl px-6 pt-10', 'novalidate' => 'novalidate']) ?>
  <input type="hidden" name="game" value="<?= html_escape($game['slug']) ?>">
  <input type="hidden" name="pack_id" value="<?= (int) set_value('pack_id') ?: '' ?>">
  <input type="hidden" name="payment_method" value="<?= html_escape(set_value('payment_method')) ?>">

  <a href="<?= site_url() ?>" class="text-sm text-slate-500 hover:text-brand">← Kembali</a>
  <?php if (!empty($global_error)): ?><div class="mt-4 rounded-xl bg-rose-50 p-4 text-sm font-semibold text-rose-700"><?= html_escape($global_error) ?></div><?php endif; ?>

  <?php if (empty($methods) || empty($pay_ready)): ?><div class="mt-4 rounded-xl bg-amber-50 p-4 text-sm font-semibold text-amber-800">Pembayaran belum tersedia. Silakan hubungi admin toko.</div><?php endif; ?>
  <div class="mt-4 grid grid-cols-3 gap-8">
    <aside class="col-span-1">
      <div class="sticky top-24 rounded-2xl border border-slate-200 p-6">
        <?= game_thumb($game, 'h-40 rounded-xl') ?>
        <h1 class="mt-4 font-display text-2xl font-extrabold"><?= html_escape($game['name']) ?></h1>
        <p class="text-sm text-slate-600">Top up <?= html_escape($game['currency']) ?> instan, proses otomatis 24 jam.</p>
      </div>
    </aside>

    <div class="col-span-2 space-y-6">
      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>1</span> Masukkan ID akun</h2>
        <div class="mt-4 grid <?= $game['needs_zone'] ? 'grid-cols-3' : 'grid-cols-1' ?> gap-3">
          <div class="<?= $game['needs_zone'] ? 'col-span-2' : '' ?>">
            <input type="text" name="user_id" value="<?= set_value('user_id') ?>" placeholder="User ID" class="field" data-userid maxlength="30" autocomplete="off">
            <?= form_error('user_id', '<p class="err">', '</p>') ?>
          </div>
          <?php if ($game['needs_zone']): ?>
          <div><input type="text" inputmode="numeric" name="zone_id" value="<?= set_value('zone_id') ?>" placeholder="Zone ID" class="field" maxlength="10" autocomplete="off">
            <?= form_error('zone_id', '<p class="err">', '</p>') ?></div>
          <?php endif; ?>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>2</span> Pilih nominal</h2>
        <div class="mt-4 grid grid-cols-3 gap-3">
          <?php foreach ($game['packs'] as $p): ?>
            <button type="button" class="pack" data-pack data-id="<?= $p[2] ?>" data-label="<?= html_escape($p[0] . ' ' . $game['currency']) ?>" data-price="<?= $p[1] ?>">
              <span class="font-display font-extrabold"><?= html_escape($p[0]) ?></span>
              <span class="text-xs text-slate-500"><?= html_escape($game['currency']) ?></span>
              <span class="mt-2 text-sm font-bold text-brand"><?= rupiah($p[1]) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <?= form_error('pack_id', '<p class="err">', '</p>') ?>
      </section>

      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>3</span> Kontak</h2>
        <div class="mt-4 grid grid-cols-2 gap-3">
          <div><input type="email" name="email" value="<?= set_value('email', isset($user['email']) ? $user['email'] : '') ?>" placeholder="Email (bukti pembayaran)" class="field" data-email maxlength="160">
            <?= form_error('email', '<p class="err">', '</p>') ?></div>
          <div><input type="tel" name="whatsapp" value="<?= set_value('whatsapp') ?>" placeholder="WhatsApp (opsional)" class="field" maxlength="16">
            <?= form_error('whatsapp', '<p class="err">', '</p>') ?></div>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 p-6">
        <h2 class="step-title"><span>4</span> Metode pembayaran</h2>
        <div class="mt-4 grid grid-cols-4 gap-3">
          <?php foreach ($methods as $code => $name): ?>
            <button type="button" class="pack py-4" data-pay data-code="<?= html_escape($code) ?>"><?= html_escape($name) ?></button>
          <?php endforeach; ?>
        </div>
        <?= form_error('payment_method', '<p class="err">', '</p>') ?>
      </section>

      <div class="flex items-center justify-between rounded-2xl bg-ink p-6 text-white">
        <div><div class="text-xs text-slate-400" data-summary>Pilih nominal</div><div class="font-display text-2xl font-extrabold" data-total>Rp0</div></div>
        <button type="submit" class="btn-primary opacity-50" data-buy disabled>Bayar Sekarang</button>
      </div>
    </div>
  </div>
</form>
