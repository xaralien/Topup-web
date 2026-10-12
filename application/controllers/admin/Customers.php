<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends Admin_Controller
{
    public function index()
    {
        $this->load->model('user_model');
        $this->render_admin('customers', ['title' => 'Pelanggan', 'customers' => $this->user_model->with_stats(200)]);
    }
}
