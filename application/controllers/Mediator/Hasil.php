<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hasil — Controller Mediator untuk menginput hasil akhir mediasi & upload laporan PDF
 */
class Hasil extends MY_Controller {

    protected $role_required = ['mediator', 'admin', 'hakim', 'pp'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_hasil', 'M_perkara']);
    }

    private function _verify_mediator_role() {
        $mediator_id = $this->session->userdata('mediator_id');
        if (!$mediator_id && $this->session->userdata('role') !== 'admin') {
            show_error('Akses ditolak. Anda tidak terdaftar sebagai mediator aktif.', 403);
        }
        return $mediator_id;
    }

    public function input($perkara_id) {
        $mediator_id = $this->_verify_mediator_role();
        $perkara     = $this->M_perkara->get_by_id($perkara_id);
        if (!$perkara) show_404();

        // Validasi penugasan mediator
        if ($this->session->userdata('role') !== 'admin' && $perkara->mediator_id != $mediator_id) {
            show_error('Akses ditolak. Perkara ini tidak di-assign ke Anda.', 403);
        }

        // Cek jika sudah diinput
        if ($this->M_hasil->is_exist($perkara_id)) {
            $this->session->set_flashdata('error', 'Hasil mediasi perkara ini sudah pernah diinput sebelumnya.');
            redirect("mediator/perkara_saya/detail/{$perkara_id}");
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('hasil', 'Hasil Mediasi', 'required|in_list[berhasil,berhasil_sebagian,tidak_berhasil]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $file_name = null;

                // Handle file upload if present
                if (!empty($_FILES['file_laporan']['name'])) {
                    $upload_path = FCPATH . 'uploads/laporan/';
                    if (!is_dir($upload_path)) {
                        @mkdir($upload_path, 0777, true);
                    }

                    $config['upload_path']   = realpath($upload_path) ?: $upload_path;
                    $config['allowed_types'] = 'pdf';
                    $config['max_size']      = 10240; // 10MB
                    $config['file_name']     = 'laporan_' . $perkara_id . '_' . time();

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('file_laporan')) {
                        $this->session->set_flashdata('error', 'Gagal upload file: ' . $this->upload->display_errors('', ''));
                        redirect("mediator/hasil/input/{$perkara_id}");
                        return;
                    }
                    $file_name = $this->upload->data('file_name');
                }

                $this->M_hasil->insert([
                    'perkara_id'   => $perkara_id,
                    'mediator_id'  => $mediator_id ?: $perkara->mediator_id,
                    'hasil'        => $this->input->post('hasil'),
                    'file_laporan' => $file_name,
                    'catatan'      => $this->input->post('catatan', true) ?: null,
                ]);

                // Update status perkara menjadi selesai
                $this->M_perkara->update($perkara_id, ['status' => 'selesai']);

                $this->session->set_flashdata('success', 'Hasil mediasi berhasil disimpan dan status perkara menjadi Selesai.');
                redirect("mediator/perkara_saya/detail/{$perkara_id}");
                return;
            }
        }

        $this->render('mediator/hasil/form', [
            'title'   => "Input Hasil Mediasi — {$perkara->nomor_perkara}",
            'perkara' => $perkara,
        ]);
    }
}
