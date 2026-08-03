<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profil — Controller untuk ganti password mandiri seluruh pengguna
 */
class Profil extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_user');
    }

    public function index() {
        redirect('profil/ganti_password');
    }

    public function ganti_password() {
        $user_id   = $this->session->userdata('user_id');
        $user_data = $this->M_user->get_by_id($user_id);
        if (!$user_data) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('pass_lama',       'Password Saat Ini', 'required');
            $this->form_validation->set_rules('pass_baru',       'Password Baru',     'required|min_length[6]');
            $this->form_validation->set_rules('konfirmasi_pass', 'Konfirmasi Password', 'required|matches[pass_baru]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $pass_lama = $this->input->post('pass_lama');
                $pass_baru = $this->input->post('pass_baru');

                // Verifikasi password lama
                if (!password_verify($pass_lama, $user_data->password)) {
                    $this->session->set_flashdata('error', 'Password saat ini (password lama) yang Anda masukkan salah.');
                    redirect('profil/ganti_password');
                    return;
                }

                // Update password baru
                $hashed = password_hash($pass_baru, PASSWORD_BCRYPT);
                $this->M_user->update($user_id, ['password' => $hashed]);

                $this->session->set_flashdata('success', 'Password Anda berhasil diperbarui. Silakan gunakan password baru ini untuk login berikutnya.');
                redirect('profil/ganti_password');
                return;
            }
        }

        $this->render('profil/ganti_password', [
            'title'     => 'Ganti Password Saya',
            'user_data' => $user_data,
        ]);
    }
}
