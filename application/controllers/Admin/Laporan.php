<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Laporan — Controller Admin & Pimpinan untuk rekapitulasi & ekspor laporan mediasi (Excel & PDF)
 */
class Laporan extends MY_Controller {

    protected $role_required = ['admin', 'pimpinan', 'hakim', 'pp'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_perkara', 'M_mediator']);
    }

    public function index() {
        $bulan = (int)($this->input->get('bulan') ?: date('m'));
        $tahun = (int)($this->input->get('tahun') ?: date('Y'));

        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, h.status_hasil as hasil, h.status_hasil, h.tgl_laporan, h.created_at as tgl_hasil');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->where('MONTH(p.created_at)', $bulan);
        $this->db->where('YEAR(p.created_at)', $tahun);
        $perkaras = $this->db->order_by('p.created_at', 'ASC')->get()->result();

        // Hitung data statistik
        $stat = [
            'total'             => count($perkaras),
            'berhasil'          => 0,
            'berhasil_sebagian' => 0,
            'tidak_berhasil'    => 0,
            'proses'            => 0,
            'menunggu'          => 0,
        ];

        foreach ($perkaras as $p) {
            if ($p->status === 'selesai') {
                if (in_array($p->hasil, ['berhasil', 'berhasil_seluruhnya'])) $stat['berhasil']++;
                elseif ($p->hasil === 'berhasil_sebagian') $stat['berhasil_sebagian']++;
                else $stat['tidak_berhasil']++;
            } elseif ($p->status === 'proses') {
                $stat['proses']++;
            } else {
                $stat['menunggu']++;
            }
        }

        $total_selesai = $stat['berhasil'] + $stat['berhasil_sebagian'] + $stat['tidak_berhasil'];
        $stat['persen_berhasil'] = $total_selesai > 0 ? round((($stat['berhasil'] + $stat['berhasil_sebagian']) / $total_selesai) * 100, 1) : 0;

        $this->render('admin/laporan/index', [
            'title'    => 'Laporan Rekapitulasi Mediasi',
            'bulan'    => $bulan,
            'tahun'    => $tahun,
            'perkaras' => $perkaras,
            'stat'     => $stat,
        ]);
    }

    public function export_excel() {
        $bulan = (int)($this->input->get('bulan') ?: date('m'));
        $tahun = (int)($this->input->get('tahun') ?: date('Y'));

        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, h.status_hasil as hasil, h.status_hasil, h.tgl_laporan, h.created_at as tgl_hasil');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->where('MONTH(p.created_at)', $bulan);
        $this->db->where('YEAR(p.created_at)', $tahun);
        $perkaras = $this->db->order_by('p.created_at', 'ASC')->get()->result();

        $bln_nama = bulan_indo($bulan);
        $filename = "Laporan_Mediasi_{$bln_nama}_{$tahun}.xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Cache-Control: max-age=0");

        $this->load->view('admin/laporan/export_excel', [
            'bulan'    => $bulan,
            'tahun'    => $tahun,
            'perkaras' => $perkaras,
        ]);
    }

    public function cetak_pdf() {
        $bulan = (int)($this->input->get('bulan') ?: date('m'));
        $tahun = (int)($this->input->get('tahun') ?: date('Y'));

        $this->db->select('p.*, jp.nama as jenis_perkara, m.nama as nama_mediator, h.status_hasil as hasil, h.status_hasil, h.tgl_laporan, h.created_at as tgl_hasil');
        $this->db->from('perkara p');
        $this->db->join('jenis_perkara jp', 'jp.id = p.jenis_perkara_id', 'left');
        $this->db->join('perkara_mediator pm', 'pm.perkara_id = p.id AND pm.is_active = 1', 'left');
        $this->db->join('mediators m', 'm.id = pm.mediator_id', 'left');
        $this->db->join('hasil_mediasi h', 'h.perkara_id = p.id', 'left');
        $this->db->where('MONTH(p.created_at)', $bulan);
        $this->db->where('YEAR(p.created_at)', $tahun);
        $perkaras = $this->db->order_by('p.created_at', 'ASC')->get()->result();

        $this->load->view('admin/laporan/cetak_pdf', [
            'title'    => "Laporan Bulanan Mediasi — {$bulan}/{$tahun}",
            'bulan'    => $bulan,
            'tahun'    => $tahun,
            'perkaras' => $perkaras,
        ]);
    }
}
