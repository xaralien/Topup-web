<?php if (!empty($site['wa_url']) && !empty($site['wa_float'])): ?>
<a href="<?= html_escape($site['wa_url']) ?>" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
   class="fixed z-40 grid h-12 w-12 place-items-center rounded-full bg-[#25D366] text-white shadow-lg shadow-emerald-900/20 transition hover:scale-105 active:scale-95 <?= $mode === 'mobile' ? 'bottom-28 right-4' : 'bottom-6 right-6' ?>"><?= icon('chat', 'h-6 w-6') ?></a>
<?php endif; ?>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
