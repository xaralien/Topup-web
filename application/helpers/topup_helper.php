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
