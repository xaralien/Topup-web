<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perintah operator (hanya CLI):
 *   php index.php cli complete MT2610XXXXXXXXXX   # tandai berhasil (item sudah dikirim)
 *   php index.php cli fail MT2610XXXXXXXXXX       # tandai gagal
 *   php index.php cli expire                      # kedaluwarsakan pesanan pending (jalankan via cron)
 *   php index.php cli orders [status]             # daftar pesanan
 *   php index.php cli create_admin <username>     # buat akun admin (password diketik interaktif)
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

    public function create_admin($username = '')
    {
        $this->load->model('admin_model');
        if (!preg_match('/^[a-z0-9_.]{3,30}$/', $username)) {
            exit("Username: 3-30 karakter huruf kecil/angka/_/.\n");
        }
        if ($this->admin_model->find_by_username($username)) {
            exit("Username sudah ada.\n");
        }
        echo 'Password (min 10 karakter): ';
        @shell_exec('stty -echo 2>/dev/null');
        $pw = trim((string) fgets(STDIN));
        @shell_exec('stty echo 2>/dev/null');
        echo "\n";
        if (strlen($pw) < 10) {
            exit("Password minimal 10 karakter.\n");
        }
        $this->admin_model->create($username, ucfirst($username), $pw);
        echo "Admin '$username' dibuat. Login di /admin\n";
    }
}
