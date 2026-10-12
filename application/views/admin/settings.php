<header><h1 class="a-title">Pengaturan</h1><p class="a-sub mt-1">Semua diatur dari sini – tanpa mengedit file. Perubahan langsung berlaku.</p></header>

<?php if (!$available): ?>
  <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Tabel <code>settings</code> belum ada. Jalankan <code>database/migrations/004_settings.sql</code> di database Anda, lalu muat ulang halaman ini.</div>
<?php endif; ?>

<?= form_open('admin/settings/save', ['class' => 'mt-6 max-w-3xl space-y-5']) ?>

  <!-- TOKO -->
  <section class="a-card p-5">
    <h2 class="font-display text-base font-extrabold">Toko</h2>
    <label class="mt-3 block text-sm font-semibold">Nama toko
      <input class="field mt-1" name="store_name" maxlength="40" placeholder="MYTopup" value="<?= html_escape($v['store_name']) ?>">
      <span class="text-xs font-normal text-slate-500">Dipakai di judul halaman dan pesan WhatsApp.</span></label>
  </section>

  <!-- DUITKU -->
  <section class="a-card p-5">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <h2 class="font-display text-base font-extrabold">Pembayaran – Duitku</h2>
      <?php if ($configured): ?><span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Terisi · <?= html_escape($mode_now) ?></span>
      <?php else: ?><span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Belum diisi</span><?php endif; ?>
    </div>
    <ol class="mt-2 list-decimal space-y-0.5 pl-5 text-xs text-slate-500">
      <li>Daftar/masuk di <a class="font-semibold text-brand underline" href="https://dashboard.duitku.com" target="_blank" rel="noopener">dashboard.duitku.com</a> dan buat <b>Project</b>.</li>
      <li>Salin <b>Merchant Code</b> dan <b>API Key</b> project itu ke kolom di bawah.</li>
      <li>Daftarkan <b>Callback URL</b> (di bawah) di pengaturan project Duitku.</li>
      <li>Klik <b>Simpan &amp; tes koneksi</b> – daftar metode bayar asli dari akun Anda akan muncul.</li>
    </ol>
    <?php if ($env_duitku): ?><p class="mt-3 rounded-xl bg-indigo-50 p-3 text-xs text-indigo-900">Saat ini memakai kredensial dari file <code>.env</code>. Isi di bawah untuk menggantinya (database diprioritaskan).</p><?php endif; ?>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
      <label class="text-sm font-semibold">Merchant Code
        <input class="field mt-1" name="duitku_merchant_code" maxlength="30" autocomplete="off" placeholder="D1234" value="<?= html_escape($v['duitku_merchant_code']) ?>"></label>
      <label class="text-sm font-semibold">API Key
        <input class="field mt-1" type="password" name="duitku_api_key" maxlength="128" autocomplete="new-password"
               placeholder="<?= $key_mask ? html_escape($key_mask) . ' (tersimpan, kosongkan bila tidak diubah)' : 'tempel API Key' ?>"></label>
      <div class="text-sm font-semibold">Mode
        <div class="mt-2 flex gap-4 font-normal">
          <label><input type="radio" name="duitku_mode" value="sandbox" <?= $v['duitku_mode'] === 'sandbox' ? 'checked' : '' ?>> Sandbox (uji coba)</label>
          <label><input type="radio" name="duitku_mode" value="production" <?= $v['duitku_mode'] === 'production' ? 'checked' : '' ?>> Production (asli)</label>
        </div></div>
      <label class="text-sm font-semibold">Batas waktu bayar (menit)
        <input class="field mt-1" type="number" min="5" max="1440" name="duitku_expiry" value="<?= html_escape($v['duitku_expiry']) ?>"></label>
    </div>

    <div class="mt-4">
      <div class="text-sm font-semibold">Callback URL <span class="font-normal text-slate-500">(isi di dashboard Duitku)</span></div>
      <div class="mt-1 flex items-center gap-2">
        <input class="field !py-2 text-sm bg-slate-50" readonly value="<?= html_escape($callback) ?>" data-copy-src>
        <button type="button" class="a-btn-soft shrink-0" data-copy-btn>Salin</button>
      </div>
      <?php if ($is_local): ?><p class="mt-1.5 text-xs font-semibold text-amber-700">Alamat ini lokal – Duitku tidak bisa menjangkaunya. Pakai domain publik/HTTPS atau tunnel (ngrok/cloudflared) saat uji; status pesanan tetap akan dicek otomatis meski callback tak sampai.</p><?php endif; ?>
    </div>

    <div class="mt-5">
      <div class="flex items-center justify-between"><div class="text-sm font-semibold">Metode pembayaran yang ditampilkan</div>
        <?php if ($last_test): ?><span class="text-xs <?= strpos($last_test, 'OK') === 0 ? 'text-emerald-700' : 'text-rose-600' ?>"><?= html_escape($last_test) ?></span><?php endif; ?></div>
      <div class="mt-2 grid gap-2 sm:grid-cols-2">
        <?php foreach ($catalog as $code => $name): ?>
          <label class="flex items-center gap-2 rounded-xl border border-[var(--line)] bg-white px-3 py-2 text-sm"><input type="checkbox" name="methods[]" value="<?= html_escape($code) ?>" <?= in_array($code, $enabled, true) ? 'checked' : '' ?>>
            <span class="flex-1"><?= html_escape($name) ?></span><span class="text-xs text-slate-400"><?= html_escape($code) ?></span></label>
        <?php endforeach; ?>
      </div>
      <p class="mt-1.5 text-xs text-slate-500">Daftar ini diperbarui dari akun Duitku Anda setiap kali menekan <b>Simpan &amp; tes koneksi</b>.</p>
    </div>
  </section>

  <!-- WHATSAPP -->
  <section class="a-card p-5">
    <h2 class="font-display text-base font-extrabold">WhatsApp</h2>
    <div class="mt-3 grid gap-3 md:grid-cols-2">
      <label class="text-sm font-semibold">Nomor WhatsApp CS
        <input class="field mt-1" name="wa_number" inputmode="tel" maxlength="20" placeholder="081234567890" value="<?= html_escape($v['wa_number']) ?>">
        <span class="text-xs font-normal text-slate-500">Format 08… atau 62… – otomatis diubah ke format internasional.</span></label>
      <div class="text-sm font-semibold">Tombol chat di situs
        <label class="mt-2 flex items-center gap-2 font-normal"><input type="checkbox" name="wa_float" value="1" <?= $v['wa_float'] !== '0' ? 'checked' : '' ?>> Tampilkan tombol WhatsApp melayang</label></div>
      <label class="text-sm font-semibold md:col-span-2">Pesan sapaan (saat pembeli menekan tombol chat)
        <input class="field mt-1" name="wa_greeting" maxlength="300" placeholder="Halo, saya mau tanya soal top up." value="<?= html_escape($v['wa_greeting']) ?>"></label>
      <label class="text-sm font-semibold md:col-span-2">Template pesan ke pelanggan (dari halaman pesanan admin)
        <textarea class="field mt-1" name="wa_order_template" rows="3" maxlength="500" placeholder="Halo kak, pesanan {no} ({item}) untuk ID {id} {status}. Terima kasih sudah belanja di {toko}!"><?= html_escape($v['wa_order_template']) ?></textarea>
        <span class="text-xs font-normal text-slate-500">Isian otomatis: <code>{no}</code> <code>{item}</code> <code>{id}</code> <code>{status}</code> <code>{toko}</code></span></label>
    </div>
    <p class="mt-3 text-xs text-slate-500">Pengiriman otomatis lewat API WhatsApp (mis. Fonnte, Wablas) belum tersedia; saat ini tombol membuka chat WhatsApp dengan pesan terisi.</p>
  </section>

  <div class="flex flex-wrap gap-2">
    <button class="a-btn" name="action" value="save">Simpan</button>
    <button class="a-btn-soft !px-4 !py-2.5" name="action" value="test"><?= icon('bolt', 'h-4 w-4') ?>Simpan &amp; tes koneksi Duitku</button>
  </div>
</form>

<script>
document.querySelectorAll('[data-copy-btn]').forEach(function (b) {
  b.addEventListener('click', function () {
    var src = document.querySelector('[data-copy-src]');
    (navigator.clipboard ? navigator.clipboard.writeText(src.value) : Promise.reject()).then(function () { b.textContent = 'Tersalin ✓'; }).catch(function () { src.select(); b.textContent = 'Tekan Ctrl+C'; });
  });
});
</script>
