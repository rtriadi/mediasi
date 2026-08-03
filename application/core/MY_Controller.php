<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Base controller dengan auth middleware & role check
 * Semua controller yang butuh login harus extend class ini.
 */
#[\AllowDynamicProperties]
class MY_Controller extends CI_Controller {

    /** Override di child controller. Bisa string atau array of roles. */
    protected $role_required = null;

    public function __construct() {
        parent::__construct();
        $this->_check_auth();
    }

    private function _check_auth() {
        $current_uri = $this->uri->segment(1);
        // Skip auth untuk controller publik dan auth
        if (in_array($current_uri, ['auth', 'publik'])) return;

        if (!$this->session->userdata('user_id')) {
            redirect('auth');
        }

        // Cek role jika diperlukan
        if ($this->role_required) {
            $user_role   = $this->session->userdata('role');
            $user_roles  = $this->session->userdata('roles') ?: [$user_role];
            $is_mediator = $this->session->userdata('is_mediator');

            $req = is_array($this->role_required) ? $this->role_required : [$this->role_required];
            
            // User allowed if any of their assigned roles intersect with required roles
            $allowed = (count(array_intersect($user_roles, $req)) > 0);

            // Izinkan akses area Mediator jika user mempunyai role mediator atau admin
            $controller_dir = $this->uri->segment(1);
            if ($controller_dir === 'mediator' && (in_array('mediator', $user_roles) || in_array('admin', $user_roles))) {
                $allowed = true;
            }

            if (!$allowed) {
                show_error('Akses ditolak. Anda tidak memiliki hak untuk mengakses halaman ini.', 403);
            }
        }
    }

    /**
     * Render view dalam layout.
     * @param string $view       Path view relatif dari application/views/
     * @param array  $data       Data yang di-pass ke view
     * @param string $layout     Nama layout (tanpa ekstensi), default 'main'
     */
    protected function render($view, $data = [], $layout = 'main') {
        $user_role  = $this->session->userdata('role');
        $user_roles = $this->session->userdata('roles') ?: [$user_role];

        $data['user'] = [
            'id'          => $this->session->userdata('user_id'),
            'nama'        => $this->session->userdata('nama'),
            'role'        => $user_role,
            'roles'       => $user_roles,
            'is_mediator' => $this->session->userdata('is_mediator'),
            'mediator_id' => $this->session->userdata('mediator_id'),
        ];
        $this->load->view("layouts/{$layout}", array_merge($data, ['content_view' => $view]));
    }
}
