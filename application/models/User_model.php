<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Akun pelanggan (pembeli). Admin ada di Admin_model. */
class User_model extends CI_Model
{
    public function find($id)
    {
        return $this->db->get_where('users', ['id' => (int) $id])->row_array();
    }

    public function find_by_email($email)
    {
        return $this->db->get_where('users', ['email' => strtolower(trim($email))])->row_array();
    }

    public function create($name, $email, $password)
    {
        $this->db->insert('users', [
            'name' => $name, 'email' => strtolower(trim($email)),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->db->insert_id();
    }

    public function authenticate($email, $password)
    {
        $u = $this->find_by_email($email);
        $hash = $u ? $u['password_hash'] : '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $ok = password_verify($password, $hash);
        if (!$u || !$ok) {
            return null;
        }
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->set_password($u['id'], $password);
        }
        $this->db->where('id', $u['id'])->update('users', ['last_login_at' => date('Y-m-d H:i:s')]);
        return $u;
    }

    public function set_password($id, $password)
    {
        $this->db->where('id', (int) $id)->update('users', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    /** Untuk admin: daftar pelanggan + jumlah & total belanja. */
    public function with_stats($limit = 100)
    {
        return $this->db->query("SELECT u.*, COUNT(o.id) AS orders,
                COALESCE(SUM(CASE WHEN o.status IN ('paid','processing','success') THEN o.amount END), 0) AS spent
            FROM users u LEFT JOIN orders o ON o.user_id = u.id
            GROUP BY u.id ORDER BY u.id DESC LIMIT " . (int) $limit)->result_array();
    }
}
