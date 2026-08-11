<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_app_setting')) {
    /**
     * Ambil nilai pengaturan aplikasi berdasarkan key
     */
    function get_app_setting($key, $default = '') {
        $CI = &get_instance();
        if (!isset($CI->M_pengaturan)) {
            $CI->load->model('M_pengaturan');
        }
        static $cached_settings = null;
        if ($cached_settings === null) {
            $cached_settings = $CI->M_pengaturan->get_all_as_array();
        }
        return isset($cached_settings[$key]) && $cached_settings[$key] !== '' ? $cached_settings[$key] : $default;
    }
}

if (!function_exists('get_app_logo_url')) {
    /**
     * Ambil URL logo aplikasi (atau false jika tidak ada upload)
     */
    function get_app_logo_url() {
        $logo = get_app_setting('logo_aplikasi', '');
        if ($logo && file_exists(FCPATH . 'uploads/settings/' . $logo)) {
            return base_url('uploads/settings/' . $logo);
        }
        return false;
    }
}

if (!function_exists('tgl_indo')) {
    /**
     * Format tanggal ke Bahasa Indonesia (misal: "Senin, 03 Agustus 2026")
     */
    function tgl_indo($date_str = null, $with_day = true) {
        if (!$date_str) {
            $timestamp = time();
        } else {
            $timestamp = is_numeric($date_str) ? $date_str : strtotime($date_str);
        }
        if (!$timestamp) return '—';

        $hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        $bulan = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',      6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',  9  => 'September',
            10 => 'Oktober',   11 => 'November', 12 => 'Desember'
        ];

        $day_name   = $hari[date('l', $timestamp)] ?? date('l', $timestamp);
        $day_num    = date('d', $timestamp);
        $month_num  = (int)date('m', $timestamp);
        $month_name = $bulan[$month_num] ?? date('F', $timestamp);
        $year       = date('Y', $timestamp);

        if ($with_day) {
            return "{$day_name}, {$day_num} {$month_name} {$year}";
        }
        return "{$day_num} {$month_name} {$year}";
    }
}

if (!function_exists('bulan_indo')) {
    /**
     * Ambil nama bulan Bahasa Indonesia berdasarkan angka 1-12
     */
    function bulan_indo($month_num) {
        $bulan = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',      6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',  9  => 'September',
            10 => 'Oktober',   11 => 'November', 12 => 'Desember'
        ];
        return $bulan[(int)$month_num] ?? '';
    }
}

if (!function_exists('get_deadline_badge')) {
    /**
     * Helper membuat Badge Alert Batas Waktu Mediasi (Tenggat 30 Hari PERMA 1/2016)
     */
    function get_deadline_badge($tgl_batas_mediasi, $status_perkara = 'proses') {
        if (empty($tgl_batas_mediasi)) {
            return '<span class="px-2 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-600 rounded-full border border-gray-200">Tenggat -</span>';
        }

        if ($status_perkara === 'selesai') {
            return '<span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200"><i class="fa-solid fa-circle-check text-emerald-600"></i> Selesai</span>';
        }

        $today_time = strtotime(date('Y-m-d'));
        $batas_time = strtotime(date('Y-m-d', strtotime($tgl_batas_mediasi)));
        $diff_days  = (int)floor(($batas_time - $today_time) / 86400);

        if ($diff_days < 0) {
            $abs = abs($diff_days);
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-lg border border-red-300 animate-pulse"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat ' . $abs . ' Hari</span>';
        } elseif ($diff_days <= 3) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-red-50 text-red-600 rounded-lg border border-red-200"><i class="fa-solid fa-clock text-red-500"></i> H-' . $diff_days . ' Batas Waktu</span>';
        } elseif ($diff_days <= 7) {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-lg border border-amber-200"><i class="fa-solid fa-clock text-amber-500"></i> H-' . $diff_days . ' Batas Waktu</span>';
        } else {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200"><i class="fa-solid fa-calendar-check text-emerald-600"></i> ' . $diff_days . ' Hari Tersisa</span>';
        }
    }
}
