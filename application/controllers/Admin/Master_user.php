<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Master_user — Admin CRUD user management
 */
class Master_user extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->model('M_user');
    }

    public function index() {
        $filter = [
            'role'   => $this->input->get('role'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;
        $total  = $this->M_user->count_all($filter);

        $this->render('admin/user/index', [
            'title'      => 'Kelola User',
            'users'      => $this->M_user->get_all($filter, $limit, $offset),
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->paginate('admin/master_user', $total, $limit, $filter),
        ]);
    }

    public function tambah() {
        if ($this->input->post()) {
            $username = $this->input->post('username', true);
            $roles    = $this->input->post('roles');

            $this->form_validation->set_rules('nama',     'Nama',     'required|trim');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|alpha_dash');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

            if (empty($roles) || !is_array($roles)) {
                $this->session->set_flashdata('error', 'Pilih minimal 1 role untuk user ini.');
                redirect('admin/master_user/tambah');
                return;
            }

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } elseif ($this->M_user->is_username_taken($username)) {
                $this->session->set_flashdata('error', 'Username sudah digunakan.');
            } else {
                $role_str = implode(',', $roles);
                $this->M_user->insert([
                    'nama'      => $this->input->post('nama', true),
                    'username'  => $username,
                    'password'  => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                    'role'      => $role_str,
                    'id_sipp'   => $this->input->post('id_sipp', true) ?: null,
                    'nip'       => $this->input->post('nip', true) ?: null,
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                ]);

                $user_id = $this->db->insert_id();

                // Auto-sync mediator profile jika memilih role mediator
                if (in_array('mediator', $roles)) {
                    $this->_sync_mediator_profile($user_id, $this->input->post('nama', true), in_array('hakim', $roles));
                }

                $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
                redirect('admin/master_user');
            }
        }
        $this->render('admin/user/form', ['title' => 'Tambah User', 'user_data' => null]);
    }

    public function edit($id) {
        $user_data = $this->M_user->get_by_id($id);
        if (!$user_data) show_404();

        if ($this->input->post()) {
            $username = $this->input->post('username', true);
            $roles    = $this->input->post('roles');

            $this->form_validation->set_rules('nama',     'Nama',     'required|trim');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|alpha_dash');

            if (empty($roles) || !is_array($roles)) {
                $this->session->set_flashdata('error', 'Pilih minimal 1 role untuk user ini.');
                redirect("admin/master_user/edit/{$id}");
                return;
            }

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } elseif ($this->M_user->is_username_taken($username, $id)) {
                $this->session->set_flashdata('error', 'Username sudah digunakan.');
            } else {
                $role_str = implode(',', $roles);
                $update_data = [
                    'nama'      => $this->input->post('nama', true),
                    'username'  => $username,
                    'role'      => $role_str,
                    'id_sipp'   => $this->input->post('id_sipp', true) ?: null,
                    'nip'       => $this->input->post('nip', true) ?: null,
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                ];

                $new_pass = $this->input->post('password');
                if ($new_pass) {
                    if (strlen($new_pass) < 6) {
                        $this->session->set_flashdata('error', 'Password minimal 6 karakter.');
                        redirect("admin/master_user/edit/{$id}");
                        return;
                    }
                    $update_data['password'] = password_hash($new_pass, PASSWORD_BCRYPT);
                }
                $this->M_user->update($id, $update_data);

                // Auto-sync mediator profile jika memilih role mediator
                if (in_array('mediator', $roles)) {
                    $this->_sync_mediator_profile($id, $this->input->post('nama', true), in_array('hakim', $roles));
                }

                $this->session->set_flashdata('success', 'User berhasil diperbarui.');
                redirect('admin/master_user');
            }
        }
        $this->render('admin/user/form', ['title' => 'Edit User', 'user_data' => $user_data]);
    }

    private function _sync_mediator_profile($user_id, $nama, $is_hakim = false) {
        $this->load->model('M_mediator');
        $existing = $this->db->get_where('mediators', ['user_id' => $user_id])->row();
        if (!$existing) {
            $this->M_mediator->insert([
                'nama'      => $nama,
                'jenis'     => $is_hakim ? 'hakim' : 'non_hakim',
                'user_id'   => $user_id,
                'is_active' => 1,
            ]);
        } else {
            $this->M_mediator->update($existing->id, [
                'nama'  => $nama,
                'jenis' => $is_hakim ? 'hakim' : 'non_hakim',
            ]);
        }
    }

    public function hapus($id) {
        $user_data = $this->M_user->get_by_id($id);
        if (!$user_data) show_404();
        $this->M_user->delete($id);
        $this->session->set_flashdata('success', 'User berhasil dihapus.');
        redirect('admin/master_user');
    }

    public function toggle_aktif($id) {
        $this->M_user->toggle_aktif($id);
        $this->session->set_flashdata('success', 'Status user berhasil diubah.');
        redirect('admin/master_user');
    }

    public function reset_password($id) {
        $user_data = $this->M_user->get_by_id($id);
        if (!$user_data) show_404();

        $default_pass = '123456';
        $hashed_pass  = password_hash($default_pass, PASSWORD_BCRYPT);
        $this->M_user->update($id, ['password' => $hashed_pass]);

        $this->session->set_flashdata('success', "Password untuk user <strong>" . htmlspecialchars($user_data->username) . "</strong> berhasil di-reset ke default: <strong class='font-mono bg-amber-100 px-2 py-0.5 rounded text-amber-900 border border-amber-300'>123456</strong>");
        redirect('admin/master_user');
    }
}
