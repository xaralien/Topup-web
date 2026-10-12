<?php $isnew = !$game; $g = $game ?: ['name' => '', 'slug' => '', 'category' => '', 'currency' => '', 'color' => '#E2E8F0', 'image' => null, 'is_hot' => 0, 'needs_zone' => 0, 'is_active' => 1, 'sort_order' => 0];
$post = $this->input->method() === 'post';
$chk = function ($f) use ($g, $post) { return $post ? (bool) $this->input->post($f) : (bool) $g[$f]; }; ?>
<a href="<?= site_url('admin/games') ?>" class="text-sm text-slate-500 hover:text-brand">← Game &amp; Produk</a>
<h1 class="mt-2 a-title"><?= $isnew ? 'Tambah game' : 'Edit ' . html_escape($game['name']) ?></h1>
<?php if ($error): ?><div class="mt-4 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"><?= html_escape($error) ?></div><?php endif; ?>
<?php $err = validation_errors('<li>', '</li>'); if ($err): ?><ul class="mt-4 list-disc rounded-xl bg-rose-50 p-3 pl-7 text-sm font-semibold text-rose-700"><?= $err ?></ul><?php endif; ?>

<?= form_open_multipart($isnew ? 'admin/games/create' : 'admin/games/edit/' . $game['id'], ['class' => 'mt-5 grid max-w-3xl gap-4 a-card p-5 md:grid-cols-2']) ?>
  <label class="md:col-span-2 text-sm font-semibold">Nama game<input class="field mt-1" name="name" value="<?= set_value('name', $g['name']) ?>" maxlength="120" required></label>
  <label class="text-sm font-semibold">Slug (URL)
    <?php if ($isnew): ?><input class="field mt-1" name="slug" value="<?= set_value('slug') ?>" placeholder="otomatis dari nama" maxlength="60">
    <?php else: ?><input class="field mt-1 bg-slate-100" value="<?= html_escape($g['slug']) ?>" disabled><?php endif; ?></label>
  <label class="text-sm font-semibold">Kategori<input class="field mt-1" name="category" list="cats" value="<?= set_value('category', $g['category']) ?>" maxlength="40" required>
    <datalist id="cats"><?php foreach ($categories as $c): ?><option value="<?= html_escape($c) ?>"><?php endforeach; ?></datalist></label>
  <label class="text-sm font-semibold">Nama item (Diamond, UC, …)<input class="field mt-1" name="currency" value="<?= set_value('currency', $g['currency']) ?>" maxlength="60" required></label>
  <label class="text-sm font-semibold">Urutan tampil<input class="field mt-1" type="number" min="0" name="sort_order" value="<?= set_value('sort_order', $g['sort_order']) ?>"></label>
  <label class="text-sm font-semibold">Warna cadangan<input class="mt-1 block h-11 w-24 cursor-pointer rounded-lg border border-slate-300" type="color" name="color" value="<?= set_value('color', $g['color']) ?>"></label>

  <div class="text-sm font-semibold">Gambar game
    <?php if (!empty($g['image'])): ?><div class="mt-1 flex items-center gap-3"><?= game_thumb($g, 'h-16 w-16 rounded-xl') ?>
      <label class="text-xs font-normal"><input type="checkbox" name="remove_image" value="1"> hapus gambar</label></div><?php endif; ?>
    <input class="mt-1 block w-full text-sm" type="file" name="image" accept="image/png,image/jpeg,image/webp">
    <span class="text-xs font-normal text-slate-500">JPG/PNG/WebP, maks 2 MB. Disarankan persegi (mis. 512×512); otomatis dikecilkan ke 640px.</span></div>

  <div class="md:col-span-2 flex flex-wrap gap-5 text-sm font-semibold">
    <label><input type="checkbox" name="is_active" value="1" <?= $chk('is_active') ? 'checked' : '' ?>> Aktif (tampil di situs)</label>
    <label><input type="checkbox" name="is_hot" value="1" <?= $chk('is_hot') ? 'checked' : '' ?>> Populer 🔥</label>
    <label><input type="checkbox" name="needs_zone" value="1" <?= $chk('needs_zone') ? 'checked' : '' ?>> Butuh Zone/Server ID</label>
  </div>
  <div class="md:col-span-2"><button class="a-btn"><?= $isnew ? 'Buat game' : 'Simpan' ?></button></div>
