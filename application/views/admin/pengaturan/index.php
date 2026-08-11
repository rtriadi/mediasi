<?php
$logo_url     = get_app_logo_url();
$email_active = get_app_setting('email_notif_active', '1') === '1';
$wa_active    = get_app_setting('wa_notif_active', '0') === '1';
?>

<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Pengaturan Aplikasi</h2>
        <p class="text-xs text-slate-500 mt-1">Konfigurasi identitas sistem, nama aplikasi, slogan, satuan kerja, logo, SMTP Email, dan WhatsApp</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Form Section -->
    <div class="lg:col-span-2">
        <form action="<?= site_url('admin/pengaturan') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 md:p-8 space-y-6">
            
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900 font-heading flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-blue-600"></i>
                    <span>Identitas & Branded Media</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Perubahan akan langsung berdampak dinamis di seluruh halaman aplikasi, header, sidebar, login, dan cetak PDF.</p>
            </div>

            <!-- Nama Aplikasi (Singkatan) -->
            <div>
                <label for="nama_aplikasi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Aplikasi (Singkatan) <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_aplikasi" name="nama_aplikasi" required
                    value="<?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?>"
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-heading font-extrabold text-slate-900"
                    placeholder="Contoh: SIPO-MEDIASI">
                <p class="text-[11px] text-slate-400 mt-1">Singkatan nama aplikasi yang ditampilkan di header & sidebar.</p>
            </div>

            <!-- Slogan / Kepanjangan Aplikasi -->
            <div>
                <label for="slogan_aplikasi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Slogan / Kepanjangan Aplikasi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="slogan_aplikasi" name="slogan_aplikasi" required
                    value="<?= htmlspecialchars(get_app_setting('slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara')) ?>"
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-800"
                    placeholder="Contoh: Sistem Informasi Pengelolaan Mediasi Perkara">
                <p class="text-[11px] text-slate-400 mt-1">Slogan atau deskripsi panjang aplikasi.</p>
            </div>

            <!-- Nama Satker -->
            <div>
                <label for="nama_satker" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Satuan Kerja (Satker) <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_satker" name="nama_satker" required
                    value="<?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>"
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-semibold text-slate-900"
                    placeholder="Contoh: Pengadilan Agama Gorontalo Kelas I A">
                <p class="text-[11px] text-slate-400 mt-1">Nama instansi/satker resmi pengadilan yang tercantum di header dan dokumen cetak.</p>
            </div>

            <!-- Upload Logo Aplikasi -->
            <div>
                <label for="logo_aplikasi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Logo Aplikasi
                </label>
                <div class="flex items-center gap-4">
                    <?php $logo_url = get_app_logo_url(); ?>
                    <?php if ($logo_url): ?>
                    <div class="relative group">
                        <img src="<?= $logo_url ?>" alt="Logo Aplikasi" class="w-16 h-16 object-contain rounded-2xl border border-slate-200 p-2 bg-slate-50 shadow-sm">
                        <a href="<?= site_url('admin/pengaturan/hapus_logo') ?>" 
                           onclick="return confirm('Hapus logo aplikasi saat ini?')"
                           class="absolute -top-2 -right-2 bg-rose-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-md hover:bg-rose-700 transition-colors"
                           title="Hapus Logo">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-300 text-slate-400 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <?php endif; ?>

                    <div class="flex-1">
                        <input type="file" id="logo_aplikasi" name="logo_aplikasi" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Format: PNG, JPG, WEBP, atau SVG. Ukuran maks 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Section Email SMTP (Notifikasi Utama / Default) -->
            <div class="border-t border-slate-200 pt-6">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-base font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-envelope-open-text text-blue-600 text-lg"></i>
                        <span>Notifikasi Email (SMTP Default)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pengaturan kirim notifikasi panggilan mediasi resmi via Email (Utama / Default).</p>
                </div>

                <!-- Status Notifikasi Email Toggle -->
                <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-200/80 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-blue-950 block">Status Notifikasi Email (Default)</span>
                        <p class="text-[11px] text-blue-800 mt-0.5">Aktifkan untuk secara otomatis mengirim undangan mediasi HTML ke email pihak berperkara saat sesi dijadwalkan.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <?php $email_active = get_app_setting('email_notif_active', '1') === '1'; ?>
                        <input type="checkbox" name="email_notif_active" value="1" class="sr-only peer" <?= $email_active ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- SMTP Host -->
                    <div>
                        <label for="smtp_host" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            SMTP Server / Host
                        </label>
                        <input type="text" id="smtp_host" name="smtp_host"
                            value="<?= htmlspecialchars(get_app_setting('smtp_host', 'smtp.gmail.com')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono text-slate-900"
                            placeholder="smtp.gmail.com">
                        <p class="text-[11px] text-slate-400 mt-1">Host SMTP (misal: smtp.gmail.com / mail.pa-gorontalo.go.id).</p>
                    </div>

                    <!-- SMTP Port -->
                    <div>
                        <label for="smtp_port" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            SMTP Port
                        </label>
                        <input type="text" id="smtp_port" name="smtp_port"
                            value="<?= htmlspecialchars(get_app_setting('smtp_port', '587')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono text-slate-900"
                            placeholder="587">
                        <p class="text-[11px] text-slate-400 mt-1">Port SSL (465) / TLS (587).</p>
                    </div>

                    <!-- SMTP User / Email Pengirim -->
                    <div>
                        <label for="smtp_user" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            SMTP User / Email Pengirim
                        </label>
                        <input type="email" id="smtp_user" name="smtp_user"
                            value="<?= htmlspecialchars(get_app_setting('smtp_user', '')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono text-slate-900"
                            placeholder="notifikasi@pa-gorontalo.go.id">
                        <p class="text-[11px] text-slate-400 mt-1">Alamat email resmi untuk pengiriman notifikasi.</p>
                    </div>

                    <!-- SMTP Password -->
                    <div>
                        <label for="smtp_pass" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            SMTP Password / App Password
                        </label>
                        <input type="password" id="smtp_pass" name="smtp_pass"
                            value="<?= htmlspecialchars(get_app_setting('smtp_pass', '')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono text-slate-900"
                            placeholder="••••••••••••">
                        <p class="text-[11px] text-slate-400 mt-1">Password akun atau Gmail App Password.</p>
                    </div>

                    <!-- SMTP Enkripsi / Crypto -->
                    <div>
                        <label for="smtp_crypto" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Protokol Enkripsi
                        </label>
                        <?php $crypto = get_app_setting('smtp_crypto', 'tls'); ?>
                        <select id="smtp_crypto" name="smtp_crypto" class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-900 bg-white">
                            <option value="tls" <?= $crypto === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                            <option value="ssl" <?= $crypto === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                        </select>
                    </div>

                    <!-- Nama Pengirim Email -->
                    <div>
                        <label for="mail_from_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Display Pengirim
                        </label>
                        <input type="text" id="mail_from_name" name="mail_from_name"
                            value="<?= htmlspecialchars(get_app_setting('mail_from_name', 'SIPO-MEDIASI PA Gorontalo')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-900 font-semibold"
                            placeholder="SIPO-MEDIASI PA Gorontalo">
                    </div>
                </div>
            </div>

            <!-- Section WhatsApp Gateway -->
            <div class="border-t border-slate-200 pt-6">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-base font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i>
                        <span>Notifikasi WhatsApp Gateway</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pengaturan kirim notifikasi otomatis panggilan mediasi ke WhatsApp pihak berperkara.</p>
                </div>

                <!-- Status Notifikasi WA Toggle -->
                <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-200/80 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-950 block">Status Notifikasi WhatsApp</span>
                        <p class="text-[11px] text-emerald-800 mt-0.5">Aktifkan untuk mengirim notifikasi jadwal otomatis saat mediator membuat jadwal baru.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <?php $wa_active = get_app_setting('wa_notif_active', '0') === '1'; ?>
                        <input type="checkbox" name="wa_notif_active" value="1" class="sr-only peer" <?= $wa_active ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="space-y-4">
                    <!-- WA Token API -->
                    <div>
                        <label for="wa_api_token" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            API Token / Key WhatsApp (Fonnte)
                        </label>
                        <input type="text" id="wa_api_token" name="wa_api_token"
                            value="<?= htmlspecialchars(get_app_setting('wa_api_token', '')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 font-mono text-slate-900"
                            placeholder="Masukkan token API Fonnte (misal: 8xHk9#2m...)">
                        <p class="text-[11px] text-slate-400 mt-1">Dapatkan token API gratis/premium di layanan gateway seperti <strong class="text-slate-700">Fonnte.com</strong>.</p>
                    </div>

                    <!-- WA Endpoint URL -->
                    <div>
                        <label for="wa_api_url" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            API Endpoint URL
                        </label>
                        <input type="text" id="wa_api_url" name="wa_api_url"
                            value="<?= htmlspecialchars(get_app_setting('wa_api_url', 'https://api.fonnte.com/send')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 font-mono text-slate-800"
                            placeholder="https://api.fonnte.com/send">
                        <p class="text-[11px] text-slate-400 mt-1">Default endpoint URL untuk pengiriman pesan cURL POST.</p>
                    </div>
                </div>
            </div>

            <!-- Section SIPP API Integration -->
            <div class="border-t border-slate-200 pt-6">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-base font-bold text-slate-900 font-heading flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-down text-indigo-600 text-lg"></i>
                        <span>Integrasi API SIPP Mediasi</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasi endpoint URL API SIPP, API Key (Header X-API-KEY), durasi batas mediasi, dan auto-sync.</p>
                </div>

                <!-- Status Auto Sync API Toggle -->
                <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-200/80 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-indigo-950 block">Status Auto-Sync API SIPP</span>
                        <p class="text-[11px] text-indigo-800 mt-0.5">Aktifkan untuk mengizinkan cronjob latar belakang menarik data baru secara berkala.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <?php $api_sync_auto = get_app_setting('api_sync_auto', '1') === '1'; ?>
                        <input type="checkbox" name="api_sync_auto" value="1" class="sr-only peer" <?= $api_sync_auto ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="space-y-4">
                    <!-- SIPP API Endpoint URL + Button Tes Koneksi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="api_mediasi_url" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                API Endpoint URL <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" onclick="testApiConnection()" id="btn-test-api"
                                class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-1 rounded-lg text-xs transition-colors border border-indigo-200 shadow-sm active:scale-95">
                                <i class="fa-solid fa-rotate text-indigo-600" id="icon-test-api"></i>
                                <span id="text-test-api">Tes Koneksi & Sync Manual</span>
                            </button>
                        </div>
                        <input type="text" id="api_mediasi_url" name="api_mediasi_url" required
                            value="<?= htmlspecialchars(get_app_setting('api_mediasi_url', 'http://192.168.100.5/perkara360/api/mediasi')) ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 font-mono text-slate-900"
                            placeholder="http://192.168.100.5/perkara360/api/mediasi">
                        <p class="text-[11px] text-slate-400 mt-1">URL Endpoint API SIPP untuk mengambil data mediasi perkara (Format JSON).</p>
                    </div>

                    <!-- Result Alert Box for API Test Connection -->
                    <div id="test-api-result" class="hidden p-3.5 rounded-xl text-xs border font-medium"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- SIPP API Key (Header X-API-KEY) -->
                        <div class="sm:col-span-1">
                            <label for="api_mediasi_key" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                API Key (X-API-KEY)
                            </label>
                            <input type="text" id="api_mediasi_key" name="api_mediasi_key"
                                value="<?= htmlspecialchars(get_app_setting('api_mediasi_key', '')) ?>"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 font-mono text-slate-900"
                                placeholder="Header Key">
                            <p class="text-[11px] text-slate-400 mt-1">Dikirim via Header <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-700 font-mono">X-API-KEY</code>.</p>
                        </div>

                        <!-- Interval Cronjob Auto Sync (Menit) -->
                        <div>
                            <label for="api_sync_interval_menit" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Interval Cronjob (Menit)
                            </label>
                            <?php $interval_menit = (int)get_app_setting('api_sync_interval_menit', '15'); ?>
                            <input type="number" id="api_sync_interval_menit" name="api_sync_interval_menit" min="1" max="1440" required
                                value="<?= $interval_menit ?>"
                                oninput="updateCronHelper(this.value)"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 font-bold text-slate-900"
                                placeholder="15">
                            <p class="text-[11px] text-slate-400 mt-1">Jarak waktu eksekusi cronjob (misal: tiap 15 menit).</p>
                        </div>

                        <!-- Batas Waktu Mediasi (Hari Kalender) -->
                        <div>
                            <label for="batas_waktu_mediasi_hari" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Batas Mediasi (Hari)
                            </label>
                            <input type="number" id="batas_waktu_mediasi_hari" name="batas_waktu_mediasi_hari" min="1" max="180"
                                value="<?= htmlspecialchars(get_app_setting('batas_waktu_mediasi_hari', '30')) ?>"
                                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 font-bold text-slate-900"
                                placeholder="30">
                            <p class="text-[11px] text-slate-400 mt-1">Default 30 hari kalender sejak penetapan.</p>
                        </div>
                    </div>

                    <!-- Cronjob Command Helper Guide -->
                    <div class="p-4 bg-slate-900 text-slate-100 rounded-2xl border border-slate-800 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-terminal"></i>
                                <span>Panduan Command Cronjob</span>
                            </span>
                            <span class="text-[10px] bg-indigo-900/80 text-indigo-300 px-2 py-0.5 rounded-full font-mono">Tiap <span id="cron-interval-badge"><?= $interval_menit ?></span> Menit</span>
                        </div>
                        
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Linux / cPanel Crontab Command:</label>
                            <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 font-mono text-xs text-emerald-400 flex items-center justify-between overflow-x-auto">
                                <code id="crontab-string">*/<?= $interval_menit ?> * * * * php <?= FCPATH ?>cronjob_api_sync.php</code>
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('crontab-string').innerText); alert('Command Crontab disalin!')" 
                                    class="text-slate-400 hover:text-white text-xs ml-2 px-2 py-1 bg-slate-800 rounded-md hover:bg-slate-700">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Windows Task Scheduler Command:</label>
                            <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 font-mono text-xs text-blue-300 flex items-center justify-between overflow-x-auto">
                                <code id="windows-cron-string">C:\xampp\php\php.exe "<?= FCPATH ?>cronjob_api_sync.php"</code>
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('windows-cron-string').innerText); alert('Command Windows disalin!')" 
                                    class="text-slate-400 hover:text-white text-xs ml-2 px-2 py-1 bg-slate-800 rounded-md hover:bg-slate-700">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php $last_sync = get_app_setting('api_last_sync', ''); ?>
                    <?php if ($last_sync): ?>
                    <div class="p-3 bg-slate-100 rounded-xl flex items-center justify-between text-xs text-slate-600">
                        <span><i class="fa-solid fa-clock-rotate-left mr-1.5 text-slate-500"></i>Terakhir Sinkronisasi API:</span>
                        <strong class="font-mono text-slate-900"><?= date('d-m-Y H:i:s', strtotime($last_sync)) ?> WITA</strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-6 py-3 rounded-xl text-xs transition-all shadow-md shadow-blue-600/20">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>

        </form>
    </div>

    <!-- Live Preview Card -->
    <div class="space-y-6">
        
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-900 font-heading mb-4 flex items-center gap-2">
                <i class="fa-solid fa-eye text-blue-600"></i>
                <span>Preview Brand Header & Sidebar</span>
            </h3>

            <!-- Sidebar Header Preview -->
            <div class="bg-slate-950 text-white p-5 rounded-2xl mb-4 border border-slate-800 shadow-inner">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-2 tracking-widest">Tampilan Brand Sidebar</span>
                <div class="flex items-center gap-3.5">
                    <?php if ($logo_url): ?>
                    <img src="<?= $logo_url ?>" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/10 p-1 shadow-md">
                    <?php else: ?>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/30">
                        <i class="fa-solid fa-scale-balanced text-white text-lg"></i>
                    </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm tracking-tight text-white leading-none"><?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?></h4>
                        <span class="text-[10px] font-semibold tracking-wider uppercase text-blue-400 block mt-1"><?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?></span>
                    </div>
                </div>
            </div>

            <!-- Login Brand Preview -->
            <div class="bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-inner text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-3 tracking-widest">Tampilan Header Login & Publik</span>
                <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl mb-3 shadow-lg">
                    <?php if ($logo_url): ?>
                    <img src="<?= $logo_url ?>" alt="Logo" class="w-10 h-10 object-contain">
                    <?php else: ?>
                    <i class="fa-solid fa-scale-balanced text-2xl text-white"></i>
                    <?php endif; ?>
                </div>
                <h4 class="text-lg font-extrabold text-white font-heading tracking-tight"><?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?></h4>
                <p class="text-blue-300 text-xs font-medium mt-0.5"><?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?></p>
                <p class="text-slate-400 text-[11px] mt-1 italic font-light">"<?= htmlspecialchars(get_app_setting('slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara')) ?>"</p>
            </div>

            <!-- Status Email Card -->
            <div class="mt-4 p-4 rounded-2xl border <?= $email_active ? 'bg-blue-50 border-blue-200 text-blue-950' : 'bg-slate-50 border-slate-200 text-slate-700' ?>">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl <?= $email_active ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600' ?> flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold block">Status Email Notifikasi</span>
                        <span class="text-[11px] font-semibold <?= $email_active ? 'text-blue-700' : 'text-slate-500' ?>">
                            <?= $email_active ? '● AKTIF (Default Notifikasi Utama)' : '○ NONAKTIF' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status WA Card -->
            <div class="mt-3 p-4 rounded-2xl border <?= $wa_active ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-slate-50 border-slate-200 text-slate-700' ?>">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl <?= $wa_active ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600' ?> flex items-center justify-center font-bold text-sm">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold block">Status WA Notifikasi</span>
                        <span class="text-[11px] font-semibold <?= $wa_active ? 'text-emerald-700' : 'text-slate-500' ?>">
                            <?= $wa_active ? '● AKTIF (Pesan otomatis terkirim)' : '○ NONAKTIF (Pesan otomatis mati)' ?>
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Fullscreen Sync Loader Modal -->
<div id="sync-loader-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden transition-all">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center border border-slate-100 flex flex-col items-center animate-in fade-in zoom-in duration-200">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-4 relative">
            <i class="fa-solid fa-cloud-arrow-down text-2xl text-indigo-600 animate-bounce"></i>
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-600 rounded-full animate-ping"></div>
        </div>
        <h3 class="text-base font-extrabold text-slate-900 font-heading">Memproses Sinkronisasi API...</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">Sedang mengimpor & memperbarui data perkara dari SIPP ke mediasi_db.</p>
        
        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mb-2">
            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-600 h-full w-full animate-pulse"></div>
        </div>
        <span class="text-[11px] text-indigo-600 font-bold font-mono">Mohon tunggu sebentar...</span>
    </div>
