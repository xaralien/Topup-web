<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Satu pintu masuk untuk semua orang:
 *   - input berisi "@"  -> akun pelanggan (email)  -> /account
 *   - tanpa "@"         -> akun admin (username)   -> /admin
 */
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->boot_session();
        $this->load->model(['admin_model', 'user_model']);
        $this->load->library('form_validation');
    }

    public function login()
    {
        $next = $this->safe_next($this->input->get('next'));
        if ($this->session->userdata('admin_id') && $this->admin_model->find($this->session->userdata('admin_id'))) {
            redirect($next ?: 'admin');
        }
        if ($this->current_user) {
            redirect($next && strpos($next, 'admin') !== 0 ? $next : 'account');
        }

        $error = null;
        $identity = '';
        if ($this->input->method() === 'post') {
            $ip       = $this->input->ip_address();
            $identity = mb_substr(trim((string) $this->input->post('identity', TRUE)), 0, 160);
            $password = mb_substr((string) $this->input->post('password'), 0, 200);

            if ($this->admin_model->is_locked($ip)) {
                $error = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . Admin_model::WINDOW_MIN . ' menit.';
            } elseif (strpos($identity, '@') === FALSE) {
                $a = $this->admin_model->authenticate($identity, $password);
                if ($a) {
                    $this->admin_model->clear_failures($ip);
                    $this->session->sess_regenerate(TRUE);          // cegah session fixation
                    $this->session->set_userdata('admin_id', (int) $a['id']);
                    redirect($next ?: 'admin');
                }
            } else {
                $u = $this->user_model->authenticate($identity, $password);
                if ($u) {
                    $this->admin_model->clear_failures($ip);
                    $this->session->sess_regenerate(TRUE);
                    $this->session->set_userdata('user_id', (int) $u['id']);
                    redirect($next && strpos($next, 'admin') !== 0 ? $next : 'account');
                }
            }
            if (!$error) {
                $this->admin_model->record_failure($ip);
                $error = 'Email/username atau password salah.';
            }
        }
        $this->load->view('auth/login', ['error' => $error, 'identity' => $identity, 'next' => $next, 'title' => 'Masuk · MYTopup']);
    }

    public function register()
    {
        if ($this->current_user) {
            redirect('account');
        }
        $this->form_validation->set_message([
            'required' => '{field} wajib diisi.', 'valid_email' => '{field} tidak valid.', 'min_length' => '{field} minimal {param} karakter.',
            'max_length' => '{field} terlalu panjang.', 'matches' => '{field} tidak sama.', 'is_unique' => '{field} sudah terdaftar. Silakan masuk.',
        ]);
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[2]|max_length[80]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[160]|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[200]');
        $this->form_validation->set_rules('password2', 'Konfirmasi password', 'required|matches[password]');

        if ($this->input->method() === 'post' && $this->form_validation->run()) {
            if ($this->admin_model->is_locked($this->input->ip_address())) {
                show_error('Terlalu banyak percobaan. Coba lagi nanti.', 429);
            }
            $id = $this->user_model->create($this->input->post('name', TRUE), $this->input->post('email', TRUE), $this->input->post('password'));
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata('user_id', $id);
            redirect('account');
        }
        $this->load->view('auth/register', ['title' => 'Daftar · MYTopup']);
    }

    /** POST /auth/logout */
    public function logout()
    {
        if ($this->input->method() === 'post') {
            $this->session->sess_destroy();
        }
        redirect();
    }

    /** Hanya path internal sederhana yang boleh jadi tujuan setelah login (cegah open redirect). */
    private function safe_next($next)
    {
        $next = trim((string) $next, '/');
        return preg_match('#^[A-Za-z0-9_\-]+(/[A-Za-z0-9_\-]+)*$#', $next) ? $next : '';
    }
}
