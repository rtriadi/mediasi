<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_ruangan extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->model('M_ruangan');
    }

    public function index() {
        $filter = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total    = $this->M_ruangan->count_all($filter);
        $ruangans = $this->M_ruangan->get_paginated($filter, $limit, $offset);

        $this->load->library('pagination');
        $this->pagination->initialize([
            'base_url'         => site_url('admin/master_ruangan') . '?' . http_build_query(array_filter($filter)) . '&',
            'total_rows'       => $total,
            'per_page'         => $limit,
            'use_page_numbers' => TRUE,
            'cur_tag_open'     => '<strong class="px-3 py-1 bg-blue-600 text-white rounded-lg font-bold">',
            'cur_tag_close'    => '</strong>',
        ]);

        $this->render('admin/ruangan/index', [
            'title'      => 'Kelola Ruangan Mediasi',
            'ruangans'   => $ruangans,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->pagination->create_links(),
        ]);
    }

    public function tambah() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_ruangan', 'Nama Ruangan', 'required|trim');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $this->M_ruangan->insert([
                    'nama_ruangan' => $this->input->post('nama_ruangan', true),
                    'keterangan'   => $this->input->post('keterangan', true) ?: null,
                    'is_active'    => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Ruangan berhasil ditambahkan.');
                redirect('admin/master_ruangan');
            }
        }
        $this->render('admin/ruangan/form', ['title' => 'Tambah Ruangan', 'ruangan_data' => null]);
    }

    public function edit($id) {
        $ruangan_data = $this->M_ruangan->get_by_id($id);
        if (!$ruangan_data) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_ruangan', 'Nama Ruangan', 'required|trim');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $this->M_ruangan->update($id, [
                    'nama_ruangan' => $this->input->post('nama_ruangan', true),
                    'keterangan'   => $this->input->post('keterangan', true) ?: null,
                    'is_active'    => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Ruangan berhasil diperbarui.');
                redirect('admin/master_ruangan');
            }
        }
        $this->render('admin/ruangan/form', ['title' => 'Edit Ruangan', 'ruangan_data' => $ruangan_data]);
    }

    public function hapus($id) {
        $ruangan_data = $this->M_ruangan->get_by_id($id);
        if (!$ruangan_data) show_404();
        $this->M_ruangan->delete($id);
        $this->session->set_flashdata('success', 'Ruangan berhasil dihapus.');
        redirect('admin/master_ruangan');
    }

    public function toggle_aktif($id) {
        $this->M_ruangan->toggle_aktif($id);
        $this->session->set_flashdata('success', 'Status ruangan berhasil diubah.');
        redirect('admin/master_ruangan');
    }
}
