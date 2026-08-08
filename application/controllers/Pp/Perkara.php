<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perkara — PP Controller untuk input perkara baru & assign mediator
 */
class Perkara extends MY_Controller {

    protected $role_required = ['pp', 'admin'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_perkara', 'M_mediator', 'M_jenis_perkara', 'M_user']);
        $this->load->library(['EmailGateway', 'WaGateway']);
    }

    public function index() {
        redirect('pp/monitor');
    }

    public function tambah() {
        if ($this->input->post('step') == '1') {
            $this->form_validation->set_rules('nomor_perkara',     'Nomor Perkara',     'required|trim|is_unique[perkara.nomor_perkara]');
            $this->form_validation->set_rules('jenis_perkara_id', 'Jenis Perkara',    'required|integer');
            $this->form_validation->set_rules('nama_hakim',       'Nama Majelis Hakim', 'required|trim');
            $this->form_validation->set_rules('tgl_batas_mediasi', 'Batas Tanggal Mediasi', 'required');

            $pihak_penggugat = $this->input->post('pihak_penggugat');
            $pihak_tergugat  = $this->input->post('pihak_tergugat');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } elseif (empty($pihak_penggugat) || empty($pihak_tergugat)) {
                $this->session->set_flashdata('error', 'Minimal harus ada 1 Penggugat dan 1 Tergugat.');
            } else {
                // Formatting data pihak
                $all_pihak = [];

                foreach ($pihak_penggugat as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'jenis'       => 'penggugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                foreach ($pihak_tergugat as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'jenis'       => 'tergugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                $pihak_turut = $this->input->post('pihak_turut') ?: [];
                foreach ($pihak_turut as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'jenis'       => 'turut_tergugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                // Simpan ke session untuk Step 2 (Assign Mediator)
                $this->session->set_userdata('new_perkara', [
                    'perkara' => [
                        'nomor_perkara'     => $this->input->post('nomor_perkara', true),
                        'jenis_perkara_id' => $this->input->post('jenis_perkara_id'),
                        'nama_hakim'        => $this->input->post('nama_hakim', true),
                        'tgl_batas_mediasi' => $this->input->post('tgl_batas_mediasi'),
                        'status'            => 'menunggu',
                        'pp_id'             => $this->session->userdata('user_id'),
                    ],
                    'pihak' => $all_pihak,
                ]);

                redirect('pp/perkara/assign_mediator');
                return;
            }
        }

        $this->render('pp/perkara/tambah', [
            'title'         => 'Input Perkara Baru',
            'jenis_perkara' => $this->M_jenis_perkara->get_all_aktif(),
            'hakim_list'    => $this->M_user->get_hakim(),
        ]);
    }

    public function assign_mediator() {
        $perkara_session = $this->session->userdata('new_perkara');
        if (!$perkara_session) {
            redirect('pp/perkara/tambah');
            return;
        }

        if ($this->input->post('mediator_id')) {
            $mediator_id = $this->input->post('mediator_id');

            // Save Perkara
            $perkara_id = $this->M_perkara->insert($perkara_session['perkara']);

            // Save Pihak-pihak
            $pihak = array_map(function($p) use ($perkara_id) {
                $p['perkara_id'] = $perkara_id;
                return $p;
            }, $perkara_session['pihak']);
            $this->M_perkara->insert_pihak($pihak);

            // Assign Mediator
            $this->M_perkara->assign_mediator([
                'perkara_id'  => $perkara_id,
                'mediator_id' => $mediator_id,
                'assigned_by' => $this->session->userdata('user_id'),
            ]);

            // Kirim notifikasi ke mediator
            $this->emailgateway->kirim_penugasan_mediator($perkara_id, $mediator_id);
            $this->wagateway->kirim_penugasan_mediator($perkara_id, $mediator_id);

            $this->session->unset_userdata('new_perkara');
            $this->session->set_flashdata('success', 'Perkara dan penetapan mediator berhasil didaftarkan.');
            redirect('pp/monitor');
            return;
        }

        $this->render('pp/perkara/assign_mediator', [
            'title'     => 'Penetapan Mediator',
            'mediators' => $this->M_mediator->get_aktif(),
            'perkara'   => $perkara_session,
        ]);
    }

    public function edit($id) {
        $perkara = $this->M_perkara->get_by_id($id);
        if (!$perkara) show_404();

        $user_roles = $this->session->userdata('roles') ?: [$this->session->userdata('role')];
        if (!in_array('admin', $user_roles) && $perkara->pp_id != $this->session->userdata('user_id')) {
            show_error('Akses ditolak. Anda tidak memiliki wewenang mengedit perkara ini.', 403);
        }

        if ($this->input->post()) {
            $nomor_perkara = $this->input->post('nomor_perkara', true);
            $this->form_validation->set_rules('nomor_perkara',     'Nomor Perkara',     'required|trim');
            $this->form_validation->set_rules('jenis_perkara_id', 'Jenis Perkara',    'required|integer');
            $this->form_validation->set_rules('nama_hakim',       'Nama Majelis Hakim', 'required|trim');
            $this->form_validation->set_rules('tgl_batas_mediasi', 'Batas Tanggal Mediasi', 'required');
            $this->form_validation->set_rules('mediator_id',      'Mediator',           'required|integer');

            $pihak_penggugat = $this->input->post('pihak_penggugat') ?: [];
            $pihak_tergugat  = $this->input->post('pihak_tergugat') ?: [];

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } elseif (empty($pihak_penggugat) || empty($pihak_tergugat)) {
                $this->session->set_flashdata('error', 'Minimal harus ada 1 Penggugat dan 1 Tergugat.');
            } else {
                // Formatting data pihak
                $all_pihak = [];

                foreach ($pihak_penggugat as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'id'          => $p['id'] ?? null,
                            'perkara_id'  => $id,
                            'jenis'       => 'penggugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                foreach ($pihak_tergugat as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'id'          => $p['id'] ?? null,
                            'perkara_id'  => $id,
                            'jenis'       => 'tergugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                $pihak_turut = $this->input->post('pihak_turut') ?: [];
                foreach ($pihak_turut as $idx => $p) {
                    if (!empty($p['nama'])) {
                        $all_pihak[] = [
                            'id'          => $p['id'] ?? null,
                            'perkara_id'  => $id,
                            'jenis'       => 'turut_tergugat',
                            'nama'        => trim($p['nama']),
                            'kuasa_hukum' => trim($p['kuasa_hukum'] ?? '') ?: null,
                            'no_hp'       => trim($p['no_hp'] ?? '') ?: null,
                            'email'       => trim($p['email'] ?? '') ?: null,
                            'urutan'      => $idx + 1,
                        ];
                    }
                }

                // Update data perkara
                $this->M_perkara->update($id, [
                    'nomor_perkara'     => $nomor_perkara,
                    'jenis_perkara_id' => $this->input->post('jenis_perkara_id'),
                    'nama_hakim'        => $this->input->post('nama_hakim', true),
                    'tgl_batas_mediasi' => $this->input->post('tgl_batas_mediasi'),
                ]);

                $new_mediator_id = $this->input->post('mediator_id');
                $old_mediator_id = $perkara->mediator_id ?? null;

                // Cek jika mediator berubah
                if ($old_mediator_id && $new_mediator_id != $old_mediator_id) {
                    $has_hasil = $this->db->get_where('hasil_mediasi', ['perkara_id' => $id])->row();
                    if ($has_hasil || $perkara->status === 'selesai') {
                        $this->session->set_flashdata('error', 'Mediator tidak dapat diganti karena perkara ini sudah memiliki Hasil Mediasi / Selesai.');
                        redirect("pp/perkara/edit/{$id}");
                        return;
                    }

                    // Assign mediator baru & catat log riwayat
                    $this->M_perkara->assign_mediator([
                        'perkara_id'  => $id,
                        'mediator_id' => $new_mediator_id,
                        'assigned_by' => $this->session->userdata('user_id'),
                    ]);

                    // Otomatis alihkan (takeover) semua sesi yang masih 'terjadwal' ke mediator baru
                    $this->db->where('perkara_id', $id)
                             ->where('status_sesi', 'terjadwal')
                             ->update('sesi_mediasi', ['mediator_id' => $new_mediator_id]);

                    // Kirim notifikasi ke mediator LAMA (pemberhentian penugasan)
                    $this->emailgateway->kirim_penggantian_mediator($id, $old_mediator_id);
                    $this->wagateway->kirim_penggantian_mediator($id, $old_mediator_id);

                    // Kirim notifikasi ke mediator BARU (penugasan baru)
                    $this->emailgateway->kirim_penugasan_mediator($id, $new_mediator_id);
                    $this->wagateway->kirim_penugasan_mediator($id, $new_mediator_id);
                } elseif (!$old_mediator_id && $new_mediator_id) {
                    // Penugasan pertama kali (jika sebelumnya belum ada)
                    $this->M_perkara->assign_mediator([
                        'perkara_id'  => $id,
                        'mediator_id' => $new_mediator_id,
                        'assigned_by' => $this->session->userdata('user_id'),
                    ]);

                    $this->emailgateway->kirim_penugasan_mediator($id, $new_mediator_id);
                    $this->wagateway->kirim_penugasan_mediator($id, $new_mediator_id);
                }

                // Smart Sync Data Pihak (Preserve IDs to prevent cascade deletion of attendance log)
                $submitted_ids = [];
                foreach ($all_pihak as $p) {
                    $pid = $p['id'] ?? null;
                    unset($p['id']);
                    if (!empty($pid)) {
                        $this->db->where('id', $pid)->update('perkara_pihak', $p);
                        $submitted_ids[] = $pid;
                    } else {
                        $this->db->insert('perkara_pihak', $p);
                        $submitted_ids[] = $this->db->insert_id();
                    }
                }

                // Delete only pihak rows that were explicitly removed by PP in the edit form
                $this->db->where('perkara_id', $id);
                if (!empty($submitted_ids)) {
                    $this->db->where_not_in('id', $submitted_ids);
                }
                $this->db->delete('perkara_pihak');

                $this->session->set_flashdata('success', 'Data perkara dan pihak berhasil diperbarui.');
                redirect("pp/monitor/detail/{$id}");
                return;
            }
        }

        $this->render('pp/perkara/edit', [
            'title'         => "Edit Data Perkara {$perkara->nomor_perkara}",
            'perkara'       => $perkara,
            'pihak'         => $this->M_perkara->get_pihak($id),
            'jenis_perkara' => $this->M_jenis_perkara->get_all_aktif(),
            'mediators'     => $this->M_mediator->get_aktif(),
            'hakim_list'    => $this->M_user->get_hakim(),
        ]);
    }
}
