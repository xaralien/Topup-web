<header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
  <div class="mx-auto max-w-6xl flex items-center gap-6 px-6 h-16">
    <a href="<?= site_url() ?>" class="font-display font-extrabold text-xl">MY<span class="text-brand">Topup</span></a>
    <?php $this->load->view('partials/search_box', ['class' => 'flex-1 max-w-sm']); ?>
    <nav class="ml-auto flex items-center gap-6 text-sm font-semibold text-slate-600">
      <a href="<?= site_url('games') ?>" class="hover:text-brand">Semua Game</a>
      <a href="<?= site_url() ?>#promo" class="hover:text-brand">Promo</a>
      <a href="<?= site_url() ?>#faq" class="hover:text-brand">FAQ</a>
      <a href="<?= site_url('order/check') ?>" class="hover:text-brand">Cek Pesanan</a>
      <?php if (!empty($user)): ?>
        <a href="<?= site_url('account') ?>" class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 hover:border-brand">
          <span class="grid h-7 w-7 place-items-center rounded-full bg-brand text-xs font-bold text-white"><?= html_escape(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
          <span class="max-w-[8rem] truncate font-bold text-ink"><?= html_escape($user['name']) ?></span></a>
      <?php else: ?>
        <a href="<?= site_url('auth/login') ?>" class="rounded-xl bg-brand px-4 py-2 font-bold text-white hover:bg-brand-dark transition">Masuk</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
