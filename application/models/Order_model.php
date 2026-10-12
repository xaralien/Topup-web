<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    public function new_order_no()
    {
        return 'MT' . date('ymd') . strtoupper(bin2hex(random_bytes(5)));
    }

    public function create(array $d)
    {
        $now = date('Y-m-d H:i:s');
        $this->db->insert('orders', $d + ['status' => 'pending', 'created_at' => $now, 'updated_at' => $now]);
        return $this->db->insert_id();
    }

    public function find($order_no)
    {
        if (!preg_match('/^MT[0-9A-Z]{10,20}$/', (string) $order_no)) {
            return null;
        }
        $o = $this->db->get_where('orders', ['order_no' => $order_no])->row_array();
        if ($o && $o['status'] === 'pending' && strtotime($o['expires_at']) < time()) {
            $this->transition($order_no, ['pending'], 'expired');
            $o['status'] = 'expired';
        }
        return $o ?: null;
    }

    /** Ubah status hanya bila status saat ini ada di $from (atomik, aman dari balapan callback vs polling). */
    public function transition($order_no, array $from, $to, array $extra = [])
    {
        $this->db->where('order_no', $order_no)->where_in('status', $from)
                 ->update('orders', $extra + ['status' => $to, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->db->affected_rows() > 0;
    }

    /** pending/expired -> paid. TRUE hanya untuk pemanggil pertama (idempoten). */
    public function mark_paid($order_no, $reference = null)
    {
        $extra = ['paid_at' => date('Y-m-d H:i:s')];
        if ($reference) {
            $extra['duitku_ref'] = $reference;
        }
        return $this->transition($order_no, ['pending', 'expired'], 'paid', $extra);
    }

    public function touch_checked($order_no)
    {
        $this->db->where('order_no', $order_no)->update('orders', ['last_checked_at' => date('Y-m-d H:i:s')]);
    }

    public function expire_stale()
    {
        $this->db->where('status', 'pending')->where('expires_at <', date('Y-m-d H:i:s'))
                 ->update('orders', ['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->db->affected_rows();
    }

    public function log($order_no, $type, $payload)
    {
        $this->db->insert('payment_logs', [
            'order_no' => $order_no, 'type' => $type,
            'payload' => is_string($payload) ? $payload : json_encode($payload),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /** Daftar pesanan untuk admin (filter status & kata kunci). Mengembalikan [rows, total]. */
    public function search($status = '', $q = '', $limit = 25, $offset = 0)
    {
        $apply = function () use ($status, $q) {
            if ($status !== '') {
                $this->db->where('status', $status);
            }
            if ($q !== '') {
                $this->db->group_start()->like('order_no', $q)->or_like('email', $q)->or_like('target_user_id', $q)->group_end();
            }
        };
        $apply();
        $total = $this->db->count_all_results('orders');
        $apply();
        $rows = $this->db->order_by('id', 'DESC')->limit((int) $limit, (int) $offset)->get('orders')->result_array();
        return [$rows, $total];
    }

    public function logs($order_no)
    {
        return $this->db->order_by('id', 'DESC')->limit(50)->get_where('payment_logs', ['order_no' => $order_no])->result_array();
    }

    public function stats()
    {
        $today = date('Y-m-d');
        $paid = "status IN ('paid','processing','success')";
        $r = $this->db->query("SELECT
                SUM(DATE(created_at) = ?) AS orders_today,
                COALESCE(SUM(CASE WHEN $paid AND DATE(paid_at) = ? THEN amount END), 0) AS revenue_today,
                COALESCE(SUM(CASE WHEN $paid AND paid_at >= ? THEN amount END), 0) AS revenue_30d,
                SUM(status = 'pending') AS pending, SUM(status IN ('paid','processing')) AS to_fulfill
            FROM orders", [$today, $today, date('Y-m-d H:i:s', time() - 30 * 86400)])->row_array();
        return array_map('intval', array_map(function ($v) { return $v === null ? 0 : $v; }, $r));
    }

    public function for_user($user_id, $limit = 50)
    {
        return $this->db->order_by('id', 'DESC')->limit((int) $limit)->get_where('orders', ['user_id' => (int) $user_id])->result_array();
    }

    /** Grafik: pendapatan per hari selama $days hari terakhir (hari tanpa transaksi = 0). */
    public function daily_revenue($days = 14)
    {
        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $q = $this->db->query("SELECT DATE(paid_at) d, SUM(amount) s, COUNT(*) n FROM orders
            WHERE status IN ('paid','processing','success') AND paid_at >= ? GROUP BY DATE(paid_at)", [$from . ' 00:00:00'])->result_array();
        $by = [];
        foreach ($q as $r) { $by[$r['d']] = ['sum' => (int) $r['s'], 'n' => (int) $r['n']]; }
        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $d = date('Y-m-d', strtotime($from . " +$i days"));
            $out[] = ['date' => $d] + (isset($by[$d]) ? $by[$d] : ['sum' => 0, 'n' => 0]);
        }
        return $out;
    }

    public function status_counts()
    {
        $r = $this->db->query('SELECT status, COUNT(*) n FROM orders GROUP BY status')->result_array();
        return array_column($r, 'n', 'status');
    }
}
