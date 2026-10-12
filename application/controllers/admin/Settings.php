<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Admin > Pengaturan: toko, Duitku, WhatsApp. Disimpan di database; API key dienkripsi. */
class Settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation', 'duitku']);
        $this->form_validation->set_message([
            'required' => '{field} wajib diisi.', 'max_length' => '{field} terlalu panjang.', 'min_length' => '{field} terlalu pendek.',
            'regex_match' => '{field} tidak valid.', 'in_list' => '{field} tidak valid.', 'is_natural_no_zero' => '{field} harus angka > 0.',
            'less_than_equal_to' => '{field} terlalu besar.',
        ]);
    }

    public function index()
    {
        $s = $this->setting_model;
        $catalog = $s->method_catalog();
        $on = json_decode((string) $s->get('payment_methods_enabled', ''), TRUE);
        $this->render_admin('settings', [
            'title'      => 'Pengaturan',
            'available'  => $s->available(),
            'v'          => [
                'store_name' => $s->get('store_name', ''), 'wa_number' => $s->get('wa_number', ''),
                'wa_greeting' => $s->get('wa_greeting', ''), 'wa_float' => $s->get('wa_float', '1'),
                'wa_order_template' => $s->get('wa_order_template', ''),
                'duitku_merchant_code' => $s->get('duitku_merchant_code', ''), 'duitku_mode' => $s->get('duitku_mode', 'sandbox') ?: 'sandbox',
                'duitku_expiry' => $s->get('duitku_expiry', '60') ?: '60',
            ],
            'key_mask'   => $s->mask('duitku_api_key'),
            'env_duitku' => !$s->has('duitku_merchant_code') && $this->duitku->configured(),
            'catalog'    => $catalog,
            'enabled'    => is_array($on) ? $on : array_keys($catalog),
            'last_test'  => $s->get('duitku_last_test', ''),
            'callback'   => public_url('payment/callback'),
            'is_local'   => (bool) preg_match('#^https?://(localhost|127\.|192\.168\.|10\.)#i', public_url('payment/callback')),
            'mode_now'   => $this->duitku->mode_label(),
            'configured' => $this->duitku->configured(),
        ]);
    }

    /** POST – simpan semua; tombol "Simpan & tes koneksi" (action=test) langsung menguji Duitku. */
    public function save()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/settings');
        }
        $s = $this->setting_model;
        if (!$s->available()) {
            $this->flash('Tabel settings belum ada. Jalankan database/migrations/004_settings.sql.', TRUE);
            redirect('admin/settings');
        }
        $this->form_validation->set_rules('store_name', 'Nama toko', 'trim|max_length[40]');
        $this->form_validation->set_rules('duitku_merchant_code', 'Merchant Code', 'trim|max_length[30]|regex_match[/^([A-Za-z0-9_\-]{2,30})?$/]');
        $this->form_validation->set_rules('duitku_api_key', 'API Key', 'trim|max_length[128]');
        $this->form_validation->set_rules('duitku_mode', 'Mode', 'required|in_list[sandbox,production]');
        $this->form_validation->set_rules('duitku_expiry', 'Batas bayar (menit)', 'required|is_natural_no_zero|less_than_equal_to[1440]');
        $this->form_validation->set_rules('wa_number', 'Nomor WhatsApp', ['trim', ['valid_wa', function ($v) { return $v === '' || wa_normalize($v) !== ''; }]],
            ['valid_wa' => '{field} tidak valid (contoh 081234567890).']);
        $this->form_validation->set_rules('wa_greeting', 'Pesan sapaan', 'trim|max_length[300]');
        $this->form_validation->set_rules('wa_order_template', 'Template pesan ke pelanggan', 'trim|max_length[500]');
        if (!$this->form_validation->run()) {
            $this->flash(trim(strip_tags(str_replace('</p>', ' ', validation_errors()))), TRUE);
            redirect('admin/settings');
        }
        $p = function ($k) { return (string) $this->input->post($k, TRUE); };

        $s->set('store_name', $p('store_name'));
        $s->set('duitku_merchant_code', $p('duitku_merchant_code'));
        if ($p('duitku_api_key') !== '') {                       // kosong = jangan ubah yang tersimpan
            $s->set('duitku_api_key', $p('duitku_api_key'), TRUE);
        }
        $s->set('duitku_mode', $p('duitku_mode'));
        $s->set('duitku_expiry', $p('duitku_expiry'));
        $s->set('wa_number', wa_normalize($p('wa_number')));
        $s->set('wa_greeting', $p('wa_greeting'));
        $s->set('wa_order_template', $p('wa_order_template'));
        $s->set('wa_float', $this->input->post('wa_float') ? '1' : '0');

        $catalog = $s->method_catalog();
        $chosen = array_values(array_intersect(array_keys($catalog), (array) $this->input->post('methods')));
        $s->set('payment_methods_enabled', json_encode($chosen));

        if ($this->input->post('action') === 'test') {
            $this->test_now();
        } else {
            $this->flash('Pengaturan disimpan.');
        }
        redirect('admin/settings');
    }

    /** POST – hanya tes koneksi dengan pengaturan tersimpan. */
    public function test()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/settings');
        }
        $this->test_now();
        redirect('admin/settings');
    }

    private function test_now()
    {
        $s = $this->setting_model;
        $this->duitku = null; $this->load->library('duitku');   // muat ulang dengan nilai terbaru
        if (!$this->duitku->configured()) {
            $s->set('duitku_last_test', 'Gagal ' . date('d/m H:i') . ' – Merchant Code / API Key belum diisi');
            $this->flash('Isi Merchant Code dan API Key dulu.', TRUE);
            return;
        }
        $r = $this->duitku->payment_methods(10000);
        if ($r['ok']) {
            $s->set('duitku_methods_cache', json_encode($r['methods']));
            // metode yang sebelumnya dipilih tapi tak ada di akun Duitku dibuang; yang baru dibiarkan nonaktif
            $on = json_decode((string) $s->get('payment_methods_enabled', ''), TRUE);
            $s->set('payment_methods_enabled', json_encode(array_values(array_intersect(is_array($on) ? $on : array_keys($r['methods']), array_keys($r['methods'])))));
            $s->set('duitku_last_test', 'OK ' . date('d/m H:i') . ' – ' . count($r['methods']) . ' metode tersedia (' . $this->duitku->mode_label() . ')');
            $this->flash('Terhubung ke Duitku (' . $this->duitku->mode_label() . '). ' . count($r['methods']) . ' metode pembayaran ditemukan – pilih yang ingin ditampilkan lalu simpan.');
        } else {
            $s->set('duitku_last_test', 'Gagal ' . date('d/m H:i') . ' – ' . $r['error']);
            $this->flash('Koneksi Duitku gagal: ' . $r['error'] . '. Periksa Merchant Code, API Key, dan mode (sandbox/production).', TRUE);
        }
    }
}
