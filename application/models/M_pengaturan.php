<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_pengaturan — Model untuk membaca & menyimpan pengaturan aplikasi
 */
class M_pengaturan extends CI_Model {

    /**
     * Ambil semua pengaturan sebagai key => value array
     */
    public function get_all_as_array() {
        $rows = $this->db->get('settings')->result();
        $settings = [
            'nama_aplikasi'   => 'SIPO-MEDIASI',
            'slogan_aplikasi' => 'Sistem Informasi Pengelolaan Mediasi Perkara',
            'nama_satker'     => 'Pengadilan Agama Gorontalo',
            'logo_aplikasi'   => '',
            'wa_notif_active'     => '0',
            'wa_api_token'        => '',
            'wa_api_url'          => 'https://api.fonnte.com/send',
            'email_notif_active'  => '1',
            'smtp_host'           => 'smtp.gmail.com',
            'smtp_port'           => '587',
            'smtp_user'           => '',
            'smtp_pass'           => '',
            'smtp_crypto'         => 'tls',
            'mail_from_name'      => 'SIPO-MEDIASI PA Gorontalo',
        ];
        foreach ($rows as $r) {
            $settings[$r->setting_key] = $r->setting_value;
        }
        return $settings;
    }

    /**
     * Ambil satu setting berdasarkan key
     */
    public function get($key, $default = '') {
        $row = $this->db->get_where('settings', ['setting_key' => $key])->row();
        return $row ? $row->setting_value : $default;
    }

    /**
     * Simpan / update setting
     */
    public function save($key, $value) {
        $exist = $this->db->get_where('settings', ['setting_key' => $key])->row();
        if ($exist) {
            return $this->db->where('setting_key', $key)->update('settings', ['setting_value' => $value]);
        } else {
            return $this->db->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
    }

    /**
     * Simpan multiple settings sekaligus
     */
    public function save_batch($array_data) {
        foreach ($array_data as $key => $val) {
            $this->save($key, $val);
        }
        return true;
    }
}
