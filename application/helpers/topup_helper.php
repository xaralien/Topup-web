<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('order_status_info')) {
    /** [label, kelas warna Tailwind, pesan] untuk tiap status pesanan. */
    function order_status_info($s)
    {
        $map = [
            'pending'    => ['Menunggu pembayaran', 'bg-amber-100 text-amber-800',  'Selesaikan pembayaran sebelum batas waktu.'],
            'paid'       => ['Pembayaran diterima', 'bg-indigo-100 text-indigo-800', 'Pesanan segera diproses.'],
            'processing' => ['Sedang diproses',     'bg-indigo-100 text-indigo-800', 'Pembayaran diterima, item sedang dikirim ke akunmu.'],
            'success'    => ['Berhasil',            'bg-emerald-100 text-emerald-800', 'Item sudah masuk ke akunmu. Selamat bermain!'],
            'failed'     => ['Gagal',               'bg-rose-100 text-rose-800',     'Pesanan gagal. Hubungi CS bila dana sudah terpotong.'],
            'expired'    => ['Kedaluwarsa',         'bg-slate-200 text-slate-700',   'Batas waktu pembayaran habis. Silakan buat pesanan baru.'],
        ];
        return isset($map[$s]) ? $map[$s] : [$s, 'bg-slate-200 text-slate-700', ''];
    }
}

if (!function_exists('public_url')) {
    /** URL absolut untuk Duitku (callback/return); bisa dipaksa lewat DUITKU_PUBLIC_BASE saat development. */
    function public_url($uri)
    {
        $CI =& get_instance();
        $base = $CI->config->item('duitku_public_base', 'duitku');
        if ($base) {
            $path = parse_url(site_url($uri), PHP_URL_PATH);
            return $base . $path;
        }
        return site_url($uri);
    }
}

if (!function_exists('game_image_url')) {
    function game_image_url($g)
    {
        return !empty($g['image']) ? base_url($g['image']) : '';
    }
}

if (!function_exists('game_thumb')) {
    /** Gambar game; bila belum ada, tampilkan blok warna. $class = ukuran/rounded (mis. "h-20 rounded-xl mb-3"). */
    function game_thumb($g, $class = '')
    {
        $bg = preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $g['color']) ? $g['color'] : '#E2E8F0';
        if (!empty($g['image'])) {
            $w = preg_match('/(^|\s)w-/', $class) ? '' : 'w-full ';
            return '<img src="' . html_escape(game_image_url($g)) . '" alt="' . html_escape($g['name']) . '" loading="lazy" decoding="async" class="' . $w . 'object-cover ' . html_escape($class) . '" style="background:' . $bg . '" onerror="this.removeAttribute(\'src\');this.alt=\'\'">';
        }
        return '<div class="' . html_escape($class) . '" style="background:' . $bg . '"></div>';
    }
}

if (!function_exists('icon')) {
    /** Ikon garis minimalis (24x24, stroke). $name: home, receipt, grid, users, shield, logout, external, plus, search, trend, clock, bolt, wallet, check, user. */
    function icon($name, $class = 'h-5 w-5')
    {
        static $p = [
            'home'     => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
            'receipt'  => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/>',
            'grid'     => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/>',
            'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.2A6.5 6.5 0 0 1 21.5 20"/>',
            'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>',
            'shield'   => '<path d="M12 3 4.5 6v5.5c0 4.5 3.2 8 7.5 9.5 4.3-1.5 7.5-5 7.5-9.5V6z"/><path d="m9 12 2 2 4-4"/>',
            'logout'   => '<path d="M14 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/><path d="M10 8l-4 4 4 4M6 12h10"/>',
            'external' => '<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/>',
            'plus'     => '<path d="M12 5v14M5 12h14"/>',
            'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4-4"/>',
            'trend'    => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
            'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'bolt'     => '<path d="M13 3 5 13h6l-1 8 8-10h-6z"/>',
            'wallet'   => '<path d="M4 7a2 2 0 0 1 2-2h12v4"/><path d="M4 7v10a2 2 0 0 0 2 2h13a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1H6a2 2 0 0 1-2-2z"/><circle cx="16.5" cy="13.5" r="1"/>',
            'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
            'chat'     => '<path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.2A8.4 8.4 0 1 1 21 11.5z"/><path d="M8.5 9.5c.3 2.3 2.7 4.7 5 5l1.2-1.2-1.8-1-.8.6a4 4 0 0 1-1.7-1.7l.6-.8-1-1.8z"/>',
        ];
        $d = isset($p[$name]) ? $p[$name] : '';
        return '<svg class="' . html_escape($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
    }
}

if (!function_exists('wa_normalize')) {
    /** Nomor WhatsApp -> format internasional tanpa tanda (08123 / +62 812-3 -> 62812...). '' bila tidak valid. */
    function wa_normalize($num)
    {
        $d = preg_replace('/\D+/', '', (string) $num);
        if ($d === '') {
            return '';
        }
        if (strpos($d, '00') === 0) { $d = substr($d, 2); }
        if (strpos($d, '0') === 0) { $d = '62' . substr($d, 1); }
        elseif (strpos($d, '8') === 0) { $d = '62' . $d; }
        return preg_match('/^62[1-9][0-9]{7,12}$/', $d) ? $d : '';
    }
}

if (!function_exists('wa_url')) {
    function wa_url($number, $text = '')
    {
        $n = wa_normalize($number);
        return $n === '' ? '' : 'https://wa.me/' . $n . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }
}

