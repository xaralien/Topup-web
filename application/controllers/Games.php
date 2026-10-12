<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Games extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
    }

    /** /games – semua game, filter kategori (?kategori=) dan pencarian (?q=) game + produk. */
    public function index()
    {
        $q   = trim((string) $this->input->get('q', TRUE));
        $cat = trim((string) $this->input->get('kategori', TRUE));
        $q   = mb_substr($q, 0, 50);

        $cats = $this->game_model->categories();
        if (!in_array($cat, $cats, TRUE)) {
            $cat = '';
        }

        $this->render('games', [
            'title'      => $q !== '' ? 'Cari "' . $q . '"' : 'Semua Game',
            'q'          => $q,
            'cat'        => $cat,
            'categories' => $cats,
            'games'      => $this->game_model->search($q, $cat),
            'products'   => $q !== '' ? $this->game_model->products($q, 12) : [],
        ]);
    }
}
