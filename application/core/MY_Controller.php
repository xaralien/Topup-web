<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $mode;
    protected $current_user = null;   // pelanggan yang sedang login (atau null)

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'device', 'topup']);
        $this->config->load('duitku', TRUE);
        $this->mode = view_mode();

        // Sesi hanya dibuka bila pengunjung sudah punya cookie sesi (tamu tidak dibebani sesi).
        if ($this->input->cookie($this->config->item('sess_cookie_name'))) {
            $this->boot_session();
        }
    }

    /** Muat library sesi (dan pastikan folder penyimpanannya ada); isi $current_user. */
    protected function boot_session()
    {
        $dir = APPPATH . 'sessions';
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, TRUE);
        }
        $this->load->library('session');
        $uid = $this->session->userdata('user_id');
        if ($uid) {
            $this->load->model('user_model');
            $this->current_user = $this->user_model->find($uid) ?: null;
            if (!$this->current_user) {
                $this->session->unset_userdata('user_id');
            }
        }
    }

    /**
     * Render halaman dari folder views/{mode}/{page}.php dibungkus partial head & foot.
     */
    protected function render($page, array $data = [])
    {
        $data['mode']  = $this->mode;
        $data['user']  = $this->current_user;
        $data['title'] = isset($data['title']) ? $data['title'] . ' · MYTopup' : 'MYTopup – Top Up Game Cepat & Aman';
        $this->load->view('partials/head', $data);
        $this->load->view($this->mode . '/' . $page, $data);
        $this->load->view($this->mode . '/footer', $data);
        $this->load->view('partials/foot', $data);
    }

    protected function flash($msg, $error = FALSE)
    {
        $this->session->set_flashdata($error ? 'flash_error' : 'flash', $msg);
    }
}

/**
 * Basis semua controller admin: wajib login sebagai admin, layout admin, flash message.
 */
class Admin_Controller extends CI_Controller
{
    protected $admin = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'device', 'topup']);
        $dir = APPPATH . 'sessions';
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, TRUE);
        }
        $this->load->library('session');
        $this->load->model(['admin_model', 'order_model']);

        // jangan di-cache browser/proxy, jangan boleh di-iframe
        $this->output->set_header('Cache-Control: no-store');
        $this->output->set_header('X-Frame-Options: DENY');

        $id = $this->session->userdata('admin_id');
        $this->admin = $id ? $this->admin_model->find($id) : null;
        if (!$this->admin) {
            $this->session->unset_userdata('admin_id');
            redirect('auth/login?next=admin');
        }
    }

    protected function render_admin($page, array $data = [])
    {
        $data['admin'] = $this->admin;
        $data['title'] = (isset($data['title']) ? $data['title'] . ' · ' : '') . 'Admin MYTopup';
        $data['flash'] = $this->session->flashdata('flash');
        $data['flash_error'] = $this->session->flashdata('flash_error');
        $this->load->view('admin/_head', $data);
        $this->load->view('admin/' . $page, $data);
        $this->load->view('admin/_foot', $data);
    }

    protected function flash($msg, $error = FALSE)
    {
        $this->session->set_flashdata($error ? 'flash_error' : 'flash', $msg);
    }
}
