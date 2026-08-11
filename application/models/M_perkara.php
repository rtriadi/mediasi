<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_perkara — Model untuk perkara & perkara_pihak & perkara_mediator
 */
class M_perkara extends CI_Model {

    public function insert($data) {
        $this->db->insert('perkara', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('perkara', $data);
    }

    public function insert_pihak($data_array) {
        return $this->db->insert_batch('perkara_pihak', $data_array);
    }

    public function assign_mediator($data) {
        $perkara_id  = $data['perkara_id'];
        $mediator_id = $data['mediator_id'];
        $assigned_by = $data['assigned_by'] ?? null;

        // Nonaktifkan penugasan mediator yang sedang aktif (histori)
        $this->db->where('perkara_id', $perkara_id)
                 ->where('is_active', 1)
                 ->update('perkara_mediator', [
                     'is_active'          => 0,
                     'alasan_penggantian' => $data['alasan_penggantian'] ?? null,
                 ]);

        // Insert penugasan baru
        $this->db->insert('perkara_mediator', [
            'perkara_id'    => $perkara_id,
            'mediator_id'   => $mediator_id,
            'tgl_penetapan' => date('Y-m-d'),
            'status_mediator' => 'N',
            'is_active'     => 1,
        ]);

        // Record log (jika tabel ada)
        $log_data = [
            'perkara_id'  => $perkara_id,
            'mediator_id' => $mediator_id,
            'tgl_assign'  => date('Y-m-d H:i:s'),
        ];
        if ($assigned_by) $log_data['assigned_by'] = $assigned_by;
        if ($this->db->table_exists('perkara_mediator_log')) {
            $this->db->insert('perkara_mediator_log', $log_data);
        }

        return true;
    }

    public function get_riwayat_mediator($perkara_id) {
        $this->db->select('pml.*, m.nama as nama_mediator, m.jenis as jenis_mediator, u1.nama as nama_assigned_by, u2.nama as nama_diganti_oleh');
        $this->db->from('perkara_mediator_log pml');
        $this->db->join('mediators m', 'm.id = pml.mediator_id', 'left');
        $this->db->join('users u1', 'u1.id = pml.assigned_by', 'left');
        $this->db->join('users u2', 'u2.id = pml.diganti_oleh', 'left');
        $this->db->where('pml.perkara_id', $perkara_id);
        $this->db->order_by('pml.id', 'ASC');
        return $this->db->get()->result();
    }


    public function get_pihak($perkara_id) {
        return $this->db->where('perkara_id', $perkara_id)->order_by('jenis_pihak, urutan')->get('perkara_pihak')->result();
    }

    public function get_kuasa($perkara_id) {
        $this->db->select('pk.*, pp.nama as nama_pihak, pp.jenis_pihak');
        $this->db->from('perkara_kuasa pk');
        $this->db->join('perkara_pihak pp', 'pp.id = pk.pihak_id', 'left');
        $this->db->where('pk.perkara_id', $perkara_id);
        return $this->db->get()->result();
    }

    public function get_all_by_pp($pp_id, $filter = [], $limit = 10, $offset = 0) {
        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, h.status_hasil');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->where('p.pp_id', $pp_id);
        if (!empty($filter['status'])) $this->db->where('p.status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('p.nomor_perkara', $filter['search']);
        return $this->db->limit($limit, $offset)->order_by('p.created_at', 'DESC')->get()->result();
    }

    public function count_by_pp($pp_id, $filter = []) {
        $this->db->where('pp_id', $pp_id);
        if (!empty($filter['status'])) $this->db->where('status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('nomor_perkara', $filter['search']);
        return $this->db->count_all_results('perkara');
    }

    public function get_by_id($id) {
        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, m.jenis as jenis_mediator, m.id as mediator_id, u.nama as nama_pp');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('users u', 'u.id = p.pp_id', 'left');
        $this->db->where('p.id', $id);
        return $this->db->get()->row();
    }

    public function get_all($filter = [], $limit = 10, $offset = 0) {
        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, h.status_hasil, u.nama as nama_pp');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->join('users u', 'u.id = p.pp_id', 'left');
        if (!empty($filter['status'])) $this->db->where('p.status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('p.nomor_perkara', $filter['search']);
        if (!empty($filter['mediator_id'])) $this->db->where('pm.mediator_id', $filter['mediator_id']);
        if (!empty($filter['hakim_id_sipp'])) {
            $id_sipp = $this->db->escape_str($filter['hakim_id_sipp']);
            $this->db->where("FIND_IN_SET('{$id_sipp}', p.majelis_id) > 0");
        }
        return $this->db->limit($limit, $offset)->order_by('p.created_at', 'DESC')->get()->result();
    }

    public function count_all($filter = []) {
        $this->db->from('perkara p');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        if (!empty($filter['status'])) $this->db->where('p.status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('p.nomor_perkara', $filter['search']);
        if (!empty($filter['mediator_id'])) $this->db->where('pm.mediator_id', $filter['mediator_id']);
        if (!empty($filter['hakim_id_sipp'])) {
            $id_sipp = $this->db->escape_str($filter['hakim_id_sipp']);
            $this->db->where("FIND_IN_SET('{$id_sipp}', p.majelis_id) > 0");
        }
        return $this->db->count_all_results();
    }

    public function get_by_mediator($mediator_id, $filter = [], $limit = 10, $offset = 0) {
        $this->db->select('p.*, jp.nama as jenis_perkara, h.status_hasil');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->where('pm.mediator_id', $mediator_id);
        if (!empty($filter['status'])) $this->db->where('p.status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('p.nomor_perkara', $filter['search']);
        return $this->db->limit($limit, $offset)->order_by('p.created_at', 'DESC')->get()->result();
    }

    public function count_by_mediator($mediator_id, $filter = []) {
        $this->db->from('perkara p');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id');
        $this->db->where('pm.mediator_id', $mediator_id);
        if (!empty($filter['status'])) $this->db->where('p.status', $filter['status']);
        if (!empty($filter['search'])) $this->db->like('p.nomor_perkara', $filter['search']);
        return $this->db->count_all_results();
    }
}
