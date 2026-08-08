<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_jadwal — Model untuk sesi_mediasi & room conflict check
 */
class M_jadwal extends CI_Model {

    /**
     * Cek konflik ruangan.
     * Return: row sesi yang bentrok, atau null jika aman.
     */
    public function check_conflict($ruangan_id, $tgl, $jam_mulai, $jam_selesai, $exclude_id = null) {
        $this->db->select('s.*, p.nomor_perkara');
        $this->db->from('sesi_mediasi s');
        $this->db->join('perkara p', 'p.id = s.perkara_id');
        $this->db->where('s.ruangan_id', $ruangan_id);
        $this->db->where('s.tgl_mediasi', $tgl);
        $this->db->where('s.status_sesi !=', 'batal');
        $this->db->where('s.jam_mulai <', $jam_selesai);
        $this->db->where('s.jam_selesai >', $jam_mulai);
        if ($exclude_id) $this->db->where('s.id !=', $exclude_id);
        return $this->db->get()->row();
    }

    public function insert($data) {
        $this->db->insert('sesi_mediasi', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('sesi_mediasi', $data);
    }

    public function get_by_mediator($mediator_id, $filter = [], $limit = 10, $offset = 0) {
        $this->db->select('s.*, p.nomor_perkara, p.tgl_batas_mediasi, r.nama_ruangan');
        $this->db->from('sesi_mediasi s');
        $this->db->join('perkara p', 'p.id = s.perkara_id');
        $this->db->join('ruangan r', 'r.id = s.ruangan_id', 'left');
        $this->db->where('s.mediator_id', $mediator_id);
        
        $this->_apply_filter($filter);

        return $this->db->order_by('s.tgl_mediasi', 'ASC')->limit($limit, $offset)->get()->result();
    }

    public function count_by_mediator($mediator_id, $filter = []) {
        $this->db->from('sesi_mediasi s');
        $this->db->join('perkara p', 'p.id = s.perkara_id', 'left');
        $this->db->where('s.mediator_id', $mediator_id);

        $this->_apply_filter($filter);

        return $this->db->count_all_results();
    }

    private function _apply_filter($filter) {
        if (!empty($filter['bulan'])) $this->db->where('MONTH(s.tgl_mediasi)', $filter['bulan']);
        if (!empty($filter['tahun'])) $this->db->where('YEAR(s.tgl_mediasi)', $filter['tahun']);
        if (!empty($filter['search'])) {
            $s = $this->db->escape_like_str($filter['search']);
            $this->db->group_start();
            $this->db->like('p.nomor_perkara', $s);
            $this->db->or_like('s.keterangan', $s);
            $this->db->or_like('s.tempat_lain', $s);
            $this->db->group_end();
        }
    }

    public function get_by_perkara($perkara_id) {
        $this->db->select('s.*, r.nama_ruangan');
        $this->db->from('sesi_mediasi s');
        $this->db->join('ruangan r', 'r.id = s.ruangan_id', 'left');
        $this->db->where('s.perkara_id', $perkara_id);
        $sesi_list = $this->db->order_by('s.tgl_mediasi', 'ASC')->get()->result();

        foreach ($sesi_list as &$s) {
            $s->kehadiran = $this->get_kehadiran($s->id);
        }
        return $sesi_list;
    }

    /**
     * Cek apakah ada sesi yang masih 'terjadwal' (belum selesai) untuk perkara ini.
     */
    public function get_unfinished_session($perkara_id) {
        return $this->db->where('perkara_id', $perkara_id)
                        ->where('status_sesi', 'terjadwal')
                        ->order_by('id', 'DESC')
                        ->get('sesi_mediasi')
                        ->row();
    }

    /**
     * Ambil data kehadiran pihak untuk suatu sesi.
     */
    public function get_kehadiran($sesi_id) {
        $this->db->select('sk.*, pp.nama as nama_pihak, pp.jenis as jenis_pihak, pp.kuasa_hukum');
        $this->db->from('sesi_kehadiran sk');
        $this->db->join('perkara_pihak pp', 'pp.id = sk.pihak_id');
        $this->db->where('sk.sesi_id', $sesi_id);
        $this->db->order_by('pp.jenis, pp.urutan');
        return $this->db->get()->result();
    }

    /**
     * Simpan data kehadiran & selesaikan sesi.
     */
    public function selesaikan_sesi($sesi_id, $catatan_sesi, $kehadiran_batch) {
        $this->db->where('id', $sesi_id)->update('sesi_mediasi', [
            'status_sesi'  => 'selesai',
            'catatan_sesi' => $catatan_sesi,
        ]);

        if (!empty($kehadiran_batch)) {
            foreach ($kehadiran_batch as $kh) {
                $this->db->replace('sesi_kehadiran', [
                    'sesi_id'          => $sesi_id,
                    'pihak_id'         => $kh['pihak_id'],
                    'status_kehadiran' => $kh['status_kehadiran'],
                    'catatan'          => $kh['catatan'] ?? null,
                ]);
            }
        }
        return true;
    }

    /**
     * Reschedule sesi: tandai sesi lama sebagai 'dijadwal_ulang', insert jadwal baru.
     * Mengembalikan ID sesi baru.
     */
    public function reschedule($sesi_id, $alasan, $data_baru) {
        // Update sesi lama
        $this->db->where('id', $sesi_id)->update('sesi_mediasi', [
            'status_sesi'       => 'dijadwal_ulang',
            'alasan_reschedule' => $alasan,
        ]);
        // Insert sesi baru
        $this->db->insert('sesi_mediasi', $data_baru);
        return $this->db->insert_id();
    }

    /**
     * Batalkan sesi mediasi.
     */
    public function batal($sesi_id, $alasan) {
        return $this->db->where('id', $sesi_id)->update('sesi_mediasi', [
            'status_sesi'       => 'batal',
            'alasan_reschedule' => $alasan,
        ]);
    }

    /**
     * Ambil satu sesi berdasarkan ID.
     */
    public function get_by_id($id) {
        $this->db->select('s.*, r.nama_ruangan, p.nomor_perkara, p.jenis_perkara_id');
        $this->db->from('sesi_mediasi s');
        $this->db->join('ruangan r', 'r.id = s.ruangan_id', 'left');
        $this->db->join('perkara p', 'p.id = s.perkara_id');
        $this->db->where('s.id', $id);
        return $this->db->get()->row();
    }
}

