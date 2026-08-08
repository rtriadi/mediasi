<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perkara_saya — Controller Mediator untuk melihat perkara yang di-assign
 */
class Perkara_saya extends MY_Controller {

    protected $role_required = ['mediator', 'admin', 'hakim', 'pp'];

    public function __construct() {
        parent::__construct();
        $this->load->model(['M_perkara', 'M_jadwal', 'M_hasil']);
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
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
        ];
        $page   = max(1, (int)($this->input->get('page') ?: 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total    = $this->M_perkara->count_by_mediator($mediator_id, $filter);
        $perkaras = $this->M_perkara->get_by_mediator($mediator_id, $filter, $limit, $offset);

        $this->render('mediator/perkara/index', [
            'title'      => 'Perkara Mediasi Saya',
            'perkaras'   => $perkaras,
            'total'      => $total,
            'filter'     => $filter,
            'page'       => $page,
            'pagination' => $this->paginate('mediator/perkara_saya', $total, $limit, $filter),
        ]);
    }

    public function detail($id) {
        $mediator_id = $this->_verify_mediator_role();
        $perkara     = $this->M_perkara->get_by_id($id);
        if (!$perkara) show_404();

        $user_roles    = $this->session->userdata('roles') ?: [$this->session->userdata('role')];
        $is_privileged = in_array('admin', $user_roles) || in_array('pp', $user_roles) || in_array('hakim', $user_roles);

        if (!$is_privileged && $perkara->mediator_id != $mediator_id) {
            show_error('Akses ditolak. Penugasan Anda pada perkara ini telah digantikan oleh mediator lain.', 403);
        }

        $pihak  = $this->M_perkara->get_pihak($id);
        $jadwal = $this->M_jadwal->get_by_perkara($id);
        $hasil  = $this->M_hasil->get_by_perkara($id);

        $riwayat_mediator = $this->M_perkara->get_riwayat_mediator($id);

        $this->render('mediator/perkara/detail', [
            'title'            => "Detail Perkara {$perkara->nomor_perkara}",
            'perkara'          => $perkara,
            'pihak'            => $pihak,
            'jadwal'           => $jadwal,
            'hasil'            => $hasil,
            'riwayat_mediator' => $riwayat_mediator,
        ]);
    }
}
