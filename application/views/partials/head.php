<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= html_escape($title) ?></title>
<meta name="description" content="Top up diamond, UC, voucher, dan akun game. Harga jujur, bayar QRIS/e-wallet, proses otomatis 24 jam.">
<meta name="theme-color" content="#4F46E5">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>
tailwind.config = {
  theme: { extend: {
    colors: { brand: { DEFAULT:'#4F46E5', dark:'#3730A3', soft:'#EEF2FF' }, ink:'#0F172A', mint:'#10B981', paper:'#F8FAFC' },
    fontFamily: { display:['"Plus Jakarta Sans"','sans-serif'], sans:['Inter','sans-serif'] }
  } }
}
</script>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="bg-white font-sans text-ink antialiased <?= $mode === 'mobile' ? 'pb-20' : '' ?>">
<?php $this->load->view($mode . '/nav'); ?>
