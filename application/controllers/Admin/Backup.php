<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backup — Controller Admin untuk mengunduh cadangan basis data SQL 1-Klik
 */
class Backup extends MY_Controller {

    protected $role_required = 'admin';

    public function index() {
        $this->load->dbutil();

        $prefs = [
            'format'      => 'zip',
            'filename'    => 'mediasi_db_backup_' . date('Y-m-d_H-i-s') . '.sql',
            'add_drop'    => TRUE,
            'add_insert'  => TRUE,
            'newline'     => "\n"
        ];

        $backup =& $this->dbutil->backup($prefs);
        $file_name = 'backup_mediasi_db_' . date('Y-m-d_H-i-s') . '.zip';

        $this->load->helper('download');
        force_download($file_name, $backup);
    }
}
