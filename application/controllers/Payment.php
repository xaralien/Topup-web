<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Webhook Duitku (tanpa CSRF – lihat csrf_exclude_uris). */
class Payment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->library(['duitku', 'fulfillment']);
    }

    public function callback()
    {
        if ($this->input->method() !== 'post') {
            return $this->reply(405, 'Method Not Allowed');
        }
        $p = $this->input->post(NULL, FALSE);
        $no = isset($p['merchantOrderId']) ? (string) $p['merchantOrderId'] : null;
        $log = $p; unset($log['signature']);
        $this->order_model->log($no, 'callback', $log);

        if (!$this->duitku->verify_callback($p)) {
            log_message('error', 'Callback Duitku: signature tidak valid untuk ' . $no);
            return $this->reply(400, 'Bad Signature');
        }
        $order = $this->order_model->find($no);
        if (!$order) {
            return $this->reply(404, 'Order Not Found');
        }
        if ((int) round((float) $p['amount']) !== (int) $order['amount']) {
            log_message('error', 'Callback Duitku: nominal tidak cocok untuk ' . $no);
            return $this->reply(400, 'Amount Mismatch');
        }

        if (isset($p['resultCode']) && $p['resultCode'] === '00') {
            // idempoten: hanya pemanggil pertama yang memicu pemenuhan
            if ($this->order_model->mark_paid($no, isset($p['reference']) ? $p['reference'] : null)) {
                $this->fulfillment->process($this->order_model->find($no));
            }
        } else {
            $this->order_model->transition($no, ['pending'], 'failed');
        }
        return $this->reply(200, 'OK');
    }

    private function reply($code, $text)
    {
        $this->output->set_status_header($code)->set_content_type('text/plain')->set_output($text);
    }
}
