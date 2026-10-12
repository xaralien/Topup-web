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
            return '<img src="' . html_escape(game_image_url($g)) . '" alt="' . html_escape($g['name']) . '" loading="lazy" decoding="async" class="' . $w . 'object-cover ' . html_escape($class) . '" style="background:' . $bg . '">';
        }
        return '<div class="' . html_escape($class) . '" style="background:' . $bg . '"></div>';
    }
}

