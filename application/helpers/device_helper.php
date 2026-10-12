<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tentukan folder view: 'mobile' atau 'desktop'.
 * Prioritas: ?view=mobile|desktop (disimpan di cookie) > cookie > User-Agent.
 */
if (!function_exists('view_mode')) {
    function view_mode()
    {
        $CI =& get_instance();
        $force = $CI->input->get('view', TRUE);
        if (in_array($force, ['mobile', 'desktop'], TRUE)) {
            $CI->input->set_cookie(['name' => 'view_mode', 'value' => $force, 'expire' => 86400 * 30]);
            return $force;
        }
        $cookie = $CI->input->cookie('view_mode', TRUE);
        if (in_array($cookie, ['mobile', 'desktop'], TRUE)) {
            return $cookie;
        }
        $ua = (string) $CI->input->user_agent();
        return preg_match('/Android|iPhone|iPod|Mobile|Opera Mini|IEMobile/i', $ua) ? 'mobile' : 'desktop';
    }
}

if (!function_exists('rupiah')) {
    function rupiah($n)
    {
        return 'Rp' . number_format((float) $n, 0, ',', '.');
    }
}
