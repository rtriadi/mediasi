<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard — Controller Pimpinan untuk statistik mediasi & export
 */
class Dashboard extends MY_Controller {

    protected $role_required = ['pimpinan', 'admin', 'hakim'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_statistik', 'M_mediator']);
    }

    private function _get_filter() {
        return [
            'mediator_id' => $this->input->get('mediator_id'),
            'bulan'       => $this->input->get('bulan'),
            'triwulan'    => $this->input->get('triwulan'),
            'tahun'       => $this->input->get('tahun') ?: date('Y'),
        ];
    }

    public function index() {
        $filter = $this->_get_filter();
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $summary = $this->M_statistik->get_summary($filter);
        $total   = $this->M_statistik->count_detail($filter);
        $detail  = $this->M_statistik->get_detail($filter, $limit, $offset);

        $mediators = $this->M_mediator->get_aktif();

        // Data Analitik untuk 3 grafik baru
        $tahun_analitik = $filter['tahun'];
        $trend_bulanan   = $this->M_statistik->trend_bulanan($tahun_analitik);
        $kinerja_mediator= $this->M_statistik->kinerja_mediator($tahun_analitik);
        $distribusi_jenis= $this->M_statistik->distribusi_jenis($tahun_analitik);

        $this->render('pimpinan/dashboard/index', [
            'title'             => 'Dashboard & Laporan Statistik Mediasi',
            'summary'           => $summary,
            'detail'            => $detail,
            'total'             => $total,
            'filter'            => $filter,
            'page'              => $page,
            'pagination'        => $this->paginate('pimpinan/dashboard', $total, $limit, $filter),
            'mediators'         => $mediators,
            'trend_bulanan'     => $trend_bulanan,
            'kinerja_mediator'  => $kinerja_mediator,
            'distribusi_jenis'  => $distribusi_jenis,
        ]);
    }

    public function export_pdf() {
        $filter  = $this->_get_filter();
        $summary = $this->M_statistik->get_summary($filter);
        $detail  = $this->M_statistik->get_detail($filter, 9999, 0);

        $html = $this->load->view('pimpinan/dashboard/export_pdf', [
            'summary' => $summary,
            'detail'  => $detail,
            'filter'  => $filter,
        ], TRUE);

        // Simple inline PDF / printable view fallback if DomPDF not loaded via composer
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream('laporan-mediasi-' . date('Ymd') . '.pdf', ['Attachment' => 0]);
        } else {
            // Render directly as printable HTML page
            echo $html;
        }
    }

    public function export_excel() {
        $filter  = $this->_get_filter();
        $summary = $this->M_statistik->get_summary($filter);
        $detail  = $this->M_statistik->get_detail($filter, 9999, 0);

        $satker   = get_app_setting('nama_satker', 'PA Gorontalo');
        $app_name = get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');
        $total          = (int)$summary['total'];
        $total_berhasil = $summary['berhasil'] + $summary['berhasil_sebagian'];
        $pct_b          = $total > 0 ? round(($total_berhasil / $total) * 100, 1) : 0;

        // Label periode
        $bln_list = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $periode  = !empty($filter['bulan']) ? ($bln_list[(int)$filter['bulan']] . ' ' . $filter['tahun']) : 'Tahun ' . $filter['tahun'];

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment;filename="laporan_mediasi_' . preg_replace('/\s+/', '_', $satker) . '_' . date('Ymd') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<html><head><meta charset='UTF-8'></head><body>";
        echo "<table border='1' style='border-collapse:collapse; font-family:Arial; font-size:11px;'>";

        // Header
        echo "<tr><td colspan='8' style='background:#1a237e; color:#fff; font-weight:bold; font-size:13px; padding:8px;'>LAPORAN REKAPITULASI HASIL MEDIASI PERKARA</td></tr>";
        echo "<tr><td colspan='8' style='background:#283593; color:#fff; padding:5px;'>{$satker} — {$app_name}</td></tr>";
        echo "<tr><td colspan='8' style='padding:5px;'>Periode: {$periode} &nbsp;|&nbsp; Dicetak: " . date('d/m/Y H:i') . " WITA</td></tr>";
        echo "<tr><td colspan='8'></td></tr>";

        // Ringkasan statistik
        echo "<tr>
            <td colspan='2' style='background:#e8f5e9; font-weight:bold; padding:6px;'>Total Mediasi Selesai</td>
            <td style='background:#e8f5e9; text-align:center; font-weight:bold; font-size:14px;'>{$total}</td>
            <td colspan='2' style='background:#e8f5e9; padding:6px;'>Berhasil Sepenuhnya: <b>{$summary['berhasil']}</b> ({$pct_b}%)</td>
            <td style='background:#fff9c4; padding:6px;'>Berhasil Sebagian: <b>{$summary['berhasil_sebagian']}</b></td>
            <td colspan='2' style='background:#fce4e4; padding:6px;'>Tidak Berhasil: <b>{$summary['tidak_berhasil']}</b></td>
        </tr>";
        echo "<tr><td colspan='8'></td></tr>";

        // Header kolom
        echo "<tr style='background:#e8eaf6; font-weight:bold;'>
            <th style='padding:6px 8px;'>No</th>
            <th style='padding:6px 8px;'>Nomor Perkara</th>
            <th style='padding:6px 8px;'>Jenis Perkara</th>
            <th style='padding:6px 8px;'>Mediator</th>
            <th style='padding:6px 8px;'>Hasil Mediasi</th>
            <th style='padding:6px 8px;'>Batas Mediasi</th>
            <th style='padding:6px 8px;'>Tgl Selesai</th>
            <th style='padding:6px 8px;'>File Laporan</th>
        </tr>";

        $no = 1;
        foreach ($detail as $d) {
            $hasil_text = ucwords(str_replace('_', ' ', $d->hasil));
            $batas = $d->tgl_batas_mediasi ? date('d/m/Y', strtotime($d->tgl_batas_mediasi)) : '-';
            $tgl_hasil = date('d/m/Y', strtotime($d->tgl_hasil));
            $file_link = !empty($d->file_laporan) ? 'Ada' : '-';

            // Warna baris per hasil
            $bg = ($d->hasil === 'berhasil') ? '#f1f8e9' : (($d->hasil === 'berhasil_sebagian') ? '#fffde7' : '#fce4e4');
            echo "<tr style='background:{$bg};'>";
            echo "<td style='text-align:center; padding:5px;'>{$no}</td>";
            echo "<td style='font-weight:bold; padding:5px;'>{$d->nomor_perkara}</td>";
            echo "<td style='padding:5px;'>{$d->jenis_perkara}</td>";
            echo "<td style='padding:5px;'>{$d->mediator}</td>";
            echo "<td style='text-align:center; font-weight:bold; padding:5px;'>{$hasil_text}</td>";
            echo "<td style='text-align:center; padding:5px;'>{$batas}</td>";
            echo "<td style='text-align:center; padding:5px;'>{$tgl_hasil}</td>";
            echo "<td style='text-align:center; padding:5px;'>{$file_link}</td>";
            echo "</tr>";
            $no++;
        }
        echo "</table></body></html>";
    }
}
