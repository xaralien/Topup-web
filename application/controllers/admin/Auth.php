<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Admin_Controller
{
    protected $require_login = FALSE;

    public function login()
    {
        if ($this->session->userdata('admin_id') && $this->admin_model->find($this->session->userdata('admin_id'))) {
            redirect('admin');
        }
        $error = null;
        if ($this->input->method() === 'post') {
            $ip = $this->input->ip_address();
            if ($this->admin_model->is_locked($ip)) {
                $error = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . Admin_model::WINDOW_MIN . ' menit.';
            } else {
                $a = $this->admin_model->authenticate(
                    mb_substr((string) $this->input->post('username', TRUE), 0, 30),
                    mb_substr((string) $this->input->post('password'), 0, 200)
                );
                if ($a) {
                    $this->admin_model->clear_failures($ip);
                    $this->session->sess_regenerate(TRUE);     // cegah session fixation
                    $this->session->set_userdata('admin_id', (int) $a['id']);
                    redirect('admin');
                }
                $this->admin_model->record_failure($ip);
                $error = 'Username atau password salah.';
            }
        }
        $this->load->view('admin/login', ['error' => $error, 'no_admin' => $this->admin_model->count() === 0]);
    }

    public function logout()
    {
        if ($this->input->method() === 'post') {
            $this->session->sess_destroy();
        }
        redirect('admin/login');
    }
}
