<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Publik — Controller halaman publik (tanpa login) untuk cek jadwal mediasi pihak
 */
class Publik extends CI_Controller {

    public function index() {
        $this->load->view('layouts/publik', [
            'content_view' => 'publik/index',
            'title'        => 'Cek Jadwal Mediasi — PA Gorontalo',
        ]);
    }

    public function cari() {
        $nomor = trim($this->input->post('nomor_perkara', true) ?: $this->input->get('nomor_perkara', true));
        if (!$nomor) {
            redirect('publik');
            return;
        }

        $this->load->model(['M_perkara', 'M_jadwal', 'M_hasil']);
        $perkara = $this->db->where('nomor_perkara', $nomor)->get('perkara')->row();

        $data = [
            'title'   => "Hasil Pencarian Jadwal — {$nomor}",
            'nomor'   => $nomor,
            'perkara' => null,
        ];

        if ($perkara) {
            $data['perkara'] = $this->M_perkara->get_by_id($perkara->id);
            $data['pihak']   = $this->M_perkara->get_pihak($perkara->id);
            $data['jadwal']  = $this->M_jadwal->get_by_perkara($perkara->id);
            $data['hasil']   = $this->M_hasil->get_by_perkara($perkara->id);
        }

        $this->load->view('layouts/publik', array_merge($data, ['content_view' => 'publik/result']));
    }
}
