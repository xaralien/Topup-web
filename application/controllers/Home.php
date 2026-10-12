<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('game_model');
    }

    public function index()
    {
        $this->render('home', [
            'games'   => $this->game_model->all(),
            'popular' => $this->game_model->popular(),
        ]);
    }
}
