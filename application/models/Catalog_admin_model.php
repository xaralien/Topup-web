<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** CRUD katalog untuk admin (termasuk game/nominal nonaktif). */
class Catalog_admin_model extends CI_Model
{
    public function games()
    {
        return $this->db->query('SELECT g.*, (SELECT COUNT(*) FROM packs p WHERE p.game_id = g.id AND p.is_active = 1) AS pack_count,
                                        (SELECT COUNT(*) FROM orders o WHERE o.game_id = g.id) AS order_count
                                 FROM games g ORDER BY g.sort_order, g.id')->result_array();
    }

    public function game($id)
    {
        return $this->db->get_where('games', ['id' => (int) $id])->row_array();
    }

    public function slug_exists($slug, $except_id = 0)
    {
        return $this->db->where('slug', $slug)->where('id !=', (int) $except_id)->count_all_results('games') > 0;
    }

    public function categories()
    {
        return array_column($this->db->select('DISTINCT category', FALSE)->order_by('category')->get('games')->result_array(), 'category');
    }

    public function save_game($id, array $d)
    {
        if ($id) {
            $this->db->where('id', (int) $id)->update('games', $d);
            return (int) $id;
        }
        $this->db->insert('games', $d);
        return $this->db->insert_id();
    }

    public function delete_game($id)
    {
        $this->db->delete('games', ['id' => (int) $id]);   // packs ikut terhapus (ON DELETE CASCADE)
    }

    public function packs($game_id)
    {
        return $this->db->order_by('sort_order, id')->get_where('packs', ['game_id' => (int) $game_id])->result_array();
    }

    public function pack($id)
    {
        return $this->db->get_where('packs', ['id' => (int) $id])->row_array();
    }

    public function pack_order_count($pack_id)
    {
        return $this->db->where('pack_id', (int) $pack_id)->count_all_results('orders');
    }

    public function save_pack($id, array $d)
    {
        if ($id) {
            $this->db->where('id', (int) $id)->update('packs', $d);
            return;
        }
        $this->db->insert('packs', $d);
    }

    public function delete_pack($id)
    {
        $this->db->delete('packs', ['id' => (int) $id]);
    }
}
