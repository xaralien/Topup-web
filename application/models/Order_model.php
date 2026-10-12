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
}
