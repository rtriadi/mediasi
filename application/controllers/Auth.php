<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller — Login, logout, profil, redirect by role
 */
#[\AllowDynamicProperties]
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_user');
    }

    /** Halaman login */
    public function index() {
        // Jika sudah login, redirect ke dashboard role-nya
        if ($this->session->userdata('user_id')) {
            $this->_redirect_by_role($this->session->userdata('role'));
        }
        $this->load->view('auth/login', ['title' => 'Login — Mediasi PA Gorontalo']);
    }

    /** Proses login */
    public function login() {
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);

        if (!$username || !$password) {
            $this->session->set_flashdata('error', 'Username dan password wajib diisi.');
            redirect('auth');
        }

        $user = $this->M_user->get_by_username($username);

        if ($user && password_verify($password, $user->password)) {
            $roles_array  = array_filter(array_map('trim', explode(',', $user->role)));
            $primary_role = reset($roles_array) ?: 'pp';
            $mediator_id  = $this->M_user->is_mediator($user->id);

            $has_mediator_role = in_array('mediator', $roles_array);

            $this->session->set_userdata([
                'user_id'     => $user->id,
                'nama'        => $user->nama,
                'role'        => $primary_role,
                'roles'       => $roles_array,
                'is_mediator' => $has_mediator_role,
                'mediator_id' => $mediator_id ?: null,
                'id_sipp'     => $user->id_sipp ?? null,
                'nip'         => $user->nip ?? null,
            ]);
            $this->_redirect_by_role($primary_role);
        } else {
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect('auth');
        }
    }

    /** Profil Saya & Ganti Password */
    public function profil() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            redirect('auth');
            return;
        }

        $user_data = $this->M_user->get_by_id($user_id);
        if (!$user_data) redirect('auth');

        if ($this->input->post()) {
            $nama     = trim($this->input->post('nama', true));
            $old_pass = $this->input->post('password_lama');
            $new_pass = $this->input->post('password_baru');

            if (empty($nama)) {
                $this->session->set_flashdata('error', 'Nama lengkap tidak boleh kosong.');
            } else {
                $update_data = ['nama' => $nama];

                // Jika ganti password
                if (!empty($new_pass)) {
                    if (!password_verify($old_pass, $user_data->password)) {
                        $this->session->set_flashdata('error', 'Password saat ini (lama) tidak sesuai.');
                        redirect('auth/profil');
                        return;
                    }
                    if (strlen($new_pass) < 6) {
                        $this->session->set_flashdata('error', 'Password baru minimal 6 karakter.');
                        redirect('auth/profil');
                        return;
                    }
                    $update_data['password'] = password_hash($new_pass, PASSWORD_BCRYPT);
                }

                $this->M_user->update($user_id, $update_data);
                $this->session->set_userdata('nama', $nama);
                $this->session->set_flashdata('success', 'Profil Anda berhasil diperbarui!');
                redirect('auth/profil');
                return;
            }
        }

        $data = [
            'title'     => 'Profil Saya',
            'user_data' => $user_data,
            'user'      => [
                'id'          => $this->session->userdata('user_id'),
                'nama'        => $this->session->userdata('nama'),
                'role'        => $this->session->userdata('role'),
                'is_mediator' => $this->session->userdata('is_mediator'),
                'mediator_id' => $this->session->userdata('mediator_id'),
            ],
            'content_view' => 'auth/profil',
        ];

        $this->load->view('layouts/main', $data);
    }

    /** Logout */
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }

    /** Redirect ke dashboard sesuai role */
    private function _redirect_by_role($role) {
        $map = [
            'admin'    => 'admin/master_user',
            'pp'       => 'pp/perkara',
            'mediator' => 'mediator/perkara_saya',
            'hakim'    => 'hakim/perkara',
            'pimpinan' => 'pimpinan/dashboard',
        ];
        redirect($map[$role] ?? 'auth');
    }
}
