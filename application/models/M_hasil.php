<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_hasil — Model untuk hasil_mediasi
 */
class M_hasil extends CI_Model {

    public function get_by_perkara($perkara_id) {
        return $this->db->get_where('hasil_mediasi', ['perkara_id' => $perkara_id])->row();
    }

    public function insert($data) {
        return $this->db->insert('hasil_mediasi', $data);
    }

    public function is_exist($perkara_id) {
        return $this->db->where('perkara_id', $perkara_id)->count_all_results('hasil_mediasi') > 0;
    }
}
