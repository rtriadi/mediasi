<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Jadwal — Controller Mediator untuk manajemen jadwal mediasi, kalender interaktif, & room conflict check
 */
class Jadwal extends MY_Controller {

    protected $role_required = ['mediator', 'admin', 'hakim', 'pp'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_jadwal', 'M_perkara', 'M_ruangan', 'M_hasil']);
    }

    private function _verify_mediator_role() {
        $mediator_id = $this->session->userdata('mediator_id');
        if (!$mediator_id) {
            $user_id = $this->session->userdata('user_id');
            $this->load->model('M_user');
            $mediator_id = $this->M_user->is_mediator($user_id);
            if ($mediator_id) {
                $this->session->set_userdata('mediator_id', $mediator_id);
                $this->session->set_userdata('is_mediator', true);
            }
        }
        if (!$mediator_id && !in_array('admin', $this->session->userdata('roles') ?: [$this->session->userdata('role')])) {
            show_error('Akses ditolak. Akun Anda belum dihubungkan dengan data Mediator Aktif. Silakan hubungi Admin.', 403);
        }
        return $mediator_id;
    }

    public function index() {
        $mediator_id = $this->_verify_mediator_role();

        $filter = [
            'bulan'  => $this->input->get('bulan'),
            'tahun'  => $this->input->get('tahun') ?: date('Y'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total   = $this->M_jadwal->count_by_mediator($mediator_id, $filter);
        $jadwals = $this->M_jadwal->get_by_mediator($mediator_id, $filter, $limit, $offset);

        $this->render('mediator/jadwal/index', [
            'title'      => 'Jadwal Mediasi Saya',
            'jadwals'    => $jadwals,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->paginate('mediator/jadwal', $total, $limit, $filter),
        ]);
    }

    public function kalender() {
        $mediator_id = $this->_verify_mediator_role();

        $user_roles = $this->session->userdata('roles') ?: [$this->session->userdata('role')];
        $is_admin   = in_array('admin', $user_roles);

        // Hanya tampilkan sesi yang 'terjadwal' (abaikan 'batal' dan 'dijadwal_ulang')
        $this->db->select('j.*, p.nomor_perkara, r.nama_ruangan')
            ->from('sesi_mediasi j')
            ->join('perkara p', 'p.id = j.perkara_id')
            ->join('ruangan r', 'r.id = j.ruangan_id', 'left')
            ->where('j.status_sesi', 'terjadwal');

        // Jika bukan admin, filter kalender khusus mediator yang sedang login
        if (!$is_admin && $mediator_id) {
            $this->db->where('j.mediator_id', $mediator_id);
        }

        $all_events = $this->db->get()->result();

        $events = [];
        foreach ($all_events as $e) {
            $jam_mulai   = date('H.i', strtotime($e->jam_mulai));
            $jam_selesai = date('H.i', strtotime($e->jam_selesai));
            $waktu_str   = $jam_mulai . ' - ' . $jam_selesai;
            $tempat_str  = '(' . ($e->nama_ruangan ?: $e->tempat_lain ?: 'Online') . ')';

            // Tentukan URL aman: jika di-assign ke mediator ini gunakan detail mediator, jika tidak gunakan detail monitor
            $target_url = ($e->mediator_id == $mediator_id)
                ? site_url("mediator/perkara_saya/detail/{$e->perkara_id}")
                : site_url("pp/monitor/detail/{$e->perkara_id}");

            $events[] = [
                'id'            => $e->id,
                'title'         => $e->nomor_perkara,
                'start'         => $e->tgl_mediasi . 'T' . $e->jam_mulai,
                'end'           => $e->tgl_mediasi . 'T' . $e->jam_selesai,
                'url'           => $target_url,
                'color'         => ($e->mediator_id == $mediator_id) ? '#2563eb' : '#475569',
                'extendedProps' => [
                    'waktu'  => $waktu_str,
                    'tempat' => $tempat_str,
                ],
            ];
        }

        $this->render('mediator/jadwal/kalender', [
            'title'       => 'Kalender Mediasi Interaktif',
            'events_json' => json_encode($events),
        ]);
    }

    public function tambah($perkara_id) {
        $mediator_id = $this->_verify_mediator_role();
        $perkara     = $this->M_perkara->get_by_id($perkara_id);
        if (!$perkara) show_404();

        // Validasi mediator
        if ($this->session->userdata('role') !== 'admin' && $perkara->mediator_id != $mediator_id) {
            show_error('Akses ditolak.', 403);
        }

        // Validasi jika hasil sudah diinput atau perkara sudah selesai
        if ($perkara->status === 'selesai' || $this->M_hasil->is_exist($perkara_id)) {
            $this->session->set_flashdata('error', 'Tidak dapat menambah jadwal karena perkara ini sudah selesai / hasil mediasi sudah diinput.');
            redirect("mediator/perkara_saya/detail/{$perkara_id}");
            return;
        }

        // Validasi jika ada sesi sebelumnya yang belum selesai
        $unfinished = $this->M_jadwal->get_unfinished_session($perkara_id);
        if ($unfinished) {
            $this->session->set_flashdata('error', 'Sesi mediasi sebelumnya (tanggal ' . date('d/m/Y', strtotime($unfinished->tgl_mediasi)) . ') belum diselesaikan. Harap catat presensi & selesaikan sesi tersebut terlebih dahulu.');
            redirect("mediator/perkara_saya/detail/{$perkara_id}");
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('tgl_mediasi', 'Tanggal Mediasi', 'required');
            $this->form_validation->set_rules('jam_mulai',   'Jam Mulai',       'required');
            $this->form_validation->set_rules('jam_selesai', 'Jam Selesai',     'required');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $tgl         = $this->input->post('tgl_mediasi');
                $jam_mulai   = $this->input->post('jam_mulai');
                $jam_selesai = $this->input->post('jam_selesai');
                $ruangan_id  = $this->input->post('ruangan_id') ?: null;

                // Validasi tanggal mediasi tidak boleh melebihi batas akhir mediasi
                if (!empty($perkara->tgl_batas_mediasi) && strtotime($tgl) > strtotime($perkara->tgl_batas_mediasi)) {
                    $this->session->set_flashdata('error', 'Tanggal mediasi (' . date('d/m/Y', strtotime($tgl)) . ') melebihi Batas Akhir Mediasi (' . date('d/m/Y', strtotime($perkara->tgl_batas_mediasi)) . '). Silakan tentukan tanggal sebelum batas akhir.');
                    redirect("mediator/jadwal/tambah/{$perkara_id}");
                    return;
                }

                // Validasi jam_selesai > jam_mulai
                if ($jam_selesai <= $jam_mulai) {
                    $this->session->set_flashdata('error', 'Jam selesai harus lebih akhir daripada jam mulai.');
                    redirect("mediator/jadwal/tambah/{$perkara_id}");
                    return;
                }

                // Conflict check jika memilih ruangan
                if ($ruangan_id) {
                    $konflik = $this->M_jadwal->check_conflict($ruangan_id, $tgl, $jam_mulai, $jam_selesai);
                    if ($konflik) {
                        $this->session->set_flashdata('error', "BENTROK RUANGAN! Ruangan tersebut sudah digunakan oleh Perkara No. {$konflik->nomor_perkara} pada tanggal {$tgl} jam {$konflik->jam_mulai} – {$konflik->jam_selesai}. Silakan pilih jam atau ruangan lain.");
                        redirect("mediator/jadwal/tambah/{$perkara_id}");
                        return;
                    }
                }

                $sesi_id = $this->M_jadwal->insert([
                    'perkara_id'      => $perkara_id,
                    'mediator_id'     => $mediator_id,
                    'tgl_mediasi'     => $tgl,
                    'jam_mulai'       => $jam_mulai,
                    'jam_selesai'     => $jam_selesai,
                    'ruangan_id'      => $ruangan_id,
                    'tempat_lain'     => $this->input->post('tempat_lain', true) ?: null,
                    'link_virtual'    => $this->input->post('link_virtual', true) ?: null,
                    'platform_virtual'=> $this->input->post('platform_virtual', true) ?: null,
                    'keterangan'      => $this->input->post('keterangan', true) ?: null,
                ]);

                // Update status perkara ke 'proses' karena jadwal pertama sudah dibuat
                $this->M_perkara->update($perkara_id, ['status' => 'proses']);

                $keterangan      = $this->input->post('keterangan', true) ?: null;

                // Trigger Email & WA notifications if enabled in settings
                if (file_exists(APPPATH . 'libraries/EmailGateway.php')) {
                    $this->load->library('emailgateway');
                    $this->emailgateway->kirim_jadwal($perkara_id, $tgl, $jam_mulai, $jam_selesai, $ruangan_id, $link_virtual, $platform_virtual, 'baru', $keterangan);
                }
                if (file_exists(APPPATH . 'libraries/WaGateway.php')) {
                    $this->load->library('wagateway');
                    $this->wagateway->kirim_jadwal($perkara_id, $tgl, $jam_mulai, $jam_selesai, $ruangan_id, $link_virtual, $platform_virtual, 'baru', $keterangan);
                }

                $this->session->set_flashdata('success', 'Jadwal mediasi berhasil dibuat.');
                redirect("mediator/perkara_saya/detail/{$perkara_id}");
                return;
            }
        }

        $this->render('mediator/jadwal/form', [
            'title'    => "Buat Jadwal Mediasi — {$perkara->nomor_perkara}",
            'perkara'  => $perkara,
            'ruangans' => $this->M_ruangan->get_aktif(),
        ]);
    }

    /**
     * Reschedule sesi mediasi (tandai lama → dijadwal_ulang, buat sesi baru).
     */
    public function reschedule($sesi_id) {
        $mediator_id = $this->_verify_mediator_role();
        $sesi = $this->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) show_404();

        $perkara = $this->M_perkara->get_by_id($sesi->perkara_id);
        if (!$perkara) show_404();

        // Validasi mediator
        if ($this->session->userdata('role') !== 'admin' && $sesi->mediator_id != $mediator_id) {
            show_error('Akses ditolak.', 403);
        }

        // Validasi perkara selesai
        if ($perkara->status === 'selesai' || $this->M_hasil->is_exist($sesi->perkara_id)) {
            $this->session->set_flashdata('error', 'Tidak dapat mengubah jadwal karena perkara ini sudah selesai.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        // Sesi yang bisa direschedule hanya berstatus 'terjadwal'
        if ($sesi->status_sesi !== 'terjadwal') {
            $this->session->set_flashdata('error', 'Hanya sesi berstatus Terjadwal yang dapat di-reschedule.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('tgl_mediasi_baru', 'Tanggal Baru',   'required');
            $this->form_validation->set_rules('jam_mulai_baru',   'Jam Mulai Baru', 'required');
            $this->form_validation->set_rules('jam_selesai_baru', 'Jam Selesai Baru','required');
            $this->form_validation->set_rules('alasan',           'Alasan',          'required|min_length[5]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $tgl_baru         = $this->input->post('tgl_mediasi_baru');
                $jam_mulai_baru   = $this->input->post('jam_mulai_baru');
                $jam_selesai_baru = $this->input->post('jam_selesai_baru');
                $ruangan_id_baru  = $this->input->post('ruangan_id_baru') ?: null;
                $alasan           = $this->input->post('alasan', true);

                if (!empty($perkara->tgl_batas_mediasi) && strtotime($tgl_baru) > strtotime($perkara->tgl_batas_mediasi)) {
                    $this->session->set_flashdata('error', 'Tanggal mediasi baru (' . date('d/m/Y', strtotime($tgl_baru)) . ') melebihi Batas Akhir Mediasi (' . date('d/m/Y', strtotime($perkara->tgl_batas_mediasi)) . ').');
                    redirect("mediator/jadwal/reschedule/{$sesi_id}"); return;
                }

                if ($jam_selesai_baru <= $jam_mulai_baru) {
                    $this->session->set_flashdata('error', 'Jam selesai harus lebih akhir daripada jam mulai.');
                    redirect("mediator/jadwal/reschedule/{$sesi_id}"); return;
                }

                if ($ruangan_id_baru) {
                    $konflik = $this->M_jadwal->check_conflict($ruangan_id_baru, $tgl_baru, $jam_mulai_baru, $jam_selesai_baru);
                    if ($konflik) {
                        $this->session->set_flashdata('error', "BENTROK RUANGAN! Digunakan oleh Perkara {$konflik->nomor_perkara}.");
                        redirect("mediator/jadwal/reschedule/{$sesi_id}"); return;
                    }
                }

                $data_baru = [
                    'perkara_id'       => $sesi->perkara_id,
                    'mediator_id'      => $sesi->mediator_id,
                    'tgl_mediasi'      => $tgl_baru,
                    'jam_mulai'        => $jam_mulai_baru,
                    'jam_selesai'      => $jam_selesai_baru,
                    'ruangan_id'       => $ruangan_id_baru,
                    'tempat_lain'      => $this->input->post('tempat_lain_baru', true) ?: null,
                    'link_virtual'     => $this->input->post('link_virtual_baru', true) ?: null,
                    'platform_virtual' => $this->input->post('platform_virtual_baru', true) ?: null,
                    'alasan_reschedule'=> "Jadwal ulang dari sesi #{$sesi_id}. Alasan: {$alasan}",
                    'keterangan'       => null,
                    'status_sesi'      => 'terjadwal',
                ];

                $new_sesi_id = $this->M_jadwal->reschedule($sesi_id, $alasan, $data_baru);

                // Kirim ulang notifikasi khusus reschedule
                if (file_exists(APPPATH . 'libraries/EmailGateway.php')) {
                    $this->load->library('emailgateway');
                    $this->emailgateway->kirim_jadwal($sesi->perkara_id, $tgl_baru, $jam_mulai_baru, $jam_selesai_baru, $ruangan_id_baru, $data_baru['link_virtual'], $data_baru['platform_virtual'], 'reschedule', $alasan);
                }
                if (file_exists(APPPATH . 'libraries/WaGateway.php')) {
                    $this->load->library('wagateway');
                    $this->wagateway->kirim_jadwal($sesi->perkara_id, $tgl_baru, $jam_mulai_baru, $jam_selesai_baru, $ruangan_id_baru, $data_baru['link_virtual'], $data_baru['platform_virtual'], 'reschedule', $alasan);
                }

                $this->session->set_flashdata('success', 'Sesi berhasil dijadwal ulang. Notifikasi telah dikirim ke para pihak.');
                redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
                return;
            }
        }

        $this->render('mediator/jadwal/reschedule', [
            'title'    => "Jadwal Ulang Sesi Mediasi — {$perkara->nomor_perkara}",
            'sesi'     => $sesi,
            'perkara'  => $perkara,
            'ruangans' => $this->M_ruangan->get_aktif(),
        ]);
    }

    /**
     * Batalkan sesi mediasi.
     */
    public function batal($sesi_id) {
        $mediator_id = $this->_verify_mediator_role();
        $sesi = $this->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) show_404();

        if (!in_array('admin', $this->session->userdata('roles') ?: [$this->session->userdata('role')]) && $sesi->mediator_id != $mediator_id) {
            show_error('Akses ditolak.', 403);
        }

        $perkara = $this->M_perkara->get_by_id($sesi->perkara_id);
        if ($perkara && ($perkara->status === 'selesai' || $this->M_hasil->is_exist($sesi->perkara_id))) {
            $this->session->set_flashdata('error', 'Tidak dapat membatalkan sesi karena perkara ini sudah selesai.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        if ($sesi->status_sesi !== 'terjadwal') {
            $this->session->set_flashdata('error', 'Hanya sesi berstatus Terjadwal yang dapat dibatalkan.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        $alasan = $this->input->post('alasan', true) ?: 'Dibatalkan oleh mediator.';
        $this->M_jadwal->batal($sesi_id, $alasan);

        // Broadcast Notifikasi Pembatalan Sesi ke Email & WA
        if (file_exists(APPPATH . 'libraries/EmailGateway.php')) {
            $this->load->library('emailgateway');
            $this->emailgateway->kirim_batal($sesi_id, $alasan);
        }
        if (file_exists(APPPATH . 'libraries/WaGateway.php')) {
            $this->load->library('wagateway');
            $this->wagateway->kirim_batal($sesi_id, $alasan);
        }

        $this->session->set_flashdata('success', 'Sesi mediasi berhasil dibatalkan. Notifikasi pembatalan telah dikirimkan ke para pihak.');
        redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
    }

    /**
     * Edit / Perbarui jadwal mediasi yang sudah ada.
     */
    public function edit($sesi_id) {
        $mediator_id = $this->_verify_mediator_role();
        $sesi = $this->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) show_404();

        $perkara = $this->M_perkara->get_by_id($sesi->perkara_id);
        if (!$perkara) show_404();

        if (!in_array('admin', $this->session->userdata('roles') ?: [$this->session->userdata('role')]) && $sesi->mediator_id != $mediator_id) {
            show_error('Akses ditolak.', 403);
        }

        if ($perkara->status === 'selesai' || $this->M_hasil->is_exist($sesi->perkara_id)) {
            $this->session->set_flashdata('error', 'Tidak dapat mengubah sesi karena perkara ini sudah selesai.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        if ($sesi->status_sesi !== 'terjadwal') {
            $this->session->set_flashdata('error', 'Sesi yang sudah selesai, dibatalkan, atau dijadwal ulang tidak dapat diedit.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('tgl_mediasi', 'Tanggal Mediasi', 'required');
            $this->form_validation->set_rules('jam_mulai',   'Jam Mulai',       'required');
            $this->form_validation->set_rules('jam_selesai', 'Jam Selesai',     'required');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $tgl         = $this->input->post('tgl_mediasi');
                $jam_mulai   = $this->input->post('jam_mulai');
                $jam_selesai = $this->input->post('jam_selesai');
                $ruangan_id  = $this->input->post('ruangan_id') ?: null;

                if (!empty($perkara->tgl_batas_mediasi) && strtotime($tgl) > strtotime($perkara->tgl_batas_mediasi)) {
                    $this->session->set_flashdata('error', 'Tanggal mediasi (' . date('d/m/Y', strtotime($tgl)) . ') melebihi Batas Akhir Mediasi (' . date('d/m/Y', strtotime($perkara->tgl_batas_mediasi)) . ').');
                    redirect("mediator/jadwal/edit/{$sesi_id}");
                    return;
                }

                if ($jam_selesai <= $jam_mulai) {
                    $this->session->set_flashdata('error', 'Jam selesai harus lebih akhir daripada jam mulai.');
                    redirect("mediator/jadwal/edit/{$sesi_id}");
                    return;
                }

                if ($ruangan_id) {
                    $konflik = $this->M_jadwal->check_conflict($ruangan_id, $tgl, $jam_mulai, $jam_selesai, $sesi_id);
                    if ($konflik) {
                        $this->session->set_flashdata('error', "BENTROK RUANGAN! Ruangan tersebut sudah digunakan oleh Perkara No. {$konflik->nomor_perkara} pada jam tersebut.");
                        redirect("mediator/jadwal/edit/{$sesi_id}");
                        return;
                    }
                }

                $keterangan      = $this->input->post('keterangan', true) ?: null;

                $update_data = [
                    'tgl_mediasi'     => $tgl,
                    'jam_mulai'       => $jam_mulai,
                    'jam_selesai'     => $jam_selesai,
                    'ruangan_id'      => $ruangan_id,
                    'tempat_lain'     => $this->input->post('tempat_lain', true) ?: null,
                    'link_virtual'    => $link_virtual,
                    'platform_virtual'=> $platform_virtual,
                    'keterangan'      => $keterangan,
                ];

                $this->M_jadwal->update($sesi_id, $update_data);

                // Broadcast Notifikasi Perubahan Jadwal ke Email & WA
                if (file_exists(APPPATH . 'libraries/EmailGateway.php')) {
                    $this->load->library('emailgateway');
                    $this->emailgateway->kirim_jadwal($sesi->perkara_id, $tgl, $jam_mulai, $jam_selesai, $ruangan_id, $link_virtual, $platform_virtual, 'edit', $keterangan);
                }
                if (file_exists(APPPATH . 'libraries/WaGateway.php')) {
                    $this->load->library('wagateway');
                    $this->wagateway->kirim_jadwal($sesi->perkara_id, $tgl, $jam_mulai, $jam_selesai, $ruangan_id, $link_virtual, $platform_virtual, 'edit', $keterangan);
                }

                $this->session->set_flashdata('success', 'Jadwal mediasi berhasil diperbarui. Notifikasi perubahan telah dikirimkan ke para pihak.');
                redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
                return;
            }
        }

        $this->render('mediator/jadwal/edit', [
            'title'    => "Edit Jadwal Mediasi — {$perkara->nomor_perkara}",
            'sesi'     => $sesi,
            'perkara'  => $perkara,
            'ruangans' => $this->M_ruangan->get_aktif(),
        ]);
    }

    /**
     * Catat Kehadiran Pihak & Selesaikan Sesi Mediasi
     */
    public function selesai($sesi_id) {
        $mediator_id = $this->_verify_mediator_role();
        $sesi = $this->M_jadwal->get_by_id($sesi_id);
        if (!$sesi) show_404();

        $perkara = $this->M_perkara->get_by_id($sesi->perkara_id);
        if (!$perkara) show_404();

        if (!in_array('admin', $this->session->userdata('roles') ?: [$this->session->userdata('role')]) && $sesi->mediator_id != $mediator_id) {
            show_error('Akses ditolak.', 403);
        }

        if ($sesi->status_sesi !== 'terjadwal') {
            $this->session->set_flashdata('error', 'Hanya sesi berstatus Terjadwal yang dapat diselesaikan.');
            redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
            return;
        }

        $pihak = $this->M_perkara->get_pihak($sesi->perkara_id);

        if ($this->input->post()) {
            $this->form_validation->set_rules('catatan_sesi', 'Catatan Jalannya Sesi', 'required|trim|min_length[5]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $catatan_sesi  = $this->input->post('catatan_sesi', true);
                $raw_kehadiran = $this->input->post('kehadiran') ?: [];

                $kehadiran_batch = [];
                $unselected_pihak = [];
                foreach ($pihak as $p) {
                    $st = $raw_kehadiran[$p->id]['status'] ?? null;
                    if (!in_array($st, ['hadir', 'absen', 'kuasa'])) {
                        $unselected_pihak[] = $p->nama;
                    }
                    $ct = $raw_kehadiran[$p->id]['catatan'] ?? null;
                    $kehadiran_batch[] = [
                        'pihak_id'         => $p->id,
                        'status_kehadiran' => $st ?: 'hadir',
                        'catatan'          => trim($ct) ?: null,
                    ];
                }

                if (!empty($unselected_pihak)) {
                    $this->session->set_flashdata('error', 'Harap pilih status presensi (Hadir/Absen/Kuasa) untuk: ' . implode(', ', $unselected_pihak));
                    redirect("mediator/jadwal/selesai/{$sesi_id}");
                    return;
                }

                $this->M_jadwal->selesaikan_sesi($sesi_id, $catatan_sesi, $kehadiran_batch);

                $this->session->set_flashdata('success', 'Sesi mediasi berhasil diselesaikan dan presensi kehadiran pihak telah tersimpan.');
                redirect("mediator/perkara_saya/detail/{$sesi->perkara_id}");
                return;
            }
        }

        $existing_kehadiran = $this->M_jadwal->get_kehadiran($sesi_id);

        $this->render('mediator/jadwal/selesai', [
            'title'              => "Presensi Kehadiran & Selesaikan Sesi — Perkara {$perkara->nomor_perkara}",
            'sesi'               => $sesi,
            'perkara'            => $perkara,
            'pihak'              => $pihak,
            'existing_kehadiran' => $existing_kehadiran,
        ]);
    }
}


