<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_sync — Controller Sync API SIPP Mediasi (Khusus Admin)
 */
class Api_sync extends MY_Controller {

    protected $role_required = 'admin';

    public function __construct() {
        parent::__construct();
        $this->load->library('SippApi', null, 'sippapi');
    }

    /**
     * Endpoint AJAX/HTTP untuk memicu sinkronisasi manual
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

        redirect('admin/dashboard');
    }

    /**
     * Test koneksi / fetch raw API tanpa menyimpan ke DB
     */
    public function check() {
        $raw = $this->sippapi->fetch_data();
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($raw));
    }

    /**
     * Endpoint Tes Koneksi API dari Form Pengaturan Admin
     */
    public function test() {
        $url = trim($this->input->post('api_mediasi_url', true));
        $key = trim($this->input->post('api_mediasi_key', true));

        $res = $this->sippapi->test_connection($url, $key);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($res));
    }
}
