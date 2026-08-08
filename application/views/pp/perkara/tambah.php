<div class="max-w-5xl mx-auto">
    <!-- Stepper -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-semibold text-sm flex items-center justify-center shadow-md">1</div>
            <span class="text-sm font-semibold text-blue-600">Data Perkara & Para Pihak</span>
        </div>
        <div class="w-16 h-0.5 bg-gray-200 mx-4"></div>
        <div class="flex items-center gap-3 opacity-40">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 font-semibold text-sm flex items-center justify-center">2</div>
            <span class="text-sm font-medium text-gray-500">Pilih & Penetapan Mediator</span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Langkah 1: Informasi Perkara & Data Pihak</h2>

        <form id="form-tambah-perkara" method="POST" action="<?= site_url('pp/perkara/tambah') ?>">
            <input type="hidden" name="step" value="1">

            <!-- Data Utama Perkara -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <label for="nomor_perkara" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Perkara <span class="text-red-500">*</span></label>
                    <input type="text" id="nomor_perkara" name="nomor_perkara" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                        placeholder="Contoh: 123/Pdt.G/2026/PA.Gtlo"
                        value="<?= set_value('nomor_perkara') ?>">
                </div>

                <div>
                    <label for="jenis_perkara_id" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Perkara <span class="text-red-500">*</span></label>
                    <select id="jenis_perkara_id" name="jenis_perkara_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">— Pilih Jenis Perkara —</option>
                        <?php foreach ($jenis_perkara as $jp): ?>
                        <option value="<?= $jp->id ?>" <?= set_select('jenis_perkara_id', $jp->id) ?>><?= htmlspecialchars($jp->nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="nama_hakim" class="block text-sm font-medium text-gray-700 mb-1.5">Majelis Hakim <span class="text-red-500">*</span></label>
                    <select id="nama_hakim" name="nama_hakim" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">— Pilih Majelis Hakim —</option>
                        <?php if (!empty($hakim_list)): ?>
                            <?php foreach ($hakim_list as $h): ?>
                            <option value="<?= htmlspecialchars($h->nama) ?>" <?= set_select('nama_hakim', $h->nama) ?>>
                                <?= htmlspecialchars($h->nama) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="tgl_batas_mediasi" class="block text-sm font-medium text-gray-700 mb-1.5">Batas Akhir Mediasi <span class="text-red-500">*</span></label>
                    <input type="date" id="tgl_batas_mediasi" name="tgl_batas_mediasi" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= set_value('tgl_batas_mediasi', date('Y-m-d', strtotime('+30 days'))) ?>">
                    <p class="text-xs text-gray-400 mt-1">Default 30 hari kerja sesuai PERMA Mediasi</p>
                </div>
            </div>

            <!-- Dynamic Section Pihak -->
            <div class="space-y-8 mb-8">
                <!-- Section Penggugat -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-blue-600 rounded-full"></span> Penggugat / Pemohon <span class="text-red-500">*</span>
                        </h3>
                        <button type="button" onclick="addPihakRow('penggugat')"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                            + Tambah Penggugat
                        </button>
                    </div>
                    <div id="container-penggugat" class="space-y-3">
                        <!-- Initial Row -->
                        <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                <input type="text" name="pihak_penggugat[0][nama]" required placeholder="Nama Penggugat 1 *" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
                                <input type="text" name="pihak_penggugat[0][kuasa_hukum]" placeholder="Kuasa Hukum (opsional)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email (Default Notifikasi)</label>
                                <input type="email" name="pihak_penggugat[0][email]" placeholder="email@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                <input type="tel" name="pihak_penggugat[0][no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Tergugat -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-amber-600 rounded-full"></span> Tergugat / Termohon <span class="text-red-500">*</span>
                        </h3>
                        <button type="button" onclick="addPihakRow('tergugat')"
                            class="text-xs text-amber-600 hover:text-amber-800 font-medium flex items-center gap-1 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition-colors">
                            + Tambah Tergugat
                        </button>
                    </div>
                    <div id="container-tergugat" class="space-y-3">
                        <!-- Initial Row -->
                        <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                <input type="text" name="pihak_tergugat[0][nama]" required placeholder="Nama Tergugat 1 *" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
                                <input type="text" name="pihak_tergugat[0][kuasa_hukum]" placeholder="Kuasa Hukum (opsional)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email (Default Notifikasi)</label>
                                <input type="email" name="pihak_tergugat[0][email]" placeholder="email@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                <input type="tel" name="pihak_tergugat[0][no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Turut Tergugat -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-gray-500 rounded-full"></span> Turut Tergugat (Opsional)
                        </h3>
                        <button type="button" onclick="addPihakRow('turut')"
                            class="text-xs text-gray-600 hover:text-gray-800 font-medium flex items-center gap-1 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                            + Tambah Turut Tergugat
                        </button>
                    </div>
                    <div id="container-turut" class="space-y-3">
                        <!-- Initial empty state or dynamic rows -->
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="submit" id="btn-next"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    Lanjut: Pilih Mediator →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let counter = { penggugat: 1, tergugat: 1, turut: 0 };

function addPihakRow(type) {
    const idx = counter[type]++;
    const container = document.getElementById(`container-${type}`);
    const nameAttr = type === 'penggugat' ? `pihak_penggugat[${idx}]` : (type === 'tergugat' ? `pihak_tergugat[${idx}]` : `pihak_turut[${idx}]`);
    const label = type === 'penggugat' ? `Penggugat ${idx+1}` : (type === 'tergugat' ? `Tergugat ${idx+1}` : `Turut Tergugat ${idx+1}`);

    const div = document.createElement('div');
    div.className = 'pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200 relative group';
    div.innerHTML = `
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
            <input type="text" name="${nameAttr}[nama]" required placeholder="Nama ${label} *" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
            <input type="text" name="${nameAttr}[kuasa_hukum]" placeholder="Kuasa Hukum (opsional)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email (Default Notifikasi)</label>
            <input type="email" name="${nameAttr}[email]" placeholder="email@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
        </div>
        <div class="relative pr-8">
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
            <input type="tel" name="${nameAttr}[no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
            <button type="button" onclick="this.closest('.pihak-row').remove()" class="absolute right-0 top-6 text-red-500 hover:text-red-700 p-1 text-lg font-bold">×</button>
        </div>
    `;
    container.appendChild(div);
}
</script>
