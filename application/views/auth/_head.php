<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= html_escape($title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{DEFAULT:'#4F46E5',dark:'#3730A3',soft:'#EEF2FF'},ink:'#0F172A',paper:'#F8FAFC'},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],sans:['Inter','sans-serif']}}}}</script>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased">
<div class="grid min-h-screen lg:grid-cols-2">
  <aside class="relative hidden overflow-hidden bg-brand p-12 text-white lg:flex lg:flex-col lg:justify-between">
    <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-indigo-400/30"></div>
    <a href="<?= site_url() ?>" class="relative font-display text-2xl font-extrabold">MY<span class="text-indigo-200">Topup</span></a>
    <div class="relative max-w-md">
      <h2 class="font-display text-4xl font-extrabold leading-tight">Top up game favoritmu, <span class="text-indigo-200">masuk dalam 10 detik.</span></h2>
      <p class="mt-4 text-indigo-100">Simpan riwayat pesanan, lacak status pembayaran, dan checkout lebih cepat dengan akunmu.</p>
    </div>
    <p class="relative text-xs text-indigo-200">© <?= date('Y') ?> MYTopup</p>
  </aside>
  <main class="flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-sm">
      <a href="<?= site_url() ?>" class="mb-8 block font-display text-2xl font-extrabold lg:hidden">MY<span class="text-brand">Topup</span></a>
