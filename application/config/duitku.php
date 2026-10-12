<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Duitku. Kredensial dibaca dari environment (.env), JANGAN di-hardcode / di-commit.
| Sandbox : https://sandbox.duitku.com/webapi/api/merchant
| Produksi: https://passport.duitku.com/webapi/api/merchant
*/
$sandbox = getenv('DUITKU_SANDBOX');
$sandbox = ($sandbox === false || $sandbox === '') ? TRUE : in_array(strtolower($sandbox), ['1', 'true', 'yes'], TRUE);

$config['duitku_merchant_code'] = (string) getenv('DUITKU_MERCHANT_CODE');
$config['duitku_api_key']       = (string) getenv('DUITKU_API_KEY');
$config['duitku_base_url']      = getenv('DUITKU_BASE_URL') ?: ($sandbox
    ? 'https://sandbox.duitku.com/webapi/api/merchant'
    : 'https://passport.duitku.com/webapi/api/merchant');

// Opsional: paksa URL callback/return publik (mis. tunnel ngrok saat development).
$config['duitku_public_base']   = rtrim((string) getenv('DUITKU_PUBLIC_BASE'), '/');

$config['duitku_expiry_minutes'] = 60;

/*
| Metode bayar yang ditawarkan di UI (kode => nama). Pastikan metode tsb AKTIF di project Duitku Anda;
| daftar resmi per project bisa diambil lewat endpoint getpaymentmethod.
*/
$config['payment_methods'] = [
    'NQ' => 'QRIS',
    'DA' => 'DANA',
    'OV' => 'OVO',
    'SA' => 'ShopeePay',
    'BC' => 'BCA VA',
    'M2' => 'Mandiri VA',
    'I1' => 'BNI VA',
    'BR' => 'BRI VA',
];

// Pemenuhan pesanan setelah dibayar: 'manual' (default) atau driver supplier buatan sendiri.
$config['fulfillment_driver'] = getenv('FULFILLMENT_DRIVER') ?: 'manual';
