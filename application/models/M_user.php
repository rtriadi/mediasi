<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_user — Model untuk tabel users
 */
class M_user extends CI_Model {

    public function get_by_username($username) {
        return $this->db->get_where('users', ['username' => $username, 'is_active' => 1])->row();
    }

    public function get_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    /**
     * Cek apakah user adalah mediator aktif.
     * Return: mediator_id (int) jika iya, false jika tidak.
     */
    public function is_mediator($user_id) {
        $result = $this->db->get_where('mediators', ['user_id' => $user_id, 'is_active' => 1])->row();
        return $result ? $result->id : false;
    }

    public function get_all($filter = [], $limit = 10, $offset = 0) {
        if (!empty($filter['role'])) $this->db->where('role', $filter['role']);
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('nama', $filter['search'])
                ->or_like('username', $filter['search'])
                ->group_end();
        }
        $this->db->where('role !=', 'admin');
        return $this->db->limit($limit, $offset)->get('users')->result();
    }

    public function count_all($filter = []) {
        if (!empty($filter['role'])) $this->db->where('role', $filter['role']);
        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('nama', $filter['search'])
                ->or_like('username', $filter['search'])
                ->group_end();
        }
        $this->db->where('role !=', 'admin');
        return $this->db->count_all_results('users');
    }

    public function insert($data) {
        return $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('users');
    }

    public function toggle_aktif($id) {
        $user = $this->get_by_id($id);
        if (!$user) return false;
        return $this->db->where('id', $id)->update('users', ['is_active' => $user->is_active ? 0 : 1]);
    }

    /** Untuk dropdown: user aktif yang belum terdaftar sebagai mediator */
    public function get_for_mediator_link() {
        $this->db->select('u.id, u.nama, u.username, u.role');
        $this->db->from('users u');
        $this->db->join('mediators m', 'm.user_id = u.id', 'left');
        $this->db->where('u.is_active', 1);
        $this->db->where('m.id IS NULL', null, false);
        $this->db->where_in('u.role', ['hakim', 'mediator']);
        return $this->db->get()->result();
    }

    /** Cek apakah username sudah dipakai (exclude id tertentu) */
    public function is_username_taken($username, $exclude_id = null) {
        $this->db->where('username', $username);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('users') > 0;
    }

    /** Ambil semua user aktif yang memiliki role hakim */
    public function get_hakim() {
        $this->db->where('is_active', 1);
        $this->db->group_start();
        $this->db->where('role', 'hakim');
        $this->db->or_like('role', 'hakim');
        $this->db->group_end();
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('users')->result();
    }
}

