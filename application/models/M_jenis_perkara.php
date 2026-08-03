<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_jenis_perkara extends CI_Model {

    public function get_all() {
        return $this->db->get('jenis_perkara')->result();
    }

    public function count_all($filter = []) {
        $this->_apply_filter($filter);
        return $this->db->count_all_results('jenis_perkara');
    }

    public function get_paginated($filter = [], $limit = 10, $offset = 0) {
        $this->_apply_filter($filter);
        $this->db->limit($limit, $offset);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('jenis_perkara')->result();
    }

    private function _apply_filter($filter) {
        if (!empty($filter['status'])) {
            if ($filter['status'] === 'aktif') $this->db->where('is_active', 1);
            if ($filter['status'] === 'nonaktif') $this->db->where('is_active', 0);
        }
        if (!empty($filter['search'])) {
            $s = $this->db->escape_like_str($filter['search']);
            $this->db->group_start();
            $this->db->like('nama', $s);
            $this->db->or_like('keterangan', $s);
            $this->db->group_end();
        }
    }

    public function get_all_aktif() {
        return $this->db->get_where('jenis_perkara', ['is_active' => 1])->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('jenis_perkara', ['id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('jenis_perkara', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('jenis_perkara', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('jenis_perkara');
    }

    public function toggle_aktif($id) {
        $jp = $this->get_by_id($id);
        if (!$jp) return false;
        return $this->db->where('id', $id)->update('jenis_perkara', ['is_active' => $jp->is_active ? 0 : 1]);
    }
}
