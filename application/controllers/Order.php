<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['game_model', 'order_model']);
        $this->load->library(['form_validation', 'duitku', 'fulfillment']);
    }

    /** /order/create|check|status/<no> ditangani method biasa; /order/<no> = detail pesanan (tanpa routes.php). */
    public function _remap($method, $params = [])
    {
        if (in_array($method, ['create', 'check', 'status'], TRUE)) {
            return call_user_func_array([$this, $method], $params);
        }
        return $this->view($method);
    }

    /** POST /order/create */
    public function create()
    {
        if ($this->input->method() !== 'post') {
            redirect();
        }
        $methods = $this->config->item('payment_methods', 'duitku');
        $game = $this->game_model->find((string) $this->input->post('game', TRUE));
        if (!$game) {
            show_404();
        }

        $this->form_validation->set_rules('user_id', 'ID akun', 'required|trim|regex_match[/^[0-9A-Za-z_.\-]{3,30}$/]');
        if ($game['needs_zone']) {
            $this->form_validation->set_rules('zone_id', 'Zone/Server ID', 'required|trim|regex_match[/^[0-9]{1,10}$/]');
        }
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[160]');
        // regex memuat '|' sehingga tak bisa ditulis sebagai string aturan CI -> pakai callable
        $this->form_validation->set_rules('whatsapp', 'WhatsApp', ['trim', ['valid_wa', function ($v) {
            return $v === '' || $v === null || (bool) preg_match('/^(\+?62|0)8[0-9]{7,13}$/', $v);
        }]], ['valid_wa' => '{field} tidak valid (contoh 081234567890).']);
        $this->form_validation->set_rules('pack_id', 'Nominal', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('payment_method', 'Metode pembayaran', 'required|in_list[' . implode(',', array_keys($methods)) . ']');
        $this->form_validation->set_message([
            'required' => '{field} wajib diisi.', 'valid_email' => '{field} tidak valid.', 'regex_match' => '{field} tidak valid.',
            'max_length' => '{field} terlalu panjang.', 'in_list' => 'Pilih {field}.', 'is_natural_no_zero' => 'Pilih {field}.',
        ]);

        $valid = $this->form_validation->run();
        $pack  = $valid ? $this->game_model->find_pack($game['id'], $this->input->post('pack_id')) : null;
        if (!$valid || !$pack) {
            return $this->show_form($game, $methods, ($valid && !$pack) ? 'Nominal tidak tersedia.' : null);
        }

        if (!$this->duitku->configured()) {
            return $this->show_form($game, $methods, 'Pembayaran belum dikonfigurasi (DUITKU_MERCHANT_CODE / DUITKU_API_KEY).');
        }

        $order_no = $this->order_model->new_order_no();
        $label    = $pack['qty'] . ' ' . $game['currency'];
        $expiry   = (int) $this->config->item('duitku_expiry_minutes', 'duitku');
        $user_id  = $this->input->post('user_id', TRUE);
        $zone_id  = $game['needs_zone'] ? $this->input->post('zone_id', TRUE) : null;
        $wa       = $this->input->post('whatsapp', TRUE);
        $code     = $this->input->post('payment_method', TRUE);

        $res = $this->duitku->inquiry([
            'order_no' => $order_no, 'amount' => (int) $pack['price'], 'method' => $code,
            'product'  => $game['name'] . ' ' . $label, 'email' => $this->input->post('email', TRUE),
            'name'     => 'MYTopup', 'phone' => $wa, 'expiry' => $expiry,
            'callback_url' => public_url('payment/callback'), 'return_url' => public_url('order/' . $order_no),
        ]);
        $this->order_model->log($order_no, 'inquiry', ['ok' => $res['ok'], 'http' => $res['http'], 'data' => $res['data'], 'error' => $res['error']]);

        if (!$res['ok']) {
            log_message('error', 'Duitku inquiry gagal: ' . $res['error']);
            return $this->show_form($game, $methods, 'Gagal membuat pembayaran. Coba metode lain atau ulangi sebentar lagi.');
        }
        $d = $res['data'];
        $this->order_model->create([
            'order_no' => $order_no, 'user_id' => $this->current_user ? (int) $this->current_user['id'] : null, 'game_id' => $game['id'], 'pack_id' => $pack['id'],
            'game_name' => $game['name'], 'item_label' => $label, 'sku' => $pack['sku'],
            'target_user_id' => $user_id, 'target_zone_id' => $zone_id,
            'email' => $this->input->post('email', TRUE), 'whatsapp' => $wa ?: null,
            'amount' => (int) $pack['price'], 'payment_method' => $code, 'payment_name' => $methods[$code],
            'duitku_ref' => isset($d['reference']) ? $d['reference'] : null,
            'payment_url' => $d['paymentUrl'],
            'va_number' => isset($d['vaNumber']) && $d['vaNumber'] !== '' ? $d['vaNumber'] : null,
            'qr_string' => isset($d['qrString']) && $d['qrString'] !== '' ? $d['qrString'] : null,
            'expires_at' => date('Y-m-d H:i:s', time() + $expiry * 60),
        ]);
        redirect('order/' . $order_no);
    }

    /** GET /order/{no} */
    public function view($order_no = '')
    {
        $order = $this->reconcile($this->order_model->find($order_no));
        if (!$order) {
            show_404();
        }
        $this->render('order', ['title' => 'Pesanan ' . $order['order_no'], 'order' => $order]);
    }

    /** GET /order/status/{no} – dipoll JS tiap beberapa detik. */
    public function status($order_no = '')
    {
        $order = $this->reconcile($this->order_model->find($order_no));
        if (!$order) {
            $this->output->set_status_header(404);
            return $this->output->set_content_type('application/json')->set_output('{"error":"not_found"}');
        }
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => $order['status'], 'label' => order_status_info($order['status'])[0],
        ]));
    }

    /** GET /order/check?no= – form "Cek Pesanan". */
    public function check()
    {
        $no = strtoupper(trim((string) $this->input->get('no', TRUE)));
        $error = null;
        if ($no !== '') {
            if ($this->order_model->find($no)) {
                redirect('order/' . $no);
            }
            $error = 'Nomor pesanan tidak ditemukan.';
        }
        $this->render('order_check', ['title' => 'Cek Pesanan', 'no' => $no, 'error' => $error]);
    }

    /**
     * Rekonsiliasi: callback bisa terlambat / tak sampai (mis. localhost), jadi saat pesanan masih
     * pending kita tanya langsung ke Duitku (dibatasi 1x per 8 detik).
     */
    private function reconcile($order)
    {
        if (!$order || $order['status'] !== 'pending' || !$this->duitku->configured()) {
            return $order;
        }
        if ($order['last_checked_at'] && time() - strtotime($order['last_checked_at']) < 8) {
            return $order;
        }
        $this->order_model->touch_checked($order['order_no']);
        $r = $this->duitku->status($order['order_no']);
        $this->order_model->log($order['order_no'], 'status_check', ['ok' => $r['ok'], 'data' => $r['data']]);
        if (!$r['ok'] || !isset($r['data']['statusCode'])) {
            return $order;
        }
        $code = $r['data']['statusCode'];
        if ($code === '00' && (int) round((float) $r['data']['amount']) === (int) $order['amount']) {
            if ($this->order_model->mark_paid($order['order_no'], isset($r['data']['reference']) ? $r['data']['reference'] : null)) {
                $this->fulfillment->process($this->order_model->find($order['order_no']));
            }
        } elseif ($code === '02') {
            $this->order_model->transition($order['order_no'], ['pending'], 'failed');
        }
        return $this->order_model->find($order['order_no']);
    }

    private function show_form($game, $methods, $global_error = null)
    {
        $this->render('game', [
            'title' => 'Top Up ' . $game['name'], 'game' => $game, 'methods' => $methods, 'global_error' => $global_error,
        ]);
    }
}
