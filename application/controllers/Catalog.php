<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
    }

    /** /games (semua game + filter kategori) dan /search?q= (game + produk). */
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

    /** JSON untuk dropdown saran saat mengetik. */
    public function suggest()
    {
        $q = mb_substr(trim((string) $this->input->get('q', TRUE)), 0, 50);
        $out = ['games' => [], 'products' => []];
        if (mb_strlen($q) >= 2) {
            foreach (array_slice($this->game_model->search($q), 0, 5) as $g) {
                $out['games'][] = ['name' => $g['name'], 'meta' => $g['category'], 'color' => $g['color'], 'url' => site_url('game/' . $g['slug'])];
            }
            foreach ($this->game_model->products($q, 4) as $p) {
                $out['products'][] = ['name' => $p['game']['name'] . ' ' . $p['qty'] . ' ' . $p['game']['currency'], 'meta' => rupiah($p['price']), 'color' => $p['game']['color'], 'url' => site_url('game/' . $p['game']['slug'])];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }
}