</div>

<script>
function updateCronHelper(val) {
    const min = Math.max(1, parseInt(val) || 15);
    document.getElementById('cron-interval-badge').innerText = min;
    document.getElementById('crontab-string').innerText = '*/' + min + ' * * * * php <?= FCPATH ?>cronjob_api_sync.php';
}

function testApiConnection() {
    const btn = document.getElementById('btn-test-api');
    const icon = document.getElementById('icon-test-api');
    const text = document.getElementById('text-test-api');
    const resBox = document.getElementById('test-api-result');
    const loaderModal = document.getElementById('sync-loader-modal');
    const urlVal = document.getElementById('api_mediasi_url').value;
    const keyVal = document.getElementById('api_mediasi_key').value;

    if (!urlVal) {
        alert('Harap isi API Endpoint URL terlebih dahulu.');
        return;
    }

    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    icon.className = 'fa-solid fa-rotate fa-spin text-indigo-600';
    text.innerText = 'Memproses Sync...';

    // Tampilkan Loader Modal Fullscreen
    if (loaderModal) loaderModal.classList.remove('hidden');

    resBox.classList.add('hidden');
    resBox.className = 'p-3.5 rounded-xl text-xs border font-medium hidden';

    const formData = new FormData();
    formData.append('api_mediasi_url', urlVal);
    formData.append('api_mediasi_key', keyVal);

    fetch('<?= site_url("admin/api_sync/test") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        // Sembunyikan Loader Modal Fullscreen
        if (loaderModal) loaderModal.classList.add('hidden');

        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        icon.className = 'fa-solid fa-rotate text-indigo-600';
        text.innerText = 'Tes Koneksi & Sync Manual';

        resBox.classList.remove('hidden');
        if (data.status === 'success') {
            resBox.className = 'p-3.5 rounded-xl text-xs border font-medium bg-emerald-50 text-emerald-900 border-emerald-200 flex items-start gap-2';
            resBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i> <div><strong>Koneksi & Sync Berhasil!</strong><br>' + data.message + '</div>';
        } else {
            resBox.className = 'p-3.5 rounded-xl text-xs border font-medium bg-rose-50 text-rose-900 border-rose-200 flex items-start gap-2';
            resBox.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 text-sm mt-0.5"></i> <div><strong>Koneksi/Sync Gagal:</strong><br>' + (data.message || 'Gagal terhubung ke API SIPP') + '</div>';
        }
    })
    .catch(err => {
        // Sembunyikan Loader Modal Fullscreen
        if (loaderModal) loaderModal.classList.add('hidden');

        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        icon.className = 'fa-solid fa-rotate text-indigo-600';
        text.innerText = 'Tes Koneksi & Sync Manual';

        resBox.classList.remove('hidden');
        resBox.className = 'p-3.5 rounded-xl text-xs border font-medium bg-rose-50 text-rose-900 border-rose-200 flex items-start gap-2';
        resBox.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 text-sm mt-0.5"></i> <div><strong>Koneksi Gagal:</strong><br>Terjadi kesalahan sistem saat menghubungi endpoint API.</div>';
        console.error(err);
    });
}
</script>
