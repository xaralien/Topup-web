<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data dummy. Ganti dengan query database / API supplier saat backend siap.
 */
class Game_model extends CI_Model
{
    private $games = [
        'mobile-legends' => ['name' => 'Mobile Legends', 'currency' => 'Diamond', 'color' => '#C7D2FE', 'hot' => true,
            'packs' => [['86', 20000], ['172', 39000], ['257', 58000], ['514', 115000], ['1050', 230000]]],
        'free-fire' => ['name' => 'Free Fire', 'currency' => 'Diamond', 'color' => '#FDE68A', 'hot' => true,
            'packs' => [['70', 10000], ['140', 20000], ['355', 50000], ['720', 100000]]],
        'genshin-impact' => ['name' => 'Genshin Impact', 'currency' => 'Genesis Crystal', 'color' => '#FBCFE8', 'hot' => false,
            'packs' => [['60', 16000], ['300', 79000], ['980', 249000], ['1980', 479000]]],
        'pubg-mobile' => ['name' => 'PUBG Mobile', 'currency' => 'UC', 'color' => '#BAE6FD', 'hot' => true,
            'packs' => [['60', 15000], ['325', 75000], ['660', 149000]]],
        'roblox' => ['name' => 'Roblox', 'currency' => 'Robux', 'color' => '#BBF7D0', 'hot' => false,
            'packs' => [['80', 15000], ['400', 75000], ['800', 150000]]],
        'valorant' => ['name' => 'Valorant', 'currency' => 'VP', 'color' => '#DDD6FE', 'hot' => false,
            'packs' => [['475', 59000], ['1000', 119000], ['2050', 239000]]],
    ];

    public function all()
    {
        $out = [];
        foreach ($this->games as $slug => $g) {
            $out[] = ['slug' => $slug] + $g;
        }
        return $out;
    }

    public function popular()
    {
        return array_values(array_filter($this->all(), function ($g) { return $g['hot']; }));
    }

    public function find($slug)
    {
        return isset($this->games[$slug]) ? ['slug' => $slug] + $this->games[$slug] : null;
    }
}
