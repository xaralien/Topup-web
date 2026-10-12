<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_Controller
{
    public function index()
    {
        $this->order_model->expire_stale();
        list($recent) = $this->order_model->search('', '', 8, 0);
        $this->render_admin('dashboard', ['title' => 'Dashboard', 'stats' => $this->order_model->stats(), 'recent' => $recent,
            'chart' => $this->order_model->daily_revenue(14), 'status_counts' => $this->order_model->status_counts()]);
    }
}
