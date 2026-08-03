<div class="max-w-2xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('mediator/perkara_saya') ?>" class="hover:text-gray-700">Perkara Saya</a>
        <span>›</span>
        <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="hover:text-gray-700"><?= htmlspecialchars($perkara->nomor_perkara) ?></a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1"><?= $title ?></h2>
        <p class="text-sm text-gray-500 mb-6">Penetapan jadwal sesi mediasi untuk Perkara No. <strong class="text-gray-800 font-mono"><?= htmlspecialchars($perkara->nomor_perkara) ?></strong></p>

        <form id="form-jadwal" method="POST" action="<?= site_url("mediator/jadwal/tambah/{$perkara->id}") ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label for="tgl_mediasi" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mediasi <span class="text-red-500">*</span></label>
                    <input type="date" id="tgl_mediasi" name="tgl_mediasi" required
                        max="<?= $perkara->tgl_batas_mediasi ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= set_value('tgl_mediasi', date('Y-m-d')) ?>">
                    <p class="text-xs text-gray-400 mt-1">Maks. <?= date('d/m/Y', strtotime($perkara->tgl_batas_mediasi)) ?></p>
                </div>

                <div>
                    <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1.5">Jam Mulai (WITA) <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_mulai" name="jam_mulai" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= set_value('jam_mulai', '09:00') ?>">
                </div>

                <div>
                    <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1.5">Jam Selesai (WITA) <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_selesai" name="jam_selesai" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= set_value('jam_selesai', '10:30') ?>">
                </div>
            </div>

            <!-- Pilih Lokasi Sesi -->
            <div class="mb-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block text-sm font-semibold text-gray-800 mb-3">Tempat / Ruangan Mediasi</label>

                <div class="space-y-3">
                    <!-- Opsi 1: Ruangan -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="pilih_tempat" value="ruangan" checked onchange="toggleTempatOption('ruangan')"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Ruangan Mediasi di Kantor Pengadilan</span>
                    </label>

                    <div id="option-ruangan" class="pl-7">
                        <select id="ruangan_id" name="ruangan_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Pilih Ruangan Mediasi —</option>
                            <?php foreach ($ruangans as $r): ?>
                            <option value="<?= $r->id ?>" <?= set_select('ruangan_id', $r->id) ?>><?= htmlspecialchars($r->nama_ruangan) ?> (<?= htmlspecialchars($r->keterangan ?: 'Lantai 1') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-blue-600 mt-1">✓ Sistem akan otomatis mengecek potensi bentrok jadwal ruangan.</p>
                    </div>

                    <!-- Opsi 2: Tempat Lain -->
                    <label class="flex items-center gap-3 cursor-pointer pt-2">
                        <input type="radio" name="pilih_tempat" value="lain" onchange="toggleTempatOption('lain')"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Tempat Lain (Luar Kantor)</span>
                    </label>

                    <div id="option-lain" class="pl-7 hidden">
                        <input type="text" id="tempat_lain" name="tempat_lain"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Kantor Desa X / Balai RW 3">
                    </div>

                    <!-- Opsi 3: Online/Virtual Meeting -->
                    <label class="flex items-center gap-3 cursor-pointer pt-2">
                        <input type="radio" name="pilih_tempat" value="virtual" onchange="toggleTempatOption('virtual')"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-bold">🎥 BARU</span>
                            Online / Virtual Meeting (Zoom, Google Meet, dll.)
                        </span>
                    </label>

                    <div id="option-virtual" class="pl-7 hidden space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Platform Meeting</label>
                                <select id="platform_virtual" name="platform_virtual"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                                    <option value="">— Pilih Platform —</option>
                                    <option value="Zoom">Zoom Meeting</option>
                                    <option value="Google Meet">Google Meet</option>
                                    <option value="MS Teams">Microsoft Teams</option>
                                    <option value="Webex">Cisco Webex</option>
                                    <option value="Lainnya">Platform Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label for="link_virtual" class="block text-xs font-medium text-gray-600 mb-1">Link / URL Meeting <span class="text-violet-600">*</span></label>
                                <input type="url" id="link_virtual" name="link_virtual"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
                                    placeholder="https://zoom.us/j/123456789">
                            </div>
                        </div>
                        <p class="text-xs text-violet-600">✓ Link meeting akan dikirimkan secara otomatis ke para pihak via WhatsApp & Email.</p>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan / Catatan untuk Para Pihak</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Para pihak wajib membawa identitas diri KTP dan dokumen pendukung."></textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                <button type="submit" id="btn-simpan-jadwal"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors shadow-md flex items-center gap-2">
                    Simpan Jadwal Mediasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTempatOption(val) {
    const sections = { ruangan: 'option-ruangan', lain: 'option-lain', virtual: 'option-virtual' };
    const inputs   = {
        ruangan: document.getElementById('ruangan_id'),
        lain:    document.getElementById('tempat_lain'),
        virtual_link: document.getElementById('link_virtual'),
        virtual_platform: document.getElementById('platform_virtual'),
    };

    // Hide all
    Object.values(sections).forEach(id => document.getElementById(id).classList.add('hidden'));
    // Clear all hidden fields
    if (val !== 'ruangan')  inputs.ruangan.value = '';
    if (val !== 'lain')     inputs.lain.value = '';
    if (val !== 'virtual') { inputs.virtual_link.value = ''; inputs.virtual_platform.value = ''; }

    // Show selected
    document.getElementById(sections[val]).classList.remove('hidden');
}
</script>

