<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
        $this->load->library('duitku');
    }

    /** /game/<slug> – segmen kedua dianggap slug (tanpa routes.php). */
    public function _remap($slug = '')
    {
        if ($slug === 'index') {
            redirect('games');
        }
        $this->detail($slug);
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
            'methods' => $this->setting_model->enabled_methods(),
            'pay_ready' => $this->duitku->configured(),
        ]);
    }
}
