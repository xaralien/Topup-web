<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Tidak ada custom route: URL mengikuti pola CodeIgniter controller/method/param.
|   /games, /search/suggest, /order/check, /payment/callback, /auth/login, /account, /admin/orders/view/<no> ...
| URL cantik /game/<slug> dan /order/<no> ditangani _remap() di controllernya.
*/
$route['default_controller']   = 'home';
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;
