<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_hasil — Model untuk hasil_mediasi (skema 3NF)
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

    public function get_all_for_laporan($filter = []) {
        $this->db->select('hm.*, p.nomor_perkara, jp.nama as jenis_perkara, m.nama as nama_mediator, u.nama as nama_pp');
        $this->db->from('hasil_mediasi hm');
        $this->db->join('perkara p', 'p.id = hm.perkara_id', 'left');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('mediators m', 'm.id = hm.mediator_id', 'left');
        $this->db->join('users u', 'u.id = p.pp_id', 'left');
        if (!empty($filter['status_hasil'])) $this->db->where('hm.status_hasil', $filter['status_hasil']);
        if (!empty($filter['tahun'])) $this->db->where('YEAR(hm.created_at)', $filter['tahun']);
        $this->db->order_by('hm.created_at', 'DESC');
        return $this->db->get()->result();
    }
}
