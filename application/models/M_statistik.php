<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_statistik — Model untuk dashboard pimpinan & statistik laporan mediasi
 */
class M_statistik extends CI_Model {

    public function get_summary($filter = []) {
        $this->db->select('h.hasil, COUNT(*) as total');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->_apply_filter($filter);
        $this->db->group_by('h.hasil');
        $rows = $this->db->get()->result();

        $result = ['berhasil' => 0, 'berhasil_sebagian' => 0, 'tidak_berhasil' => 0, 'total' => 0];
        foreach ($rows as $row) {
            $result[$row->hasil] = (int)$row->total;
            $result['total'] += (int)$row->total;
        }

        // Hitung juga total perkara terdaftar dalam periode tersebut
        $this->db->from('perkara p');
        if (!empty($filter['tahun'])) {
            $this->db->where('YEAR(p.created_at)', $filter['tahun']);
        }
        $result['total_perkara'] = $this->db->count_all_results();

        return $result;
    }

    public function get_detail($filter = [], $limit = 10, $offset = 0) {
        $this->db->select('p.nomor_perkara, p.tgl_batas_mediasi, jp.nama as jenis_perkara, m.nama as mediator, h.hasil, h.tgl_hasil, h.file_laporan');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('mediators m', 'm.id = h.mediator_id', 'left');
        $this->_apply_filter($filter);
        return $this->db->order_by('h.tgl_hasil', 'DESC')->limit($limit, $offset)->get()->result();
    }

    public function count_detail($filter = []) {
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->_apply_filter($filter);
        return $this->db->count_all_results();
    }

    private function _apply_filter($filter) {
        if (!empty($filter['mediator_id'])) {
            $this->db->where('h.mediator_id', $filter['mediator_id']);
        }
        if (!empty($filter['bulan']) && !empty($filter['tahun'])) {
            $this->db->where('MONTH(h.tgl_hasil)', $filter['bulan']);
            $this->db->where('YEAR(h.tgl_hasil)', $filter['tahun']);
        } elseif (!empty($filter['triwulan']) && !empty($filter['tahun'])) {
            $q = (int)$filter['triwulan'];
            $this->db->where('QUARTER(h.tgl_hasil)', $q);
            $this->db->where('YEAR(h.tgl_hasil)', $filter['tahun']);
        } elseif (!empty($filter['tahun'])) {
            $this->db->where('YEAR(h.tgl_hasil)', $filter['tahun']);
        }
    }

    /**
     * Tren mediasi bulanan — 12 bulan terakhir (untuk line/bar chart).
     * Returns array of: { bulan, label, berhasil, berhasil_sebagian, tidak_berhasil, total }
     */
    public function trend_bulanan($tahun = null) {
        $tahun = $tahun ?: date('Y');
        $sql = "
            SELECT
                MONTH(h.tgl_hasil)  AS bulan,
                h.hasil,
                COUNT(*)             AS total
            FROM hasil_mediasi h
            JOIN perkara p ON p.id = h.perkara_id
            WHERE YEAR(h.tgl_hasil) = ?
            GROUP BY MONTH(h.tgl_hasil), h.hasil
            ORDER BY bulan ASC
        ";
        $rows = $this->db->query($sql, [$tahun])->result();

        $bln_labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[$m] = ['bulan' => $m, 'label' => $bln_labels[$m-1], 'berhasil' => 0, 'berhasil_sebagian' => 0, 'tidak_berhasil' => 0, 'total' => 0];
        }
        foreach ($rows as $r) {
            $m = (int)$r->bulan;
            if (isset($data[$m][$r->hasil])) {
                $data[$m][$r->hasil] += (int)$r->total;
            }
            $data[$m]['total'] += (int)$r->total;
        }
        return array_values($data);
    }

    /**
     * Kinerja per-mediator — tingkat keberhasilan.
     * Returns array of: { mediator_id, nama, berhasil, berhasil_sebagian, tidak_berhasil, total, pct_berhasil }
     */
    public function kinerja_mediator($tahun = null) {
        $tahun = $tahun ?: date('Y');
        $sql = "
            SELECT
                m.id AS mediator_id,
                m.nama,
                SUM(IF(h.hasil = 'berhasil', 1, 0))          AS berhasil,
                SUM(IF(h.hasil = 'berhasil_sebagian', 1, 0)) AS berhasil_sebagian,
                SUM(IF(h.hasil = 'tidak_berhasil', 1, 0))    AS tidak_berhasil,
                COUNT(*)                                       AS total
            FROM hasil_mediasi h
            JOIN mediators m ON m.id = h.mediator_id
            WHERE YEAR(h.tgl_hasil) = ?
            GROUP BY m.id, m.nama
            ORDER BY berhasil DESC, total DESC
        ";
        $rows = $this->db->query($sql, [$tahun])->result();
        foreach ($rows as &$r) {
            $r->pct_berhasil = $r->total > 0 ? round(($r->berhasil / $r->total) * 100, 1) : 0;
        }
        return $rows;
    }

    /**
     * Distribusi jenis perkara — untuk pie/doughnut chart.
     * Returns array of: { jenis_perkara, total, pct }
     */
    public function distribusi_jenis($tahun = null) {
        $tahun = $tahun ?: date('Y');
        $sql = "
            SELECT
                jp.nama AS jenis_perkara,
                COUNT(*) AS total
            FROM hasil_mediasi h
            JOIN perkara p ON p.id = h.perkara_id
            JOIN jenis_perkara jp ON jp.id = p.jenis_perkara_id
            WHERE YEAR(h.tgl_hasil) = ?
            GROUP BY jp.id, jp.nama
            ORDER BY total DESC
        ";
        $rows = $this->db->query($sql, [$tahun])->result();
        $grand_total = array_sum(array_column((array)$rows, 'total'));
        foreach ($rows as &$r) {
            $r->pct = $grand_total > 0 ? round(($r->total / $grand_total) * 100, 1) : 0;
        }
        return $rows;
    }
}
