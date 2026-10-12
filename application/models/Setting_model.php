<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengaturan toko di database (key/value). Nilai rahasia (API key) dienkripsi AES-256-GCM dengan
 * kunci acak di application/config/app.key (dibuat otomatis, ditolak webserver oleh application/.htaccess).
 * Urutan prioritas pemakai: database -> config/.env (fallback).
 */
class Setting_model extends CI_Model
{
    private $rows = null;

    private function load()
    {
        if ($this->rows !== null) {
            return;
        }
        $this->rows = [];
        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;                 // tabel belum ada (belum migrasi) -> jangan crash
        $q = $this->db->get('settings');
        $this->db->db_debug = $debug;
        if ($q) {
            foreach ($q->result_array() as $r) {
                $this->rows[$r['k']] = $r;
            }
        }
    }

    public function available()
    {
        $this->load();
        return $this->db->table_exists('settings');
    }

    public function get($key, $default = '')
    {
        $this->load();
        if (!isset($this->rows[$key]) || $this->rows[$key]['v'] === null || $this->rows[$key]['v'] === '') {
            return $default;
        }
        $r = $this->rows[$key];
        if ((int) $r['is_secret'] === 1) {
            $plain = $this->decrypt($r['v']);
            return $plain === null ? $default : $plain;
        }
        return $r['v'];
    }

    public function has($key)
    {
        return $this->get($key, '') !== '';
    }

    public function set($key, $value, $secret = FALSE)
    {
        $this->load();
        $stored = $secret && $value !== '' ? $this->encrypt((string) $value) : (string) $value;
        $this->db->replace('settings', ['k' => $key, 'v' => $stored, 'is_secret' => $secret ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')]);
        $this->rows[$key] = ['k' => $key, 'v' => $stored, 'is_secret' => $secret ? 1 : 0];
    }

    /** "••••••••abcd" – tampilan aman untuk nilai rahasia. */
    public function mask($key)
    {
        $v = (string) $this->get($key, '');
        return $v === '' ? '' : str_repeat('•', 8) . substr($v, -4);
    }

    // ---- helper tingkat tinggi ------------------------------------------------------------

    public function store_name()
    {
        return $this->get('store_name', '') ?: 'MYTopup';
    }

    public function wa_number()
    {
        return wa_normalize($this->get('wa_number', ''));
    }

    /** Tautan wa.me ke CS toko ('' bila nomor belum diisi). */
    public function wa_link($text = '')
    {
        return wa_url($this->wa_number(), $text);
    }

    /** Katalog metode bayar: hasil "Tes koneksi" (nama resmi dari Duitku) atau daftar bawaan config. */
    public function method_catalog()
    {
        $cache = json_decode((string) $this->get('duitku_methods_cache', ''), TRUE);
        if (is_array($cache) && $cache) {
            return $cache;
        }
        $this->config->load('duitku', TRUE);
        return $this->config->item('payment_methods', 'duitku');
    }

    /** Metode bayar yang ditawarkan ke pembeli: pilihan admin (urut katalog); belum dipilih = semua katalog. */
    public function enabled_methods()
    {
        $catalog = $this->method_catalog();
        $on = json_decode((string) $this->get('payment_methods_enabled', ''), TRUE);
        if (!is_array($on)) {
            return $catalog;
        }
        return array_intersect_key($catalog, array_flip($on));
    }

    // ---- enkripsi -------------------------------------------------------------------------

    private function key()
    {
        $file = APPPATH . 'config/app.key';
        if (!is_file($file)) {
            if (@file_put_contents($file, bin2hex(random_bytes(32)), LOCK_EX) === FALSE) {
                throw new RuntimeException('Tidak bisa membuat application/config/app.key (folder tidak bisa ditulis).');
            }
            @chmod($file, 0600);
        }
        return hash('sha256', trim((string) file_get_contents($file)), TRUE);
    }

    private function encrypt($plain)
    {
        $iv = random_bytes(12);
        $ct = openssl_encrypt($plain, 'aes-256-gcm', $this->key(), OPENSSL_RAW_DATA, $iv, $tag);
        return 'v1:' . base64_encode($iv . $tag . $ct);
    }

    private function decrypt($stored)
    {
        if (strpos($stored, 'v1:') !== 0) {
            return null;
        }
        $raw = base64_decode(substr($stored, 3), TRUE);
        if ($raw === FALSE || strlen($raw) < 29) {
            return null;
        }
        $pt = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $this->key(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
        return $pt === FALSE ? null : $pt;
    }
}
