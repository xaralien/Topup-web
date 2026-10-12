<?php $this->load->view('auth/_head', ['title' => $title]); ?>
      <h1 class="font-display text-3xl font-extrabold">Masuk</h1>
      <p class="mt-1 text-sm text-slate-500">Selamat datang kembali 👋</p>
      <?php if ($no_admin): ?><p class="mt-5 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">Belum ada akun admin. <a href="<?= site_url('auth/setup') ?>" class="font-bold underline">Buat admin pertama →</a></p><?php endif; ?>
      <?php if ($error): ?><p class="mt-5 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700" role="alert"><?= html_escape($error) ?></p><?php endif; ?>
      <?= form_open('auth/login' . ($next ? '?next=' . rawurlencode($next) : ''), ['class' => 'mt-6 space-y-4']) ?>
        <label class="block text-sm font-semibold">Email atau username admin
          <input class="field mt-1" name="identity" value="<?= html_escape($identity) ?>" autocomplete="username" required autofocus maxlength="160"></label>
        <label class="block text-sm font-semibold">Password
          <input class="field mt-1" type="password" name="password" autocomplete="current-password" required maxlength="200"></label>
        <button class="btn-primary w-full">Masuk</button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">Belum punya akun? <a href="<?= site_url('auth/register') ?>" class="font-bold text-brand hover:underline">Daftar gratis</a></p>
      <p class="mt-3 text-center text-xs text-slate-400"><a href="<?= site_url() ?>" class="hover:text-brand">← Kembali ke beranda</a></p>
<?php $this->load->view('auth/_foot'); ?>
