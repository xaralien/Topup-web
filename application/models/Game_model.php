<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Katalog dari database. Bentuk array dijaga sama dengan prototipe:
 * 'packs' => [[qty, price, pack_id], ...] supaya view lama tetap jalan.
 */
class Game_model extends CI_Model
{
    private $cache = null;

    public function all()
    {
        if ($this->cache !== null) {
            return $this->cache;
        }
        $games = [];
        foreach ($this->db->order_by('sort_order, id')->get_where('games', ['is_active' => 1])->result_array() as $g) {
            $games[$g['id']] = [
                'id' => (int) $g['id'], 'slug' => $g['slug'], 'category' => $g['category'], 'name' => $g['name'],
                'currency' => $g['currency'], 'color' => $g['color'], 'hot' => (bool) $g['is_hot'],
                'needs_zone' => (bool) $g['needs_zone'], 'packs' => [],
            ];
        }
        if ($games) {
            $q = $this->db->where_in('game_id', array_keys($games))->where('is_active', 1)->order_by('sort_order, id')->get('packs');
            foreach ($q->result_array() as $p) {
                $games[$p['game_id']]['packs'][] = [$p['qty'], (int) $p['price'], (int) $p['id']];
            }
        }
        // game tanpa nominal aktif tidak ditampilkan
        return $this->cache = array_values(array_filter($games, function ($g) { return !empty($g['packs']); }));
    }

    /** Game populer; selalu minimal $min item (dilengkapi game lain bila yang ditandai is_hot kurang). */
    public function popular($min = 5)
    {
        $all = $this->all();
        $hot = array_values(array_filter($all, function ($g) { return $g['hot']; }));
        if (count($hot) < $min) {
            $rest = array_values(array_filter($all, function ($g) { return !$g['hot']; }));
            $hot = array_merge($hot, array_slice($rest, 0, $min - count($hot)));
        }
        return $hot;
    }

    public function find($slug)
    {
        foreach ($this->all() as $g) {
            if ($g['slug'] === $slug) {
                return $g;
            }
        }
        return null;
    }

    /** Ambil nominal milik game tsb – harga SELALU dari DB, bukan dari input pengguna. */
    public function find_pack($game_id, $pack_id)
    {
        return $this->db->get_where('packs', ['id' => (int) $pack_id, 'game_id' => (int) $game_id, 'is_active' => 1])->row_array();
    }

    public function slug_by_id($id)
    {
        $r = $this->db->select('slug')->get_where('games', ['id' => (int) $id])->row_array();
        return $r ? $r['slug'] : '';
    }

    public function categories()
    {
        return array_values(array_unique(array_column($this->all(), 'category')));
    }

    private function norm($s)
    {
        return trim(preg_replace('/[\s\-_]+/', ' ', mb_strtolower((string) $s)));
    }

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
