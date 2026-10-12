<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $mode;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'device', 'topup']);
        $this->config->load('duitku', TRUE);
        $this->mode = view_mode();
    }

    /**
     * Render halaman dari folder views/{mode}/{page}.php dibungkus partial head & foot.
     */
    protected function render($page, array $data = [])
    {
        $data['mode']  = $this->mode;
        $data['title'] = isset($data['title']) ? $data['title'] . ' · MYTopup' : 'MYTopup – Top Up Game Cepat & Aman';
        $this->load->view('partials/head', $data);
        $this->load->view($this->mode . '/' . $page, $data);
        $this->load->view($this->mode . '/footer', $data);
        $this->load->view('partials/foot', $data);
    }
}

/**
 * Basis semua controller admin: wajib login (sesi), layout admin, helper flash message.
 * Controller Admin/Auth memakai base ini dengan $require_login = FALSE.
 */
class Admin_Controller extends CI_Controller
{
    protected $require_login = TRUE;
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

        // jangan di-cache browser/proxy
        $this->output->set_header('Cache-Control: no-store');
        $this->output->set_header('X-Frame-Options: DENY');

        if ($this->require_login) {
            $id = $this->session->userdata('admin_id');
            $this->admin = $id ? $this->admin_model->find($id) : null;
            if (!$this->admin) {
                $this->session->sess_destroy();
                redirect('admin/login');
            }
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
