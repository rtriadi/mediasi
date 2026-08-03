<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_ruangan extends CI_Model {

    public function get_all() {
        return $this->db->get('ruangan')->result();
    }

    public function count_all($filter = []) {
        $this->_apply_filter($filter);
        return $this->db->count_all_results('ruangan');
    }

    public function get_paginated($filter = [], $limit = 10, $offset = 0) {
        $this->_apply_filter($filter);
        $this->db->limit($limit, $offset);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('ruangan')->result();
    }

    private function _apply_filter($filter) {
        if (!empty($filter['status'])) {
            if ($filter['status'] === 'aktif') $this->db->where('is_active', 1);
            if ($filter['status'] === 'nonaktif') $this->db->where('is_active', 0);
        }
        if (!empty($filter['search'])) {
            $s = $this->db->escape_like_str($filter['search']);
            $this->db->group_start();
            $this->db->like('nama_ruangan', $s);
            $this->db->or_like('keterangan', $s);
            $this->db->group_end();
        }
    }

    public function get_aktif() {
        return $this->db->get_where('ruangan', ['is_active' => 1])->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('ruangan', ['id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('ruangan', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('ruangan', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('ruangan');
    }

    public function toggle_aktif($id) {
        $ruangan = $this->get_by_id($id);
        if (!$ruangan) return false;
        return $this->db->where('id', $id)->update('ruangan', ['is_active' => $ruangan->is_active ? 0 : 1]);
    }
}
