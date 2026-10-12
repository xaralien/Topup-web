<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data dummy. Ganti dengan query database / API supplier saat backend siap.
 */
class Game_model extends CI_Model
{
    private $games = [
        'mobile-legends' => ['category' => 'Game Mobile', 'name' => 'Mobile Legends', 'currency' => 'Diamond', 'color' => '#C7D2FE', 'hot' => true,
            'packs' => [['86', 20000], ['172', 39000], ['257', 58000], ['514', 115000], ['1050', 230000]]],
        'free-fire' => ['category' => 'Game Mobile', 'name' => 'Free Fire', 'currency' => 'Diamond', 'color' => '#FDE68A', 'hot' => true,
            'packs' => [['70', 10000], ['140', 20000], ['355', 50000], ['720', 100000]]],
        'genshin-impact' => ['category' => 'Game Mobile', 'name' => 'Genshin Impact', 'currency' => 'Genesis Crystal', 'color' => '#FBCFE8', 'hot' => false,
            'packs' => [['60', 16000], ['300', 79000], ['980', 249000], ['1980', 479000]]],
        'pubg-mobile' => ['category' => 'Game Mobile', 'name' => 'PUBG Mobile', 'currency' => 'UC', 'color' => '#BAE6FD', 'hot' => true,
            'packs' => [['60', 15000], ['325', 75000], ['660', 149000]]],
        'roblox' => ['category' => 'Game PC', 'name' => 'Roblox', 'currency' => 'Robux', 'color' => '#BBF7D0', 'hot' => false,
            'packs' => [['80', 15000], ['400', 75000], ['800', 150000]]],
        'valorant' => ['category' => 'Game PC', 'name' => 'Valorant', 'currency' => 'VP', 'color' => '#DDD6FE', 'hot' => false,
            'packs' => [['475', 59000], ['1000', 119000], ['2050', 239000]]],
        'steam-wallet' => ['category' => 'Voucher', 'name' => 'Steam Wallet', 'currency' => 'IDR Voucher', 'color' => '#E2E8F0', 'hot' => false,
            'packs' => [['12000', 14000], ['45000', 50000], ['120000', 130000]]],
        'google-play' => ['category' => 'Voucher', 'name' => 'Google Play', 'currency' => 'Voucher', 'color' => '#D1FAE5', 'hot' => false,
            'packs' => [['20000', 22000], ['50000', 53000], ['100000', 104000]]],
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

    public function categories()
    {
        return array_values(array_unique(array_column($this->all(), 'category')));
    }

    private function norm($s)
    {
        return trim(preg_replace('/[\s\-_]+/', ' ', mb_strtolower((string) $s)));
    }

    /** Cari game berdasarkan nama/mata uang/kategori; $cat opsional. */
    public function search($q = '', $cat = '')
    {
        $q = $this->norm($q);
        return array_values(array_filter($this->all(), function ($g) use ($q, $cat) {
            if ($cat !== '' && $g['category'] !== $cat) {
                return false;
            }
            return $q === '' || strpos($this->norm($g['name'] . ' ' . $g['currency'] . ' ' . $g['category']), $q) !== false;
        }));
    }

    /** Cari produk (nominal) – semua kata kunci harus cocok, mis. "mlbb 86" / "diamond 172". */
    public function products($q, $limit = 12)
    {
        $words = array_filter(explode(' ', $this->norm($q)));
        $out = [];
        if (!$words) {
            return $out;
        }
        foreach ($this->all() as $g) {
            foreach ($g['packs'] as $p) {
                $hay = $this->norm($g['name'] . ' ' . $p[0] . ' ' . $g['currency']);
                foreach ($words as $w) {
                    if (strpos($hay, $w) === false) {
                        continue 2;
                    }
                }
                $out[] = ['game' => $g, 'qty' => $p[0], 'price' => $p[1]];
                if (count($out) >= $limit) {
                    return $out;
                }
            }
        }
        return $out;
    }
}
