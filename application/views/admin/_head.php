<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#4F46E5">
<title><?= html_escape($title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{DEFAULT:'#4F46E5',dark:'#3730A3',soft:'#EEF2FF'},ink:'#0F172A',paper:'#F8FAFC'},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],sans:['Inter','sans-serif']}}}}</script>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin font-sans antialiased">
<?php
$seg = $this->uri->segment(2);
$nav = [
  ['',          'Dashboard',      'admin',            'home'],
  ['orders',    'Pesanan',        'admin/orders',     'receipt'],
  ['games',     'Game & Produk',  'admin/games',      'grid'],
  ['customers', 'Pelanggan',      'admin/customers',  'users'],
  ['users',     'Akun Admin',     'admin/users',      'shield'],
  ['settings',  'Pengaturan',     'admin/settings',   'settings'],
];
$initial = html_escape(mb_strtoupper(mb_substr($admin['name'], 0, 1)));
?>
<div class="md:flex md:min-h-screen">
  <!-- Sidebar (desktop) -->
  <aside class="a-side hidden md:flex md:w-64 md:shrink-0 md:flex-col md:sticky md:top-0 md:h-screen">
    <a href="<?= site_url('admin') ?>" class="px-6 pb-4 pt-6 font-display text-xl font-extrabold">MY<span class="text-brand">Topup</span><span class="ml-2 rounded-md bg-brand-soft px-1.5 py-0.5 align-middle text-[10px] font-bold uppercase tracking-wide text-brand">admin</span></a>
    <nav class="flex-1 space-y-1 px-3 pt-2">
      <?php foreach ($nav as $n): $on = ($n[0] === '' ? (empty($seg) || $seg === 'home') : $seg === $n[0]); ?>
        <a href="<?= site_url($n[2]) ?>" class="a-link <?= $on ? 'is-on' : '' ?>"><?= icon($n[3]) ?><?= $n[1] ?></a>
      <?php endforeach; ?>
      <a href="<?= site_url() ?>" target="_blank" rel="noopener" class="a-link mt-4"><?= icon('external') ?>Lihat situs</a>
    </nav>
    <div class="m-3 rounded-2xl border border-[var(--line)] bg-paper p-3">
      <div class="flex items-center gap-3">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand text-sm font-bold text-white"><?= $initial ?></span>
        <div class="min-w-0"><div class="truncate text-sm font-bold"><?= html_escape($admin['name']) ?></div><div class="truncate text-xs text-slate-500">@<?= html_escape($admin['username']) ?></div></div>
      </div>
      <?= form_open('auth/logout', ['class' => 'mt-3']) ?><button class="a-btn-soft w-full justify-center"><?= icon('logout', 'h-4 w-4') ?>Keluar</button></form>
    </div>
  </aside>

  <!-- Bottom tab (mobile) -->
  <nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-6 border-t border-[var(--line)] bg-white/95 backdrop-blur md:hidden">
    <?php foreach ($nav as $n): $on = ($n[0] === '' ? (empty($seg) || $seg === 'home') : $seg === $n[0]); ?>
      <a href="<?= site_url($n[2]) ?>" class="flex flex-col items-center gap-0.5 py-2 text-[10px] font-semibold <?= $on ? 'text-brand' : 'text-slate-500' ?>"><?= icon($n[3], 'h-5 w-5') ?><?= html_escape(explode(' ', $n[1])[0]) ?></a>
    <?php endforeach; ?>
  </nav>

  <main class="min-w-0 flex-1 px-4 py-5 md:px-10 md:py-8">
    <div class="mb-4 flex items-center justify-between md:hidden">
      <a href="<?= site_url('admin') ?>" class="font-display text-lg font-extrabold">MY<span class="text-brand">Topup</span></a>
      <?= form_open('auth/logout') ?><button class="a-btn-soft !py-1.5"><?= icon('logout', 'h-4 w-4') ?>Keluar</button></form>
    </div>
    <?php if (!empty($flash)): ?><div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800"><?= icon('check', 'h-4 w-4') ?><?= html_escape($flash) ?></div><?php endif; ?>
    <?php if (!empty($flash_error)): ?><div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700"><?= html_escape($flash_error) ?></div><?php endif; ?>
