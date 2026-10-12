<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Masuk Admin · MYTopup</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:{DEFAULT:'#4F46E5',dark:'#3730A3',soft:'#EEF2FF'},ink:'#0F172A',paper:'#F8FAFC'},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],sans:['Inter','sans-serif']}}}}</script>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="grid min-h-screen place-items-center bg-paper px-4 font-sans text-ink antialiased">
<div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
  <div class="font-display text-2xl font-extrabold">MY<span class="text-brand">Topup</span> <span class="text-sm text-slate-400">admin</span></div>
  <?php if ($no_admin): ?>
    <p class="mt-4 rounded-xl bg-amber-50 p-3 text-sm text-amber-800">Belum ada akun admin. Buat lewat terminal:<br><code class="font-semibold">php index.php cli create_admin namaanda</code></p>
  <?php endif; ?>
  <?php if ($error): ?><p class="mt-4 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"><?= html_escape($error) ?></p><?php endif; ?>
  <?= form_open('admin/login', ['class' => 'mt-5 space-y-3']) ?>
    <input class="field" name="username" placeholder="Username" autocomplete="username" required autofocus maxlength="30">
    <input class="field" type="password" name="password" placeholder="Password" autocomplete="current-password" required maxlength="200">
    <button class="btn-primary w-full">Masuk</button>
  </form>
</div>
</body>
</html>
