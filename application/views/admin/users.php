<h1 class="font-display text-2xl font-extrabold">Akun Admin</h1>
<div class="mt-5 max-w-3xl space-y-2">
  <?php foreach ($users as $u): ?>
    <div class="rounded-xl border border-slate-200 bg-white p-3">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <div><span class="font-semibold"><?= html_escape($u['name']) ?></span> <span class="text-sm text-slate-500">@<?= html_escape($u['username']) ?></span>
          <?= (int) $u['id'] === (int) $admin['id'] ? '<span class="ml-1 rounded-full bg-brand-soft px-2 py-0.5 text-xs font-bold text-brand">Anda</span>' : '' ?>
          <div class="text-xs text-slate-500">Login terakhir: <?= html_escape($u['last_login_at'] ?: '-') ?></div></div>
        <?php if ((int) $u['id'] !== (int) $admin['id']): ?>
          <?= form_open('admin/users/delete/' . $u['id'], ['onsubmit' => "return confirm('Hapus admin ini?')"]) ?><button class="text-xs font-bold text-rose-700 hover:underline">Hapus</button></form>
        <?php endif; ?>
      </div>
      <?= form_open('admin/users/password/' . $u['id'], ['class' => 'mt-2 flex flex-wrap gap-2']) ?>
        <input class="field !w-48 !py-2 text-sm" type="password" name="password" placeholder="Password baru (min 10)" autocomplete="new-password" required>
        <input class="field !w-48 !py-2 text-sm" type="password" name="password2" placeholder="Ulangi password" autocomplete="new-password" required>
        <button class="rounded-lg bg-ink px-3 py-2 text-xs font-bold text-white">Ganti password</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<h2 class="mt-8 font-display text-lg font-extrabold">Tambah admin</h2>
<?= form_open('admin/users/create', ['class' => 'mt-3 grid max-w-3xl gap-2 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-2']) ?>
  <input class="field" name="username" placeholder="username (a-z, 0-9, _ .)" maxlength="30" required autocomplete="off">
  <input class="field" name="name" placeholder="Nama lengkap" maxlength="80" required>
  <input class="field" type="password" name="password" placeholder="Password (min 10 karakter)" autocomplete="new-password" required>
  <input class="field" type="password" name="password2" placeholder="Ulangi password" autocomplete="new-password" required>
  <div class="md:col-span-2"><button class="btn-primary !py-2">Tambah admin</button></div>
</form>
