<header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
  <div class="mx-auto max-w-6xl flex items-center gap-6 px-6 h-16">
    <a href="<?= site_url() ?>" class="font-display font-extrabold text-xl">MY<span class="text-brand">Topup</span></a>
    <?php $this->load->view('partials/search_box', ['class' => 'flex-1 max-w-sm']); ?>
    <nav class="ml-auto flex items-center gap-6 text-sm font-semibold text-slate-600">
      <a href="<?= site_url('games') ?>" class="hover:text-brand">Semua Game</a>
      <a href="<?= site_url() ?>#promo" class="hover:text-brand">Promo</a>
      <a href="<?= site_url() ?>#faq" class="hover:text-brand">FAQ</a>
      <a href="<?= site_url('order/check') ?>" class="hover:text-brand">Cek Pesanan</a>
      <a href="#" class="rounded-xl bg-brand px-4 py-2 font-bold text-white hover:bg-brand-dark transition">Masuk</a>
    </nav>
  </div>
</header>
