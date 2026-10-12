<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller']   = 'home';
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;

$route['game/(:any)']   = 'game/detail/$1';
$route['games']         = 'catalog/index';
$route['search']        = 'catalog/index';
$route['search/suggest'] = 'catalog/suggest';

$route['order/create']          = 'order/create';
$route['order/check']           = 'order/check';
$route['order/status/(:any)']   = 'order/status/$1';
$route['order/(:any)']          = 'order/view/$1';
$route['payment/callback']      = 'payment/callback';