</form>

<?php if (!$isnew): ?>
<h2 class="mt-10 font-display text-lg font-extrabold">Nominal produk</h2>
<p class="text-sm text-slate-500">Harga di sini yang dipakai saat checkout. SKU = kode produk di supplier (untuk pengiriman otomatis nanti).</p>
<div class="mt-3 max-w-4xl space-y-2">
  <div class="hidden grid-cols-12 gap-2 px-1 text-xs font-semibold uppercase text-slate-500 md:grid"><div class="col-span-2">Jumlah</div><div class="col-span-2">Harga (Rp)</div><div class="col-span-3">SKU</div><div class="col-span-1">Urut</div><div class="col-span-1">Aktif</div><div class="col-span-3"></div></div>
  <?php foreach ($packs as $p): ?>
    <div class="a-card !rounded-xl p-2">
      <?= form_open('admin/games/pack/' . $game['id'] . '/' . $p['id'], ['class' => 'grid grid-cols-6 items-center gap-2 md:grid-cols-12']) ?>
        <input class="field col-span-3 md:col-span-2" name="qty" value="<?= html_escape($p['qty']) ?>" maxlength="20" aria-label="Jumlah">
        <input class="field col-span-3 md:col-span-2" type="number" min="1" name="price" value="<?= (int) $p['price'] ?>" aria-label="Harga">
        <input class="field col-span-3 md:col-span-3" name="sku" value="<?= html_escape($p['sku']) ?>" maxlength="60" placeholder="SKU" aria-label="SKU">
        <input class="field col-span-1 md:col-span-1" type="number" min="0" name="sort_order" value="<?= (int) $p['sort_order'] ?>" aria-label="Urutan">
        <label class="col-span-2 text-center text-sm md:col-span-1"><input type="checkbox" name="is_active" value="1" <?= $p['is_active'] ? 'checked' : '' ?>></label>
        <div class="col-span-6 flex gap-2 md:col-span-3 md:justify-end">
          <button class="rounded-lg bg-brand px-3 py-2 text-xs font-bold text-white">Simpan</button>
          <button type="submit" formaction="<?= site_url('admin/games/pack_delete/' . $game['id'] . '/' . $p['id']) ?>" onclick="return confirm('Hapus nominal ini?')" class="rounded-lg border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button>
        </div>
      </form>
    </div>
  <?php endforeach; ?>
  <div class="rounded-xl border border-dashed border-slate-300 bg-white p-2">
    <?= form_open('admin/games/pack/' . $game['id'] . '/0', ['class' => 'grid grid-cols-6 items-center gap-2 md:grid-cols-12']) ?>
      <input class="field col-span-3 md:col-span-2" name="qty" placeholder="86" maxlength="20" required aria-label="Jumlah">
      <input class="field col-span-3 md:col-span-2" type="number" min="1" name="price" placeholder="20000" required aria-label="Harga">
      <input class="field col-span-3 md:col-span-3" name="sku" maxlength="60" placeholder="SKU (opsional)" aria-label="SKU">
      <input class="field col-span-1" type="number" min="0" name="sort_order" value="<?= count($packs) + 1 ?>" aria-label="Urutan">
      <label class="col-span-2 text-center text-sm md:col-span-1"><input type="checkbox" name="is_active" value="1" checked></label>
      <div class="col-span-6 md:col-span-3 md:text-right"><button class="rounded-lg bg-ink px-3 py-2 text-xs font-bold text-white">+ Tambah nominal</button></div>
    </form>
  </div>
</div>

<h2 class="mt-10 font-display text-lg font-extrabold text-rose-700">Zona berbahaya</h2>
<?= form_open('admin/games/delete/' . $game['id'], ['class' => 'mt-2', 'onsubmit' => "return confirm('Hapus game ini beserta semua nominalnya?')"]) ?>
  <button class="a-btn-danger">Hapus game</button>
  <span class="ml-2 text-xs text-slate-500">Game yang sudah punya pesanan tidak bisa dihapus – nonaktifkan saja.</span>
</form>
<?php endif; ?>
