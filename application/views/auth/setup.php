<?php $this->load->view('auth/_head', ['title' => $title]); ?>
      <h1 class="font-display text-3xl font-extrabold">Buat admin pertama</h1>
      <p class="mt-1 text-sm text-slate-500">Halaman ini hilang otomatis setelah admin pertama dibuat.</p>
      <?php if ($error): ?><p class="mt-5 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700" role="alert"><?= html_escape($error) ?></p><?php endif; ?>
      <?= form_open('auth/setup', ['class' => 'mt-6 space-y-4', 'novalidate' => 'novalidate']) ?>
        <label class="block text-sm font-semibold">Username <span class="font-normal text-slate-400">(a-z, 0-9, _ .)</span>
          <input class="field mt-1" name="username" value="<?= set_value('username') ?>" autocomplete="username" required maxlength="30" autofocus>
          <?= form_error('username', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Nama
          <input class="field mt-1" name="name" value="<?= set_value('name') ?>" autocomplete="name" required maxlength="80">
          <?= form_error('name', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Password <span class="font-normal text-slate-400">(min 10 karakter)</span>
          <input class="field mt-1" type="password" name="password" autocomplete="new-password" required maxlength="200">
          <?= form_error('password', '<p class="err">', '</p>') ?></label>
        <label class="block text-sm font-semibold">Ulangi password
          <input class="field mt-1" type="password" name="password2" autocomplete="new-password" required maxlength="200">
          <?= form_error('password2', '<p class="err">', '</p>') ?></label>
        <?php if ($needs_key): ?>
        <label class="block text-sm font-semibold">Kunci setup <span class="font-normal text-slate-400">(SETUP_KEY di .env)</span>
          <input class="field mt-1" type="password" name="setup_key" autocomplete="off" required></label>
        <?php endif; ?>
        <button class="btn-primary w-full">Buat admin &amp; masuk</button>
      </form>
<?php $this->load->view('auth/_foot'); ?>
