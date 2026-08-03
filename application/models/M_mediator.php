<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_mediator — Model untuk tabel mediators
 */
class M_mediator extends CI_Model {

    public function get_all($filter = [], $limit = 10, $offset = 0) {
        $this->db->select('m.*, u.username, u.role as user_role');
        $this->db->from('mediators m');
        $this->db->join('users u', 'u.id = m.user_id', 'left');
        if (!empty($filter['jenis'])) $this->db->where('m.jenis', $filter['jenis']);
        if (!empty($filter['search'])) $this->db->like('m.nama', $filter['search']);
        return $this->db->limit($limit, $offset)->get()->result();
    }

    public function get_aktif() {
        return $this->db->where('is_active', 1)->get('mediators')->result();
    }

    public function get_all_aktif() {
        return $this->get_aktif();
    }

    public function get_by_id($id) {
        $this->db->select('m.*, u.username');
        $this->db->from('mediators m');
        $this->db->join('users u', 'u.id = m.user_id', 'left');
        $this->db->where('m.id', $id);
        return $this->db->get()->row();
    }

    public function count_all($filter = []) {
        if (!empty($filter['jenis'])) $this->db->where('jenis', $filter['jenis']);
        if (!empty($filter['search'])) $this->db->like('nama', $filter['search']);
        return $this->db->count_all_results('mediators');
    }

    public function insert($data) {
        return $this->db->insert('mediators', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('mediators', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('mediators');
    }

    public function toggle_aktif($id) {
        $mediator = $this->get_by_id($id);
        if (!$mediator) return false;
        return $this->db->where('id', $id)->update('mediators', ['is_active' => $mediator->is_active ? 0 : 1]);
    }
}
