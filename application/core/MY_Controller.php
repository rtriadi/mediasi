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
     * Generate HTML pagination links secara manual (tanpa bergantung CI Pagination library).
     * Menghasilkan URL dengan query string ?page=N yang selalu benar.
     *
     * @param  string $base_url   Controller path (misal: 'admin/master_mediator')
     * @param  int    $total      Total rows
     * @param  int    $per_page   Rows per page
     * @param  array  $extra_qs   Filter params tambahan (search, status, dll)
     * @return string             HTML pagination atau string kosong jika hanya 1 halaman
     */
    protected function paginate($base_url, $total, $per_page, $extra_qs = []) {
        if ($total <= $per_page) return '';

        $total_pages = (int) ceil($total / $per_page);
        $current     = max(1, (int)($this->input->get('page') ?: 1));

        // Bangun base URL dengan filter (tanpa 'page')
        $qs = array_filter($extra_qs, function($v) { return $v !== null && $v !== ''; });
        unset($qs['page']);
        $base = site_url($base_url);
        $sep  = !empty($qs) ? '?' . http_build_query($qs) . '&' : '?';

        $btn_base    = 'display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:0.5rem;font-size:0.75rem;text-decoration:none;transition:all .15s;';
        $btn_num     = $btn_base . 'background:#fff;border:1px solid #e2e8f0;color:#475569;font-weight:500;';
        $btn_active  = $btn_base . 'background:#2563eb;border:1px solid #2563eb;color:#fff;font-weight:700;pointer-events:none;box-shadow:0 1px 3px rgba(37,99,235,.4);';
        $btn_nav     = $btn_base . 'background:#fff;border:1px solid #e2e8f0;color:#64748b;font-size:1rem;font-weight:700;';
        $btn_dis     = $btn_base . 'background:#f8fafc;border:1px solid #e2e8f0;color:#cbd5e1;pointer-events:none;';

        $html  = '<nav style="display:flex;align-items:center;gap:0.25rem;" aria-label="Pagination">';

        // Prev
        if ($current > 1) {
            $html .= '<a href="' . $base . $sep . 'page=' . ($current - 1) . '" style="' . $btn_nav . '">&#8249;</a>';
        } else {
            $html .= '<span style="' . $btn_dis . '">&#8249;</span>';
        }

        // Angka halaman dengan ellipsis
        $range  = 2; // tampilkan N halaman di kiri & kanan halaman aktif
        $pages  = [];
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i === 1 || $i === $total_pages || ($i >= $current - $range && $i <= $current + $range)) {
                $pages[] = $i;
            }
        }

        $prev_p = null;
        foreach ($pages as $p) {
            if ($prev_p !== null && $p - $prev_p > 1) {
                $html .= '<span style="' . $btn_dis . '">…</span>';
            }
            if ($p === $current) {
                $html .= '<span style="' . $btn_active . '">' . $p . '</span>';
            } else {
                $html .= '<a href="' . $base . $sep . 'page=' . $p . '" style="' . $btn_num . '">' . $p . '</a>';
            }
            $prev_p = $p;
        }

        // Next
        if ($current < $total_pages) {
            $html .= '<a href="' . $base . $sep . 'page=' . ($current + 1) . '" style="' . $btn_nav . '">&#8250;</a>';
        } else {
            $html .= '<span style="' . $btn_dis . '">&#8250;</span>';
        }

        $html .= '</nav>';
        return $html;
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
