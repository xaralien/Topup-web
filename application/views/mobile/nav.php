<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="flex items-center justify-between px-4 h-14">
    <a href="<?= site_url() ?>" class="font-display font-extrabold text-lg">MY<span class="text-brand">Topup</span></a>
    <?php if (!empty($user)): ?>
      <a href="<?= site_url('account') ?>" class="flex items-center gap-2 text-xs font-bold">
        <span class="grid h-7 w-7 place-items-center rounded-full bg-brand text-xs font-bold text-white"><?= html_escape(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span></a>
    <?php else: ?>
      <a href="<?= site_url('auth/login') ?>" class="rounded-lg bg-brand px-3 py-1.5 text-xs font-bold text-white">Masuk</a>
    <?php endif; ?>
  </div>
  <?php $this->load->view('partials/search_box', ['class' => 'px-4 pb-3']); ?>
</header>
