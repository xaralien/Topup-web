<?= form_open('order/create', ['data-checkout' => '', 'class' => 'pb-40', 'novalidate' => 'novalidate']) ?>
  <input type="hidden" name="game" value="<?= html_escape($game['slug']) ?>">
  <input type="hidden" name="pack_id" value="<?= (int) set_value('pack_id') ?: '' ?>">
  <input type="hidden" name="payment_method" value="<?= html_escape(set_value('payment_method')) ?>">

  <div class="flex items-center gap-3 px-4 pt-4">
    <a href="<?= site_url() ?>" class="text-slate-500">←</a>
    <div class="h-12 w-12 rounded-xl" style="background:<?= $game['color'] ?>"></div>
    <div><h1 class="font-display text-lg font-extrabold leading-tight"><?= html_escape($game['name']) ?></h1><p class="text-xs text-slate-500">Proses otomatis 24 jam</p></div>
  </div>
  <?php if (!empty($global_error)): ?><div class="mx-4 mt-4 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"><?= html_escape($global_error) ?></div><?php endif; ?>

  <section class="mx-4 mt-5 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>1</span> ID akun</h2>
    <div class="mt-3 flex gap-2">
      <div class="flex-1"><input type="text" name="user_id" value="<?= set_value('user_id') ?>" placeholder="User ID" class="field" data-userid maxlength="30" autocomplete="off"></div>
      <?php if ($game['needs_zone']): ?><div class="w-28"><input type="text" inputmode="numeric" name="zone_id" value="<?= set_value('zone_id') ?>" placeholder="Zone" class="field" maxlength="10" autocomplete="off"></div><?php endif; ?>
    </div>
    <?= form_error('user_id', '<p class="err">', '</p>') ?><?= form_error('zone_id', '<p class="err">', '</p>') ?>
  </section>

  <section class="mx-4 mt-4 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>2</span> Nominal</h2>
    <div class="mt-3 grid grid-cols-2 gap-2">
      <?php foreach ($game['packs'] as $p): ?>
        <button type="button" class="pack" data-pack data-id="<?= $p[2] ?>" data-label="<?= html_escape($p[0] . ' ' . $game['currency']) ?>" data-price="<?= $p[1] ?>">
          <span class="font-display font-extrabold"><?= html_escape($p[0]) ?> <small class="font-sans text-xs font-normal text-slate-500"><?= html_escape($game['currency']) ?></small></span>
          <span class="text-sm font-bold text-brand"><?= rupiah($p[1]) ?></span>
        </button>
      <?php endforeach; ?>
    </div>
    <?= form_error('pack_id', '<p class="err">', '</p>') ?>
  </section>

  <section class="mx-4 mt-4 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>3</span> Kontak</h2>
    <input type="email" name="email" value="<?= set_value('email') ?>" placeholder="Email (bukti pembayaran)" class="field mt-3" data-email maxlength="160">
    <?= form_error('email', '<p class="err">', '</p>') ?>
    <input type="tel" name="whatsapp" value="<?= set_value('whatsapp') ?>" placeholder="WhatsApp (opsional)" class="field mt-2" maxlength="16">
    <?= form_error('whatsapp', '<p class="err">', '</p>') ?>
  </section>

  <section class="mx-4 mt-4 rounded-xl border border-slate-200 p-4">
    <h2 class="step-title"><span>4</span> Pembayaran</h2>
    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
      <?php foreach ($methods as $code => $name): ?>
        <button type="button" class="pack py-3" data-pay data-code="<?= html_escape($code) ?>"><?= html_escape($name) ?></button>
      <?php endforeach; ?>
    </div>
    <?= form_error('payment_method', '<p class="err">', '</p>') ?>
  </section>

  <div class="fixed inset-x-0 bottom-14 z-30 flex items-center justify-between bg-ink px-4 py-3 text-white">
    <div><div class="text-[11px] text-slate-400" data-summary>Pilih nominal</div><div class="font-display font-extrabold" data-total>Rp0</div></div>
    <button type="submit" class="btn-primary !py-2 opacity-50" data-buy disabled>Bayar</button>
  </div>
</form>
