<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengaturan — Controller Pengaturan Aplikasi, WA & SMTP Email Gateway (Khusus Admin)
 */
class Pengaturan extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->model('M_pengaturan');
    }

    public function index() {
        if ($this->input->method() === 'post') {
            $this->_simpan_pengaturan();
            return;
        }

        $data['title']    = 'Pengaturan Aplikasi';
        $data['settings'] = $this->M_pengaturan->get_all_as_array();

        $this->render('admin/pengaturan/index', $data);
    }

    private function _simpan_pengaturan() {
        $nama_aplikasi   = trim($this->input->post('nama_aplikasi', true));
        $slogan_aplikasi = trim($this->input->post('slogan_aplikasi', true));
        $nama_satker     = trim($this->input->post('nama_satker', true));
        
        // WA Settings
        $wa_notif_active = $this->input->post('wa_notif_active') ? '1' : '0';
        $wa_api_token    = trim($this->input->post('wa_api_token', true));
        $wa_api_url      = trim($this->input->post('wa_api_url', true)) ?: 'https://api.fonnte.com/send';

        // SMTP Email Settings
        $email_notif_active = $this->input->post('email_notif_active') ? '1' : '0';
        $smtp_host          = trim($this->input->post('smtp_host', true)) ?: 'smtp.gmail.com';
        $smtp_port          = trim($this->input->post('smtp_port', true)) ?: '587';
        $smtp_user          = trim($this->input->post('smtp_user', true));
        $smtp_pass          = trim($this->input->post('smtp_pass', true));
        $smtp_crypto        = trim($this->input->post('smtp_crypto', true)) ?: 'tls';
        $mail_from_name     = trim($this->input->post('mail_from_name', true)) ?: $nama_aplikasi;

        $this->M_pengaturan->save('nama_aplikasi', $nama_aplikasi);
        $this->M_pengaturan->save('slogan_aplikasi', $slogan_aplikasi);
        $this->M_pengaturan->save('nama_satker', $nama_satker);
        
        $this->M_pengaturan->save('wa_notif_active', $wa_notif_active);
        $this->M_pengaturan->save('wa_api_token', $wa_api_token);
        $this->M_pengaturan->save('wa_api_url', $wa_api_url);

        $this->M_pengaturan->save('email_notif_active', $email_notif_active);
        $this->M_pengaturan->save('smtp_host', $smtp_host);
        $this->M_pengaturan->save('smtp_port', $smtp_port);
        $this->M_pengaturan->save('smtp_user', $smtp_user);
        $this->M_pengaturan->save('smtp_pass', $smtp_pass);
        $this->M_pengaturan->save('smtp_crypto', $smtp_crypto);
        $this->M_pengaturan->save('mail_from_name', $mail_from_name);

        // Upload Logo Aplikasi jika ada
        if (!empty($_FILES['logo_aplikasi']['name'])) {
            $upload_path = FCPATH . 'uploads/settings/';
            if (!is_dir($upload_path)) {
                @mkdir($upload_path, 0777, true);
            }

            $config['upload_path']   = realpath($upload_path) ?: $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|webp|svg';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'logo_' . time();
            $config['overwrite']     = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('logo_aplikasi')) {
                $upload_data = $this->upload->data();
                
                // Hapus logo lama jika ada
                $old_logo = $this->M_pengaturan->get('logo_aplikasi');
                if ($old_logo && file_exists($upload_path . $old_logo)) {
                    @unlink($upload_path . $old_logo);
                }

                $this->M_pengaturan->save('logo_aplikasi', $upload_data['file_name']);
            } else {
                $this->session->set_flashdata('error', 'Gagal mengunggah logo: ' . $this->upload->display_errors('', ''));
                redirect('admin/pengaturan');
                return;
            }
        }

        $this->session->set_flashdata('success', 'Pengaturan aplikasi, Email SMTP, dan WhatsApp berhasil disimpan!');
        redirect('admin/pengaturan');
    }

    public function hapus_logo() {
        $old_logo = $this->M_pengaturan->get('logo_aplikasi');
        if ($old_logo) {
            $file_path = FCPATH . 'uploads/settings/' . $old_logo;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $this->M_pengaturan->save('logo_aplikasi', '');
            $this->session->set_flashdata('success', 'Logo aplikasi berhasil dihapus.');
        }
        redirect('admin/pengaturan');
    }
}
