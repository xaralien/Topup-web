<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Kelola akun admin. */
class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->form_validation->set_message([
            'required' => '{field} wajib diisi.', 'min_length' => '{field} minimal {param} karakter.', 'max_length' => '{field} terlalu panjang.',
            'matches' => '{field} tidak sama.', 'regex_match' => '{field} tidak valid.', 'is_unique' => '{field} sudah dipakai.',
        ]);
    }

    public function index()
    {
        $this->render_admin('users', ['title' => 'Akun Admin', 'users' => $this->admin_model->all()]);
    }

    public function create()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/users');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|trim|regex_match[/^[a-z0-9_.]{3,30}$/]|is_unique[admins.username]');
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[80]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[10]|max_length[200]');
        $this->form_validation->set_rules('password2', 'Konfirmasi password', 'required|matches[password]');
        if ($this->form_validation->run()) {
            $this->admin_model->create($this->input->post('username', TRUE), $this->input->post('name', TRUE), $this->input->post('password'));
            $this->flash('Admin ditambahkan.');
        } else {
            $this->flash(trim(strip_tags(str_replace('</p>', ' ', validation_errors()))), TRUE);
        }
        redirect('admin/users');
    }

    public function password($id = 0)
    {
        $u = $this->admin_model->find((int) $id);
        if ($this->input->method() !== 'post' || !$u) {
            show_404();
        }
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[10]|max_length[200]');
        $this->form_validation->set_rules('password2', 'Konfirmasi password', 'required|matches[password]');
        if ($this->form_validation->run()) {
            $this->admin_model->set_password($u['id'], $this->input->post('password'));
            $this->flash('Password ' . $u['username'] . ' diganti.');
        } else {
            $this->flash(trim(strip_tags(str_replace('</p>', ' ', validation_errors()))), TRUE);
        }
        redirect('admin/users');
    }

    public function delete($id = 0)
    {
        $u = $this->admin_model->find((int) $id);
        if ($this->input->method() !== 'post' || !$u) {
            show_404();
        }
        if ((int) $u['id'] === (int) $this->admin['id']) {
            $this->flash('Tidak bisa menghapus akun yang sedang dipakai.', TRUE);
        } elseif ($this->admin_model->count() <= 1) {
            $this->flash('Minimal harus ada satu admin.', TRUE);
        } else {
            $this->admin_model->delete($u['id']);
            $this->flash('Admin dihapus.');
        }
        redirect('admin/users');
    }
}
