<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    public function index()
    {
        $this->order_model->expire_stale();
        list($recent) = $this->order_model->search('', '', 8, 0);
        $this->render_admin('dashboard', ['title' => 'Dashboard', 'stats' => $this->order_model->stats(), 'recent' => $recent]);
    }
}
