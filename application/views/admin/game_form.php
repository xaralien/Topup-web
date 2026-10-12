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
    <input class="mt-1 block w-full text-sm" type="file" name="image" accept="image/png,image/jpeg,image/webp" data-pv-file>
    <span class="text-xs font-normal text-slate-500">JPG/PNG/WebP, maks 2 MB. Disarankan persegi (mis. 512×512); otomatis dikecilkan ke 640px.</span>
    <p class="mt-1 hidden text-xs font-semibold" data-pv-msg></p></div>

  <?php $minprice = $packs ? min(array_column($packs, 'price')) : 20000; ?>
  <!-- Pratinjau tampilan di situs (diperbarui langsung saat gambar/nama/warna berubah) -->
  <section class="md:col-span-2 rounded-2xl border border-dashed border-indigo-200 bg-brand-soft/40 p-4" data-pv
           data-img="<?= html_escape(game_image_url($g)) ?>" data-min="<?= (int) $minprice ?>">
    <div class="flex items-center justify-between"><h3 class="text-sm font-extrabold">Pratinjau di situs</h3>
      <span class="text-xs font-normal text-slate-500">Halaman <b>Semua Game</b> &amp; hasil pencarian produk</span></div>
    <div class="mt-3 flex flex-wrap items-start gap-6">
      <figure>
        <figcaption class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Desktop</figcaption>
        <div class="game-card w-36 !bg-white">
          <div class="mb-3 h-24 overflow-hidden rounded-xl" data-pv-box><img alt="" class="hidden h-full w-full object-cover" data-pv-img></div>
          <div class="truncate text-sm font-bold" data-pv-name>Nama game</div>
          <div class="text-xs text-slate-500">mulai <span data-pv-price></span></div>
        </div>
      </figure>
      <figure>
        <figcaption class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Mobile</figcaption>
        <div class="game-card w-28 !bg-white p-2">
          <div class="mb-2 h-14 overflow-hidden rounded-lg" data-pv-box><img alt="" class="hidden h-full w-full object-cover" data-pv-img></div>
          <div class="text-[11px] font-bold leading-tight" data-pv-name>Nama game</div>
        </div>
      </figure>
      <figure class="min-w-[15rem] flex-1">
        <figcaption class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Hasil pencarian produk</figcaption>
        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3">
          <span class="h-10 w-10 shrink-0 overflow-hidden rounded-lg" data-pv-box><img alt="" class="hidden h-full w-full object-cover" data-pv-img></span>
          <span class="min-w-0 flex-1"><span class="block truncate text-sm font-bold"><span data-pv-name>Nama game</span> · 86 <span data-pv-cur>Diamond</span></span>
            <span class="block text-xs text-slate-500" data-pv-cat>Kategori</span></span>
          <span class="text-sm font-bold text-brand" data-pv-price></span>
        </div>
      </figure>
    </div>
    <p class="mt-3 text-xs text-slate-500" data-pv-note>Gambar dipotong ke tengah agar memenuhi kotak (seperti pratinjau di atas).</p>
  </section>

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

<script>
(function () {
  var pv = document.querySelector('[data-pv]'); if (!pv) return;
  var f = document.querySelector('[data-pv-file]'), msg = document.querySelector('[data-pv-msg]');
  var name = document.querySelector('[name="name"]'), cat = document.querySelector('[name="category"]'),
      cur = document.querySelector('[name="currency"]'), color = document.querySelector('[name="color"]'),
      rm = document.querySelector('[name="remove_image"]');
  var url = null, saved = pv.dataset.img || '', min = +pv.dataset.min || 0;
  var boxes = pv.querySelectorAll('[data-pv-box]'), imgs = pv.querySelectorAll('[data-pv-img]');
  var rp = function (n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); };

  function text(sel, v) { pv.querySelectorAll(sel).forEach(function (e) { e.textContent = v; }); }
  function paint() {
    var src = (rm && rm.checked) ? '' : (url || saved);
    var c = /^#[0-9a-f]{6}$/i.test(color.value) ? color.value : '#E2E8F0';
    boxes.forEach(function (b) { b.style.background = c; });
    imgs.forEach(function (i) { if (src) { i.src = src; i.classList.remove('hidden'); } else { i.removeAttribute('src'); i.classList.add('hidden'); } });
    text('[data-pv-name]', name.value.trim() || 'Nama game');
    text('[data-pv-cat]', cat.value.trim() || 'Kategori');
    text('[data-pv-cur]', cur.value.trim() || 'Diamond');
    text('[data-pv-price]', rp(min));
  }
  function say(t, bad) { msg.textContent = t; msg.className = 'mt-1 text-xs font-semibold ' + (bad ? 'text-rose-600' : 'text-emerald-700'); if (!t) msg.classList.add('hidden'); }

  f.addEventListener('change', function () {
    if (url) { URL.revokeObjectURL(url); url = null; }
    var file = f.files && f.files[0];
    if (!file) { say(''); paint(); return; }
    if (['image/png', 'image/jpeg', 'image/webp'].indexOf(file.type) === -1) { f.value = ''; say('Format harus JPG, PNG, atau WebP.', true); paint(); return; }
    if (file.size > 2 * 1024 * 1024) { f.value = ''; say('Ukuran ' + (file.size / 1048576).toFixed(1) + ' MB melebihi batas 2 MB.', true); paint(); return; }
    url = URL.createObjectURL(file);
    if (rm) rm.checked = false;
    var im = new Image();
    im.onload = function () {
      var r = im.width / im.height, sq = Math.abs(r - 1) < 0.06;
      say(file.name + ' · ' + im.width + '×' + im.height + 'px · ' + Math.round(file.size / 1024) + ' KB' + (sq ? '' : ' — tidak persegi, bagian tepi akan terpotong'), false);
      if (!sq) msg.className = 'mt-1 text-xs font-semibold text-amber-600';
    };
    im.onerror = function () { f.value = ''; URL.revokeObjectURL(url); url = null; say('File tidak bisa dibaca sebagai gambar.', true); paint(); };
    im.src = url;
    paint();
  });
  [name, cat, cur, color].forEach(function (e) { e && e.addEventListener('input', paint); });
  if (rm) rm.addEventListener('change', function () {
    if (rm.checked) { f.value = ''; if (url) { URL.revokeObjectURL(url); url = null; } say(''); }  // hapus = batalkan file baru juga
    paint();
  });
  paint();
})();
</script>
