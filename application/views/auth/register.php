<?php $this->load->view('auth/_head', ['title' => $title]); ?>
      <h1 class="font-display text-3xl font-extrabold">Buat akun</h1>
      <p class="mt-1 text-sm text-slate-500">Gratis, kurang dari satu menit.</p>
      <?= form_open('auth/register', ['class' => 'mt-6 space-y-4', 'novalidate' => 'novalidate']) ?>
        <label class="block text-sm font-semibold">Nama
          <input class="field mt-1" name="name" value="<?= set_value('name') ?>" autocomplete="name" required maxlength="80">
          <?= form_error('name', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Email
          <input class="field mt-1" type="email" name="email" value="<?= set_value('email') ?>" autocomplete="email" required maxlength="160">
          <?= form_error('email', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Password <span class="font-normal text-slate-400">(min 8 karakter)</span>
          <input class="field mt-1" type="password" name="password" autocomplete="new-password" required maxlength="200">
          <?= form_error('password', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Ulangi password
          <input class="field mt-1" type="password" name="password2" autocomplete="new-password" required maxlength="200">
          <?= form_error('password2', '<p class="err">', '</p>') ?></label>
        <button class="btn-primary w-full">Daftar</button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">Sudah punya akun? <a href="<?= site_url('auth/login') ?>" class="font-bold text-brand hover:underline">Masuk</a></p>
<?php $this->load->view('auth/_foot'); ?>
