<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_sync — Controller Sync API SIPP Mediasi (Khusus PP)
 */
class Api_sync extends MY_Controller {

    protected $role_required = 'pp';

    public function __construct() {
        parent::__construct();
        $this->load->library('SippApi', null, 'sippapi');
    }

    /**
     * Endpoint AJAX/HTTP untuk memicu sinkronisasi manual dari dashboard PP
     */
    public function run() {
        $result = $this->sippapi->sync();
        
        if ($this->input->is_ajax_request()) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($result));
            return;
        }

        if ($result['status'] === 'success') {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('pp/dashboard');
    }
}
