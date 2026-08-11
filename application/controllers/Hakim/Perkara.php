<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perkara — Controller Hakim untuk melihat seluruh perkara & download laporan
 */
class Perkara extends MY_Controller {

    protected $role_required = ['hakim', 'admin'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_perkara', 'M_jadwal', 'M_hasil']);
    }

    public function index() {
        $hakim_id_sipp = $this->session->userdata('id_sipp');
        $user_roles    = $this->session->userdata('roles') ?: [$this->session->userdata('role')];
        $is_admin      = in_array('admin', $user_roles);

        $filter = [
            'status'         => $this->input->get('status'),
            'search'         => $this->input->get('search'),
            'hakim_id_sipp'  => $is_admin ? null : $hakim_id_sipp,
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total    = $this->M_perkara->count_all($filter);
        $perkaras = $this->M_perkara->get_all($filter, $limit, $offset);

        $this->render('hakim/perkara/index', [
            'title'         => 'Monitoring Perkara Mediasi',
            'perkaras'      => $perkaras,
            'total'         => $total,
            'filter'        => $filter,
            'page'          => $page,
            'pagination'    => $this->paginate('hakim/perkara', $total, $limit, $filter),
            'is_admin'      => $is_admin,
        ]);
    }

    public function detail($id) {
        $perkara = $this->M_perkara->get_by_id($id);
        if (!$perkara) show_404();

        $pihak  = $this->M_perkara->get_pihak($id);
        $jadwal = $this->M_jadwal->get_by_perkara($id);
        $hasil  = $this->M_hasil->get_by_perkara($id);

        $riwayat_mediator = $this->M_perkara->get_riwayat_mediator($id);

        $this->render('hakim/perkara/detail', [
            'title'            => "Detail Perkara {$perkara->nomor_perkara}",
            'perkara'          => $perkara,
            'pihak'            => $pihak,
            'jadwal'           => $jadwal,
            'hasil'            => $hasil,
            'riwayat_mediator' => $riwayat_mediator,
        ]);
    }

    public function download_laporan($perkara_id) {
        $hasil = $this->M_hasil->get_by_perkara($perkara_id);
        if (!$hasil || !$hasil->file_laporan_pdf) show_404();

        $file_path = FCPATH . 'uploads/laporan/' . $hasil->file_laporan_pdf;
        if (!file_exists($file_path)) show_error('File laporan tidak ditemukan.', 404);

        $this->load->helper('download');
        force_download($file_path, NULL);
    }
}
