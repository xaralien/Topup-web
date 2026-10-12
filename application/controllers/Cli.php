<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perintah operator (hanya CLI):
 *   php index.php cli complete MT2610XXXXXXXXXX   # tandai berhasil (item sudah dikirim)
 *   php index.php cli fail MT2610XXXXXXXXXX       # tandai gagal
 *   php index.php cli expire                      # kedaluwarsakan pesanan pending (jalankan via cron)
 *   php index.php cli orders [status]             # daftar pesanan
 */
class Cli extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->model('order_model');
    }

    public function complete($no = '')
    {
        $ok = $this->order_model->transition($no, ['paid', 'processing'], 'success', ['fulfilled_at' => date('Y-m-d H:i:s')]);
        echo $ok ? "OK: $no -> success\n" : "Tidak berubah (status harus paid/processing): $no\n";
    }

    public function fail($no = '')
    {
        $ok = $this->order_model->transition($no, ['paid', 'processing'], 'failed');
        echo $ok ? "OK: $no -> failed\n" : "Tidak berubah: $no\n";
    }

    public function expire()
    {
        echo 'Kedaluwarsa: ' . $this->order_model->expire_stale() . "\n";
    }

    public function orders($status = '')
    {
        $this->db->order_by('id', 'DESC')->limit(50);
        if ($status !== '') {
            $this->db->where('status', $status);
        }
        foreach ($this->db->get('orders')->result_array() as $o) {
            printf("%-20s %-11s %-10s %-22s %s\n", $o['order_no'], $o['status'], $o['amount'], $o['game_name'] . ' ' . $o['item_label'], $o['target_user_id'] . ($o['target_zone_id'] ? '(' . $o['target_zone_id'] . ')' : ''));
        }
    }
}
