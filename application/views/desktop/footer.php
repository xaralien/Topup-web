<footer class="bg-ink text-slate-300 mt-24">
  <div class="mx-auto max-w-6xl px-6 py-12 grid grid-cols-4 gap-8 text-sm">
    <div class="col-span-2">
      <div class="font-display font-extrabold text-xl text-white">MY<span class="text-indigo-400">Topup</span></div>
      <p class="mt-3 max-w-sm text-slate-400">Platform top up game, voucher, dan akun game. Cepat, aman, harga jujur.</p>
    </div>
    <div><div class="font-bold text-white mb-3">Layanan</div><ul class="space-y-2"><li>Top Up Game</li><li>Voucher</li><li>Akun Game</li></ul></div>
    <div><div class="font-bold text-white mb-3">Bantuan</div><ul class="space-y-2"><li><?php $w = $site['wa_url']; echo $w ? '<a class="hover:text-white" target="_blank" rel="noopener" href="' . html_escape($w) . '">WhatsApp CS</a>' : 'WhatsApp 24 jam'; ?></li><li>Syarat &amp; Ketentuan</li><li>Kebijakan Privasi</li></ul></div>
  </div>
  <div class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">© <?= date('Y') ?> <?= html_escape($site['name']) ?>. Semua hak dilindungi.</div>
</footer>
