<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_jenis_perkara extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->model('M_jenis_perkara');
    }

    public function index() {
        $filter = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total      = $this->M_jenis_perkara->count_all($filter);
        $jenis_list = $this->M_jenis_perkara->get_paginated($filter, $limit, $offset);

        $this->render('admin/jenis_perkara/index', [
            'title'      => 'Kelola Jenis Perkara',
            'jenis_list' => $jenis_list,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->paginate('admin/master_jenis_perkara', $total, $limit, $filter),
        ]);
    }

    public function tambah() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama Jenis Perkara', 'required|trim');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $this->M_jenis_perkara->insert([
                    'nama'       => $this->input->post('nama', true),
                    'keterangan' => $this->input->post('keterangan', true) ?: null,
                    'is_active'  => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Jenis perkara berhasil ditambahkan.');
                redirect('admin/master_jenis_perkara');
            }
        }
        $this->render('admin/jenis_perkara/form', ['title' => 'Tambah Jenis Perkara', 'jenis_data' => null]);
    }

    public function edit($id) {
        $jenis_data = $this->M_jenis_perkara->get_by_id($id);
        if (!$jenis_data) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama Jenis Perkara', 'required|trim');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $this->M_jenis_perkara->update($id, [
                    'nama'       => $this->input->post('nama', true),
                    'keterangan' => $this->input->post('keterangan', true) ?: null,
                    'is_active'  => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Jenis perkara berhasil diperbarui.');
                redirect('admin/master_jenis_perkara');
            }
        }
        $this->render('admin/jenis_perkara/form', ['title' => 'Edit Jenis Perkara', 'jenis_data' => $jenis_data]);
    }

    public function hapus($id) {
        $jenis_data = $this->M_jenis_perkara->get_by_id($id);
        if (!$jenis_data) show_404();
        $this->M_jenis_perkara->delete($id);
        $this->session->set_flashdata('success', 'Jenis perkara berhasil dihapus.');
        redirect('admin/master_jenis_perkara');
    }

    public function toggle_aktif($id) {
        $this->M_jenis_perkara->toggle_aktif($id);
        $this->session->set_flashdata('success', 'Status jenis perkara berhasil diubah.');
        redirect('admin/master_jenis_perkara');
    }
}
