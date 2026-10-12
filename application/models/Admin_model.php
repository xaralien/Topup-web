<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    const MAX_ATTEMPTS = 5;      // gagal login per IP
    const WINDOW_MIN   = 15;     // dalam N menit

    public function find($id)
    {
        return $this->db->get_where('admins', ['id' => (int) $id])->row_array();
    }

    public function find_by_username($u)
    {
        return $this->db->get_where('admins', ['username' => $u])->row_array();
    }

    public function all()
    {
        return $this->db->order_by('id')->get('admins')->result_array();
    }

    public function count()
    {
        return (int) $this->db->count_all('admins');
    }

    public function create($username, $name, $password)
    {
        $this->db->insert('admins', [
            'username' => $username, 'name' => $name,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->db->insert_id();
    }

    public function set_password($id, $password)
    {
        $this->db->where('id', (int) $id)->update('admins', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function delete($id)
    {
        $this->db->delete('admins', ['id' => (int) $id]);
    }

    /** Verifikasi kredensial; selalu menjalankan password_verify agar waktu respons tidak membocorkan username. */
    public function authenticate($username, $password)
    {
        $a = $this->find_by_username($username);
        $hash = $a ? $a['password_hash'] : '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $ok = password_verify($password, $hash);
        if (!$a || !$ok) {
            return null;
        }
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->set_password($a['id'], $password);
        }
        $this->db->where('id', $a['id'])->update('admins', ['last_login_at' => date('Y-m-d H:i:s')]);
        return $a;
    }

    public function is_locked($ip)
    {
        $since = date('Y-m-d H:i:s', time() - self::WINDOW_MIN * 60);
        return $this->db->where('ip', $ip)->where('created_at >=', $since)->count_all_results('login_attempts') >= self::MAX_ATTEMPTS;
    }

    public function record_failure($ip)
    {
        $this->db->insert('login_attempts', ['ip' => $ip, 'created_at' => date('Y-m-d H:i:s')]);
        $this->db->where('created_at <', date('Y-m-d H:i:s', time() - 86400))->delete('login_attempts');
    }

    public function clear_failures($ip)
    {
        $this->db->delete('login_attempts', ['ip' => $ip]);
    }
}
