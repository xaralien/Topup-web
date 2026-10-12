<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Games extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('text');
        $this->load->model('catalog_admin_model', 'catalog');
        $this->load->library(['form_validation', 'game_image']);
        $this->form_validation->set_message([
            'required' => '{field} wajib diisi.', 'max_length' => '{field} terlalu panjang.', 'min_length' => '{field} terlalu pendek.',
            'regex_match' => '{field} tidak valid.', 'is_natural' => '{field} harus angka bulat.', 'is_natural_no_zero' => '{field} harus angka > 0.',
            'less_than_equal_to' => '{field} terlalu besar.',
        ]);
    }

    public function index()
    {
        $this->render_admin('games', ['title' => 'Game & Produk', 'games' => $this->catalog->games()]);
    }

    public function create()
    {
        $this->form(0);
    }

    public function edit($id = 0)
    {
        $this->form((int) $id);
    }

    private function form($id)
    {
        $game = $id ? $this->catalog->game($id) : null;
        if ($id && !$game) {
            show_404();
        }
        $error = null;

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[2]|max_length[120]');
            if (!$id) {
                $this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[60]|regex_match[/^([a-z0-9]+(-[a-z0-9]+)*)?$/]');
            }
            $this->form_validation->set_rules('category', 'Kategori', 'required|trim|max_length[40]');
            $this->form_validation->set_rules('currency', 'Mata uang item', 'required|trim|max_length[60]');
            $this->form_validation->set_rules('color', 'Warna', 'required|regex_match[/^#[0-9a-fA-F]{6}$/]');
            $this->form_validation->set_rules('sort_order', 'Urutan', 'trim|is_natural|less_than_equal_to[100000]');

            if ($this->form_validation->run()) {
                $slug = $game ? $game['slug'] : (string) $this->input->post('slug', TRUE);
                if (!$game) {
                    $slug = $slug !== '' ? $slug : strtolower(url_title(convert_accented_characters((string) $this->input->post('name', TRUE)), '-', TRUE));
                    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug) || strlen($slug) > 60) {
                        $error = 'Slug tidak valid. Gunakan huruf kecil, angka, dan tanda minus.';
                    } elseif ($this->catalog->slug_exists($slug)) {
                        $error = 'Slug "' . $slug . '" sudah dipakai game lain.';
                    }
                }
                $image = $game ? $game['image'] : null;
                $new_image = null;
                if (!$error) {
                    $new_image = $this->game_image->store('image');
                    if ($new_image === null && $this->game_image->error) {
                        $error = 'Gambar: ' . $this->game_image->error;
                    }
                }
                if (!$error) {
                    if ($new_image) {
                        $this->game_image->remove($image);
                        $image = $new_image;
                    } elseif ($this->input->post('remove_image')) {
                        $this->game_image->remove($image);
                        $image = null;
                    }
                    $gid = $this->catalog->save_game($id, [
                        'slug' => $slug, 'name' => $this->input->post('name', TRUE), 'category' => $this->input->post('category', TRUE),
                        'currency' => $this->input->post('currency', TRUE), 'color' => $this->input->post('color'), 'image' => $image,
                        'is_hot' => $this->input->post('is_hot') ? 1 : 0, 'needs_zone' => $this->input->post('needs_zone') ? 1 : 0,
                        'is_active' => $this->input->post('is_active') ? 1 : 0, 'sort_order' => (int) $this->input->post('sort_order'),
                    ]);
                    $this->flash($id ? 'Game disimpan.' : 'Game dibuat. Tambahkan nominal produk di bawah.');
                    redirect('admin/games/edit/' . $gid);
                }
            }
        }

        $this->render_admin('game_form', [
            'title' => $game ? 'Edit ' . $game['name'] : 'Tambah Game', 'game' => $game, 'error' => $error,
            'categories' => $this->catalog->categories(), 'packs' => $game ? $this->catalog->packs($id) : [],
        ]);
    }

    public function delete($id = 0)
    {
        $game = $this->post_game($id);
        $orders = (int) $this->db->where('game_id', $game['id'])->count_all_results('orders');
        if ($orders > 0) {
            $this->flash('Game punya ' . $orders . ' pesanan, tidak bisa dihapus. Nonaktifkan saja.', TRUE);
            redirect('admin/games/edit/' . $game['id']);
        }
        $this->game_image->remove($game['image']);
        $this->catalog->delete_game($game['id']);
        $this->flash('Game dihapus.');
        redirect('admin/games');
    }

    /** POST admin/games/pack/{game_id}/{pack_id|0} – tambah / ubah nominal. */
    public function pack($game_id = 0, $pack_id = 0)
    {
        $game = $this->post_game($game_id);
        $pack_id = (int) $pack_id;
        if ($pack_id && (!($p = $this->catalog->pack($pack_id)) || (int) $p['game_id'] !== (int) $game['id'])) {
            show_404();
        }
        $this->form_validation->set_rules('qty', 'Jumlah', 'required|trim|max_length[20]|regex_match[/^[0-9A-Za-z .,]+$/]');
        $this->form_validation->set_rules('price', 'Harga', 'required|trim|is_natural_no_zero|less_than_equal_to[100000000]');
        $this->form_validation->set_rules('sku', 'SKU', 'trim|max_length[60]');
        $this->form_validation->set_rules('sort_order', 'Urutan', 'trim|is_natural|less_than_equal_to[100000]');
        if (!$this->form_validation->run()) {
            $this->flash(trim(strip_tags(str_replace('</p>', ' ', validation_errors()))), TRUE);
            redirect('admin/games/edit/' . $game['id']);
        }
        $d = [
            'qty' => $this->input->post('qty', TRUE), 'price' => (int) $this->input->post('price'),
            'sku' => $this->input->post('sku', TRUE) ?: null, 'sort_order' => (int) $this->input->post('sort_order'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ];
        $this->catalog->save_pack($pack_id, $pack_id ? $d : $d + ['game_id' => (int) $game['id']]);
        $this->flash($pack_id ? 'Nominal disimpan.' : 'Nominal ditambahkan.');
        redirect('admin/games/edit/' . $game['id']);
    }

    public function pack_delete($game_id = 0, $pack_id = 0)
    {
        $game = $this->post_game($game_id);
        $p = $this->catalog->pack((int) $pack_id);
        if (!$p || (int) $p['game_id'] !== (int) $game['id']) {
            show_404();
        }
        if ($this->catalog->pack_order_count($p['id']) > 0) {
            $this->flash('Nominal ini sudah dipakai pesanan, tidak bisa dihapus. Nonaktifkan saja.', TRUE);
        } else {
            $this->catalog->delete_pack($p['id']);
            $this->flash('Nominal dihapus.');
        }
        redirect('admin/games/edit/' . $game['id']);
    }

    private function post_game($id)
    {
        $game = $this->input->method() === 'post' ? $this->catalog->game((int) $id) : null;
        if (!$game) {
            show_404();
        }
        return $game;
    }
}
