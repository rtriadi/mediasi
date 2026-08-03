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
        $filter = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total    = $this->M_perkara->count_all($filter);
        $perkaras = $this->M_perkara->get_all($filter, $limit, $offset);

        $this->load->library('pagination');
        $this->pagination->initialize([
            'base_url'         => site_url('hakim/perkara') . '?' . http_build_query(array_filter($filter)) . '&',
            'total_rows'       => $total,
            'per_page'         => $limit,
            'use_page_numbers' => TRUE,
            'cur_tag_open'     => '<strong>',
            'cur_tag_close'    => '</strong>',
        ]);

        $this->render('hakim/perkara/index', [
            'title'      => 'Semua Perkara Mediasi (Hakim)',
            'perkaras'   => $perkaras,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->pagination->create_links(),
        ]);
    }

    public function detail($id) {
        $perkara = $this->M_perkara->get_by_id($id);
        if (!$perkara) show_404();

        $pihak  = $this->M_perkara->get_pihak($id);
        $jadwal = $this->M_jadwal->get_by_perkara($id);
        $hasil  = $this->M_hasil->get_by_perkara($id);

        $this->render('hakim/perkara/detail', [
            'title'   => "Detail Perkara {$perkara->nomor_perkara}",
            'perkara' => $perkara,
            'pihak'   => $pihak,
            'jadwal'  => $jadwal,
            'hasil'   => $hasil,
        ]);
    }

    public function download_laporan($perkara_id) {
        $hasil = $this->M_hasil->get_by_perkara($perkara_id);
        if (!$hasil || !$hasil->file_laporan) show_404();

        $file_path = FCPATH . 'uploads/laporan/' . $hasil->file_laporan;
        if (!file_exists($file_path)) show_error('File laporan tidak ditemukan.', 404);

        $this->load->helper('download');
        force_download($file_path, NULL);
    }
}
