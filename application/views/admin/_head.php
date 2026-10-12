<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= html_escape($title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{DEFAULT:'#4F46E5',dark:'#3730A3',soft:'#EEF2FF'},ink:'#0F172A',paper:'#F8FAFC'},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],sans:['Inter','sans-serif']}}}}</script>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="bg-paper font-sans text-ink antialiased">
<?php $seg = $this->uri->segment(2); $nav = ['' => ['Dashboard', 'admin'], 'orders' => ['Pesanan', 'admin/orders'], 'games' => ['Game & Produk', 'admin/games'], 'users' => ['Akun Admin', 'admin/users']]; ?>
<div class="md:flex min-h-screen">
  <aside class="md:w-60 md:shrink-0 bg-ink text-slate-300 md:min-h-screen">
    <div class="flex items-center justify-between px-5 py-4 md:block">
      <a href="<?= site_url('admin') ?>" class="font-display text-lg font-extrabold text-white">MY<span class="text-indigo-400">Topup</span> <span class="text-xs font-semibold text-slate-400">admin</span></a>
    </div>
    <nav class="flex gap-1 overflow-x-auto px-3 pb-3 md:block md:space-y-1 md:px-3">
      <?php foreach ($nav as $key => $n): $on = ($key === '' ? (empty($seg) || $seg === 'dashboard') : $seg === $key); ?>
        <a href="<?= site_url($n[1]) ?>" class="block whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold <?= $on ? 'bg-white/10 text-white' : 'hover:bg-white/5' ?>"><?= $n[0] ?></a>
      <?php endforeach; ?>
      <a href="<?= site_url() ?>" target="_blank" rel="noopener" class="block whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold hover:bg-white/5">Lihat situs ↗</a>
    </nav>
    <div class="hidden md:block px-5 pt-6 text-xs text-slate-400">
      Masuk sebagai<div class="font-semibold text-white"><?= html_escape($admin['name']) ?></div>
      <?= form_open('admin/logout', ['class' => 'mt-2']) ?><button class="font-semibold text-indigo-300 hover:underline">Keluar</button></form>
    </div>
  </aside>
  <main class="flex-1 min-w-0 p-4 md:p-8">
    <div class="md:hidden mb-3 flex items-center justify-between text-xs text-slate-500">
      <span><?= html_escape($admin['name']) ?></span>
      <?= form_open('admin/logout') ?><button class="font-semibold text-brand">Keluar</button></form>
    </div>
    <?php if (!empty($flash)): ?><div class="mb-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800"><?= html_escape($flash) ?></div><?php endif; ?>
    <?php if (!empty($flash_error)): ?><div class="mb-4 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"><?= html_escape($flash_error) ?></div><?php endif; ?>
