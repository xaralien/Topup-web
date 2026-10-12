<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pemenuhan pesanan setelah pembayaran diterima (kirim item ke akun game).
 * Duitku hanya payment gateway; pengiriman item butuh supplier (Digiflazz, VIP Reseller, dst.).
 *
 * Driver 'manual' (default): pesanan -> 'processing', diselesaikan operator:
 *     php index.php cli complete MT2610...   (atau cli fail <no>)
 * Untuk supplier otomatis: tambahkan method driver_<nama>() lalu set FULFILLMENT_DRIVER=<nama>.
 */
class Fulfillment
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('duitku', TRUE);
        $this->CI->load->model('order_model');
    }

    public function process(array $order)
    {
        $driver = $this->CI->config->item('fulfillment_driver', 'duitku');
        $method = 'driver_' . preg_replace('/[^a-z0-9_]/i', '', $driver);
        if (!method_exists($this, $method)) {
            $method = 'driver_manual';
        }
        $this->CI->order_model->log($order['order_no'], 'fulfill', 'driver=' . $driver);
        return $this->$method($order);
    }

    private function driver_manual(array $order)
    {
        return $this->CI->order_model->transition($order['order_no'], ['paid'], 'processing');
    }
}
