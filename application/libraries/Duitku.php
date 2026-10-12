<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Klien Duitku (API v2): request transaksi, cek status, verifikasi callback.
 * Signature:
 *   inquiry  = md5(merchantCode . merchantOrderId . paymentAmount . apiKey)
 *   status   = md5(merchantCode . merchantOrderId . apiKey)
 *   callback = md5(merchantCode . amount . merchantOrderId . apiKey)
 */
class Duitku
{
    private $CI;
    private $code;
    private $key;
    private $base;

    private $expiry;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('duitku', TRUE);
        $this->CI->load->model('setting_model');
        $c = $this->CI->config->item('duitku');
        $s = $this->CI->setting_model;

        // Prioritas: pengaturan di database (Admin > Pengaturan) -> .env/config sebagai cadangan.
        $this->code = trim($s->get('duitku_merchant_code', '')) ?: $c['duitku_merchant_code'];
        $this->key  = trim($s->get('duitku_api_key', '')) ?: $c['duitku_api_key'];
        $mode = $s->get('duitku_mode', '');
        if (getenv('DUITKU_BASE_URL')) {                         // override khusus development/tes
            $this->base = rtrim(getenv('DUITKU_BASE_URL'), '/');
        } elseif ($mode === 'production') {
            $this->base = 'https://passport.duitku.com/webapi/api/merchant';
        } elseif ($mode === 'sandbox') {
            $this->base = 'https://sandbox.duitku.com/webapi/api/merchant';
        } else {
            $this->base = rtrim($c['duitku_base_url'], '/');
        }
        $this->expiry = (int) $s->get('duitku_expiry', '') ?: (int) $c['duitku_expiry_minutes'];
    }

    public function expiry_minutes()
    {
        return $this->expiry;
    }

    public function mode_label()
    {
        return strpos($this->base, 'sandbox') !== false ? 'sandbox' : 'production';
    }

    public function configured()
    {
        return $this->code !== '' && $this->key !== '';
    }

    /**
     * @param array $o order_no, amount, method, product, email, name, phone, callback_url, return_url, expiry
     * @return array ['ok'=>bool, 'data'=>array, 'error'=>string]
     */
    public function inquiry(array $o)
    {
        $payload = [
            'merchantCode'    => $this->code,
            'paymentAmount'   => (int) $o['amount'],
            'paymentMethod'   => $o['method'],
            'merchantOrderId' => $o['order_no'],
            'productDetails'  => $o['product'],
            'additionalParam' => '',
            'merchantUserId'  => '',
            'customerVaName'  => mb_substr($o['name'], 0, 20),
            'email'           => $o['email'],
            'phoneNumber'     => isset($o['phone']) ? $o['phone'] : '',
            'itemDetails'     => [['name' => mb_substr($o['product'], 0, 50), 'price' => (int) $o['amount'], 'quantity' => 1]],
            'callbackUrl'     => $o['callback_url'],
            'returnUrl'       => $o['return_url'],
            'signature'       => md5($this->code . $o['order_no'] . (int) $o['amount'] . $this->key),
            'expiryPeriod'    => (int) $o['expiry'],
        ];
        $r = $this->post('/v2/inquiry', $payload);
        if ($r['ok'] && (!isset($r['data']['statusCode']) || $r['data']['statusCode'] !== '00' || empty($r['data']['paymentUrl']))) {
            $r['ok'] = FALSE;
            $r['error'] = isset($r['data']['statusMessage']) ? $r['data']['statusMessage'] : 'Respons Duitku tidak valid';
        }
        return $r;
    }

    /**
     * Daftar metode bayar yang AKTIF di project Duitku ini. Sekaligus berfungsi sebagai tes kredensial.
     * signature = sha256(merchantcode + amount + datetime + apiKey)
     * @return array ['ok'=>bool, 'methods'=>[code=>name], 'error'=>string]
     */
    public function payment_methods($amount = 10000)
    {
        $dt = date('Y-m-d H:i:s');
        $r = $this->post('/paymentmethod/getpaymentmethod', [
            'merchantcode' => $this->code, 'amount' => (int) $amount, 'datetime' => $dt,
            'signature'    => hash('sha256', $this->code . (int) $amount . $dt . $this->key),
        ]);
        $list = isset($r['data']['paymentFee']) && is_array($r['data']['paymentFee']) ? $r['data']['paymentFee'] : [];
        if (!$r['ok'] || !$list) {
            $msg = $r['error'] ?: (isset($r['data']['Message']) ? $r['data']['Message'] : (isset($r['data']['responseMessage']) ? $r['data']['responseMessage'] : 'Respons tidak dikenali'));
            return ['ok' => FALSE, 'methods' => [], 'error' => $msg];
        }
        $m = [];
        foreach ($list as $row) {
            if (!empty($row['paymentMethod'])) {
                $m[(string) $row['paymentMethod']] = (string) (isset($row['paymentName']) ? $row['paymentName'] : $row['paymentMethod']);
            }
        }
        return ['ok' => TRUE, 'methods' => $m, 'error' => ''];
    }

    /** statusCode: 00 = berhasil, 01 = menunggu, 02 = batal/gagal */
    public function status($order_no)
    {
        return $this->post('/transactionStatus', [
            'merchantCode'    => $this->code,
            'merchantOrderId' => $order_no,
            'signature'       => md5($this->code . $order_no . $this->key),
        ]);
    }

    public function verify_callback(array $p)
    {
        foreach (['merchantCode', 'amount', 'merchantOrderId', 'signature'] as $f) {
            if (!isset($p[$f]) || $p[$f] === '') {
                return FALSE;
            }
        }
        if (!hash_equals($this->code, (string) $p['merchantCode'])) {
            return FALSE;
        }
        $calc = md5($p['merchantCode'] . $p['amount'] . $p['merchantOrderId'] . $this->key);
        return hash_equals($calc, strtolower((string) $p['signature']));
    }

    private function post($path, array $payload)
    {
        $ch = curl_init($this->base . $path);
        curl_setopt_array($ch, [
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT        => 20,
        ]);
        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === FALSE) {
            return ['ok' => FALSE, 'http' => 0, 'data' => [], 'error' => 'Koneksi ke Duitku gagal: ' . $err];
        }
        $data = json_decode($body, TRUE);
        if (!is_array($data)) {
            return ['ok' => FALSE, 'http' => $http, 'data' => [], 'error' => 'Respons Duitku bukan JSON (HTTP ' . $http . ')'];
        }
        $ok = $http >= 200 && $http < 300;
        return ['ok' => $ok, 'http' => $http, 'data' => $data, 'error' => $ok ? '' : (isset($data['Message']) ? $data['Message'] : (isset($data['statusMessage']) ? $data['statusMessage'] : 'HTTP ' . $http))];
    }
}
