<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifikasi_log — Admin controller untuk memantau status pengiriman email/WA & Kirim Ulang
 */
class Notifikasi_log extends MY_Controller {

    protected $role_required = 'admin';

    public function index() {
        $filter = [
            'status' => $this->input->get('status'),
            'jenis'  => $this->input->get('jenis'),
            'search' => $this->input->get('search'),
        ];

        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $this->db->from('log_notifikasi l');
        $this->db->join('perkara p', 'p.id = l.perkara_id', 'left');

        if (!empty($filter['status'])) $this->db->where('l.status', $filter['status']);
        if (!empty($filter['jenis']))  $this->db->where('l.jenis', $filter['jenis']);
        if (!empty($filter['search'])) {
            $s = $this->db->escape_like_str($filter['search']);
            $this->db->group_start();
            $this->db->like('l.penerima', $s);
            $this->db->or_like('l.subjek', $s);
            $this->db->or_like('p.nomor_perkara', $s);
            $this->db->group_end();
        }

        $total = $this->db->count_all_results();

        $this->db->select('l.*, p.nomor_perkara');
        $this->db->from('log_notifikasi l');
        $this->db->join('perkara p', 'p.id = l.perkara_id', 'left');

        if (!empty($filter['status'])) $this->db->where('l.status', $filter['status']);
        if (!empty($filter['jenis']))  $this->db->where('l.jenis', $filter['jenis']);
        if (!empty($filter['search'])) {
            $s = $this->db->escape_like_str($filter['search']);
            $this->db->group_start();
            $this->db->like('l.penerima', $s);
            $this->db->or_like('l.subjek', $s);
            $this->db->or_like('p.nomor_perkara', $s);
            $this->db->group_end();
        }

        $logs = $this->db->order_by('l.created_at', 'DESC')->limit($limit, $offset)->get()->result();

        $this->load->library('pagination');
        $this->pagination->initialize([
            'base_url'         => site_url('admin/notifikasi_log') . '?' . http_build_query(array_filter($filter)) . '&',
            'total_rows'       => $total,
            'per_page'         => $limit,
            'use_page_numbers' => TRUE,
            'cur_tag_open'     => '<strong>',
            'cur_tag_close'    => '</strong>',
        ]);

        $this->render('admin/notifikasi_log/index', [
            'title'      => 'Riwayat Log Notifikasi',
            'logs'       => $logs,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->pagination->create_links(),
        ]);
    }

    public function kirim_ulang($id) {
        $log = $this->db->get_where('log_notifikasi', ['id' => $id])->row();
        if (!$log) show_404();

        if ($log->jenis === 'email') {
            $this->load->library('emailgateway');
            $sent = $this->emailgateway->kirim($log->penerima, $log->subjek, $log->pesan, $log->perkara_id);
        } else {
            $this->load->library('wagateway');
            $sent = $this->wagateway->kirim($log->penerima, $log->pesan, $log->perkara_id);
        }

        if ($sent) {
            $this->session->set_flashdata('success', "Pengiriman ulang notifikasi ke <strong>{$log->penerima}</strong> BERHASIL!");
        } else {
            $this->session->set_flashdata('error', "Pengiriman ulang notifikasi ke <strong>{$log->penerima}</strong> GAGAL. Periksa kembali konfigurasi / server gateway.");
        }

        redirect('admin/notifikasi_log');
    }
}
