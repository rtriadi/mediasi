<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_statistik — Model untuk dashboard pimpinan & statistik laporan mediasi
 */
class M_statistik extends CI_Model {

    public function get_summary($filter = []) {
        $this->db->select('h.status_hasil as hasil, h.status_hasil, COUNT(*) as total');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->_apply_filter($filter);
        $this->db->group_by('h.status_hasil');
        $rows = $this->db->get()->result();

        $result = ['berhasil' => 0, 'berhasil_seluruhnya' => 0, 'berhasil_sebagian' => 0, 'tidak_berhasil' => 0, 'tidak_dapat_dilaksanakan' => 0, 'total' => 0];
        foreach ($rows as $row) {
            $key = $row->status_hasil;
            if ($key === 'berhasil_seluruhnya') {
                $result['berhasil'] += (int)$row->total;
                $result['berhasil_seluruhnya'] = (int)$row->total;
            } else {
                $result[$key] = (int)$row->total;
            }
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
        $this->db->select('p.nomor_perkara, p.tgl_batas_mediasi, jp.nama as jenis_perkara, m.nama as mediator, h.status_hasil as hasil, h.status_hasil, h.created_at as tgl_hasil, h.tgl_laporan, h.file_laporan_pdf as file_laporan');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('mediators m', 'm.id = h.mediator_id', 'left');
        $this->_apply_filter($filter);
        return $this->db->order_by('h.created_at', 'DESC')->limit($limit, $offset)->get()->result();
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
            $this->db->where('MONTH(h.created_at)', $filter['bulan']);
            $this->db->where('YEAR(h.created_at)', $filter['tahun']);
        } elseif (!empty($filter['triwulan']) && !empty($filter['tahun'])) {
            $q = (int)$filter['triwulan'];
            $this->db->where('QUARTER(h.created_at)', $q);
            $this->db->where('YEAR(h.created_at)', $filter['tahun']);
        } elseif (!empty($filter['tahun'])) {
            $this->db->where('YEAR(h.created_at)', $filter['tahun']);
        }
    }

    /**
     * Tren mediasi bulanan — 12 bulan terakhir (untuk line/bar chart).
     * Returns array of: { bulan, label, berhasil, berhasil_sebagian, tidak_berhasil, total }
     */
    public function trend_bulanan($filter = []) {
        if (!is_array($filter)) {
            $filter = ['tahun' => $filter];
        }
        $this->db->select('MONTH(h.created_at) AS bulan, h.status_hasil as hasil, h.status_hasil, COUNT(*) AS total');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->_apply_filter($filter);
        $this->db->group_by(['MONTH(h.created_at)', 'h.status_hasil']);
        $this->db->order_by('bulan', 'ASC');
        $rows = $this->db->get()->result();

        $bln_labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[$m] = ['bulan' => $m, 'label' => $bln_labels[$m-1], 'berhasil' => 0, 'berhasil_seluruhnya' => 0, 'berhasil_sebagian' => 0, 'tidak_berhasil' => 0, 'tidak_dapat_dilaksanakan' => 0, 'total' => 0];
        }
        foreach ($rows as $r) {
            $m = (int)$r->bulan;
            $st = $r->status_hasil;
            if (isset($data[$m][$st])) {
                $data[$m][$st] += (int)$r->total;
            }
            if ($st === 'berhasil_seluruhnya') {
                $data[$m]['berhasil'] += (int)$r->total;
            }
            $data[$m]['total'] += (int)$r->total;
        }
        return array_values($data);
    }

    /**
     * Kinerja per-mediator — tingkat keberhasilan.
     * Returns array of: { mediator_id, nama, berhasil, berhasil_sebagian, tidak_berhasil, total, pct_berhasil }
     */
    public function kinerja_mediator($filter = []) {
        if (!is_array($filter)) {
            $filter = ['tahun' => $filter];
        }
        $this->db->select("
            m.id AS mediator_id,
            m.nama,
            SUM(IF(h.status_hasil IN ('berhasil', 'berhasil_seluruhnya'), 1, 0)) AS berhasil,
            SUM(IF(h.status_hasil = 'berhasil_sebagian', 1, 0))                   AS berhasil_sebagian,
            SUM(IF(h.status_hasil = 'tidak_berhasil', 1, 0))                      AS tidak_berhasil,
            SUM(IF(h.status_hasil = 'tidak_dapat_dilaksanakan', 1, 0))            AS tidak_dapat_dilaksanakan,
            COUNT(*)                                                               AS total
        ", false);
        $this->db->from('hasil_mediasi h');
        $this->db->join('mediators m', 'm.id = h.mediator_id');
        $this->db->join('perkara p', 'p.id = h.perkara_id', 'left');
        $this->_apply_filter($filter);
        $this->db->group_by(['m.id', 'm.nama']);
        $this->db->order_by('berhasil', 'DESC');
        $this->db->order_by('total', 'DESC');
        $rows = $this->db->get()->result();

        foreach ($rows as &$r) {
            $r->pct_berhasil = $r->total > 0 ? round(($r->berhasil / $r->total) * 100, 1) : 0;
        }
        return $rows;
    }

    /**
     * Distribusi jenis perkara — untuk pie/doughnut chart.
     * Returns array of: { jenis_perkara, total, pct }
     */
    public function distribusi_jenis($filter = []) {
        if (!is_array($filter)) {
            $filter = ['tahun' => $filter];
        }
        $this->db->select('jp.nama AS jenis_perkara, COUNT(*) AS total');
        $this->db->from('hasil_mediasi h');
        $this->db->join('perkara p', 'p.id = h.perkara_id');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id');
        $this->_apply_filter($filter);
        $this->db->group_by(['jp.id', 'jp.nama']);
        $this->db->order_by('total', 'DESC');
        $rows = $this->db->get()->result();

        $grand_total = array_sum(array_column((array)$rows, 'total'));
        foreach ($rows as &$r) {
            $r->pct = $grand_total > 0 ? round(($r->total / $grand_total) * 100, 1) : 0;
        }
        return $rows;
    }
}
