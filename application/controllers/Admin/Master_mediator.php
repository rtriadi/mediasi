<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Master_mediator — Admin CRUD mediator management
 */
class Master_mediator extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_mediator', 'M_user']);
    }

    public function index() {
        $filter = [
            'jenis'  => $this->input->get('jenis'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $total  = $this->M_mediator->count_all($filter);

        $this->render('admin/mediator/index', [
            'title'      => 'Kelola Mediator',
            'mediators'  => $this->M_mediator->get_all($filter, $limit, $offset),
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->paginate('admin/master_mediator', $total, $limit, $filter),
        ]);
    }

    public function tambah() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('nama',  'Nama Mediator', 'required|trim');
            $this->form_validation->set_rules('jenis', 'Jenis',         'required|in_list[hakim,non_hakim]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $user_id = $this->input->post('user_id') ?: null;
                $this->M_mediator->insert([
                    'nama'          => $this->input->post('nama', true),
                    'jenis'         => $this->input->post('jenis'),
                    'no_sertifikat' => $this->input->post('no_sertifikat', true) ?: null,
                    'email'         => $this->input->post('email', true) ?: null,
                    'no_hp'         => $this->input->post('no_hp', true) ?: null,
                    'user_id'       => $user_id,
                    'is_active'     => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Mediator berhasil ditambahkan.');
                redirect('admin/master_mediator');
            }
        }
        $this->render('admin/mediator/form', [
            'title'         => 'Tambah Mediator',
            'mediator_data' => null,
            'available_users' => $this->M_user->get_for_mediator_link(),
        ]);
    }

    public function edit($id) {
        $mediator_data = $this->M_mediator->get_by_id($id);
        if (!$mediator_data) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama',  'Nama Mediator', 'required|trim');
            $this->form_validation->set_rules('jenis', 'Jenis',         'required|in_list[hakim,non_hakim]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $user_id = $this->input->post('user_id') ?: null;
                $this->M_mediator->update($id, [
                    'nama'          => $this->input->post('nama', true),
                    'jenis'         => $this->input->post('jenis'),
                    'no_sertifikat' => $this->input->post('no_sertifikat', true) ?: null,
                    'email'         => $this->input->post('email', true) ?: null,
                    'no_hp'         => $this->input->post('no_hp', true) ?: null,
                    'user_id'       => $user_id,
                    'is_active'     => $this->input->post('is_active') ? 1 : 0,
                ]);
                $this->session->set_flashdata('success', 'Data mediator berhasil diperbarui.');
                redirect('admin/master_mediator');
            }
        }

        // Include current linked user in available users
        $available_users = $this->M_user->get_for_mediator_link();
        if ($mediator_data->user_id) {
            $current_user = $this->M_user->get_by_id($mediator_data->user_id);
            if ($current_user) {
                array_unshift($available_users, $current_user);
            }
        }

        $this->render('admin/mediator/form', [
            'title'           => 'Edit Mediator',
            'mediator_data'   => $mediator_data,
            'available_users' => $available_users,
        ]);
    }

    public function hapus($id) {
        $mediator_data = $this->M_mediator->get_by_id($id);
        if (!$mediator_data) show_404();
        $this->M_mediator->delete($id);
        $this->session->set_flashdata('success', 'Mediator berhasil dihapus.');
        redirect('admin/master_mediator');
    }

    public function toggle_aktif($id) {
        $this->M_mediator->toggle_aktif($id);
        $this->session->set_flashdata('success', 'Status mediator berhasil diubah.');
        redirect('admin/master_mediator');
    }
}
