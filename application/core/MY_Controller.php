<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $mode;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'device']);
        $this->mode = view_mode();
    }

    /**
     * Render halaman dari folder views/{mode}/{page}.php dibungkus partial head & foot.
     */
    protected function render($page, array $data = [])
    {
        $data['mode']  = $this->mode;
        $data['title'] = isset($data['title']) ? $data['title'] . ' · MYTopup' : 'MYTopup – Top Up Game Cepat & Aman';
        $this->load->view('partials/head', $data);
        $this->load->view($this->mode . '/' . $page, $data);
        $this->load->view($this->mode . '/footer', $data);
        $this->load->view('partials/foot', $data);
    }
}
