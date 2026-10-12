<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** /account – profil & riwayat pesanan pelanggan. */
class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->boot_session();
        if (!$this->current_user) {
            redirect('auth/login?next=account');
        }
        $this->load->model('order_model');
        $this->output->set_header('Cache-Control: no-store');
    }

    public function index()
    {
        $this->render('account', [
            'title'  => 'Akun Saya',
            'orders' => $this->order_model->for_user($this->current_user['id']),
            'flash'  => $this->session->flashdata('flash'),
        ]);
    }

    /** POST /account/password */
    public function password()
    {
        if ($this->input->method() !== 'post') {
            redirect('account');
        }
        $this->load->model('user_model');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('old', 'Password lama', 'required');
        $this->form_validation->set_rules('password', 'Password baru', 'required|min_length[8]|max_length[200]');
        $this->form_validation->set_rules('password2', 'Konfirmasi', 'required|matches[password]');
        if (!$this->form_validation->run()) {
            $this->flash('Gagal: periksa password lama, minimal 8 karakter, dan konfirmasi harus sama.', TRUE);
        } elseif (!password_verify((string) $this->input->post('old'), $this->current_user['password_hash'])) {
            $this->flash('Password lama salah.', TRUE);
        } else {
            $this->user_model->set_password($this->current_user['id'], $this->input->post('password'));
            $this->flash('Password diganti.');
        }
        redirect('account');
    }
}
