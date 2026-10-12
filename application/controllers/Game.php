<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
    }

    public function detail($slug = '')
    {
        $game = $this->game_model->find($slug);
        if (!$game) {
            show_404();
        }
        $this->render('game', [
            'title'   => 'Top Up ' . $game['name'],
            'game'    => $game,
            'methods' => $this->config->item('payment_methods', 'duitku'),
        ]);
    }
}
