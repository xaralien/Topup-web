<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller
{
    const PER_PAGE = 25;
    private $statuses = ['pending', 'paid', 'processing', 'success', 'failed', 'expired'];

    public function index()
    {
        $status = (string) $this->input->get('status', TRUE);
        $status = in_array($status, $this->statuses, TRUE) ? $status : '';
        $q      = mb_substr(trim((string) $this->input->get('q', TRUE)), 0, 60);
        $page   = max(1, (int) $this->input->get('page'));
        list($rows, $total) = $this->order_model->search($status, $q, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
        $this->render_admin('orders', [
            'title' => 'Pesanan', 'rows' => $rows, 'total' => $total, 'status' => $status, 'q' => $q,
            'statuses' => $this->statuses, 'page' => $page, 'pages' => max(1, (int) ceil($total / self::PER_PAGE)),
        ]);
    }

    public function view($no = '')
    {
        $order = $this->order_model->find($no);
        if (!$order) {
            show_404();
        }
        $this->render_admin('order_view', ['title' => 'Pesanan ' . $no, 'order' => $order, 'logs' => $this->order_model->logs($no)]);
    }

    /** POST: tandai item sudah dikirim / gagal. Hanya dari status paid/processing. */
    public function set($no = '', $to = '')
    {
        if ($this->input->method() !== 'post' || !in_array($to, ['success', 'failed'], TRUE) || !$this->order_model->find($no)) {
            show_404();
        }
        $extra = $to === 'success' ? ['fulfilled_at' => date('Y-m-d H:i:s')] : [];
        if ($this->order_model->transition($no, ['paid', 'processing'], $to, $extra)) {
            $this->order_model->log($no, 'admin', ['by' => $this->admin['username'], 'to' => $to]);
            $this->flash('Pesanan ditandai ' . ($to === 'success' ? 'berhasil' : 'gagal') . '.');
        } else {
            $this->flash('Status tidak berubah (hanya pesanan yang sudah dibayar yang bisa diubah).', TRUE);
        }
        redirect('admin/orders/view/' . $no);
    }
}
