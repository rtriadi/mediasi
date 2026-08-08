<div class="max-w-5xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('pp/monitor') ?>" class="hover:text-gray-700">Monitor Perkara</a>
        <span>›</span>
        <a href="<?= site_url("pp/monitor/detail/{$perkara->id}") ?>" class="hover:text-gray-700"><?= htmlspecialchars($perkara->nomor_perkara) ?></a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    <span>Edit Informasi Perkara & Data Pihak</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Perbarui nomor perkara, jenis perkara, majelis hakim, mediator, atau data para pihak</p>
            </div>
            <a href="<?= site_url("pp/monitor/detail/{$perkara->id}") ?>" class="text-xs text-gray-600 hover:text-gray-900 font-semibold px-3 py-1.5 bg-gray-100 rounded-lg">
                ✕ Batal
            </a>
        </div>

        <form id="form-edit-perkara" method="POST" action="<?= site_url("pp/perkara/edit/{$perkara->id}") ?>">

            <!-- Data Utama Perkara -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <label for="nomor_perkara" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Perkara <span class="text-red-500">*</span></label>
                    <input type="text" id="nomor_perkara" name="nomor_perkara" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                        value="<?= htmlspecialchars($perkara->nomor_perkara) ?>">
                </div>

                <div>
                    <label for="jenis_perkara_id" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Perkara <span class="text-red-500">*</span></label>
                    <select id="jenis_perkara_id" name="jenis_perkara_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <?php foreach ($jenis_perkara as $jp): ?>
                        <option value="<?= $jp->id ?>" <?= $perkara->jenis_perkara_id == $jp->id ? 'selected' : '' ?>><?= htmlspecialchars($jp->nama) ?></option>
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
                            <option value="<?= htmlspecialchars($h->nama) ?>" <?= $perkara->nama_hakim == $h->nama ? 'selected' : '' ?>>
                                <?= htmlspecialchars($h->nama) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        $in_list = false;
                        if (!empty($hakim_list)) {
                            foreach ($hakim_list as $h) {
                                if ($h->nama == $perkara->nama_hakim) { $in_list = true; break; }
                            }
                        }
                        if (!$in_list && !empty($perkara->nama_hakim)):
                        ?>
                        <option value="<?= htmlspecialchars($perkara->nama_hakim) ?>" selected><?= htmlspecialchars($perkara->nama_hakim) ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="tgl_batas_mediasi" class="block text-sm font-medium text-gray-700 mb-1.5">Batas Akhir Mediasi <span class="text-red-500">*</span></label>
                    <input type="date" id="tgl_batas_mediasi" name="tgl_batas_mediasi" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= $perkara->tgl_batas_mediasi ?>">
                </div>

                <div class="md:col-span-2">
                    <label for="mediator_id" class="block text-sm font-medium text-gray-700 mb-1.5">Mediator Ditugaskan <span class="text-red-500">*</span></label>
                    <select id="mediator_id" name="mediator_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-semibold">
                        <?php foreach ($mediators as $m): ?>
                        <option value="<?= $m->id ?>" <?= $perkara->mediator_id == $m->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m->nama) ?> (Mediator <?= $m->jenis === 'hakim' ? 'Hakim' : 'Non-Hakim' ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Dynamic Section Pihak -->
            <?php
                $penggugats = array_values(array_filter($pihak, function($p){ return $p->jenis === 'penggugat'; }));
                $tergugats  = array_values(array_filter($pihak, function($p){ return $p->jenis === 'tergugat'; }));
                $turuts     = array_values(array_filter($pihak, function($p){ return $p->jenis === 'turut_tergugat'; }));
            ?>

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
                        <?php foreach ($penggugats as $idx => $p): ?>
                        <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200 relative">
                            <input type="hidden" name="pihak_penggugat[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                <input type="text" name="pihak_penggugat[<?= $idx ?>][nama]" required value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
                                <input type="text" name="pihak_penggugat[<?= $idx ?>][kuasa_hukum]" value="<?= htmlspecialchars($p->kuasa_hukum ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email (Notifikasi)</label>
                                <input type="email" name="pihak_penggugat[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                <input type="tel" name="pihak_penggugat[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                        <?php foreach ($tergugats as $idx => $p): ?>
                        <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <input type="hidden" name="pihak_tergugat[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                <input type="text" name="pihak_tergugat[<?= $idx ?>][nama]" required value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
                                <input type="text" name="pihak_tergugat[<?= $idx ?>][kuasa_hukum]" value="<?= htmlspecialchars($p->kuasa_hukum ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email (Notifikasi)</label>
                                <input type="email" name="pihak_tergugat[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                <input type="tel" name="pihak_tergugat[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Section Turut Tergugat -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span> Turut Tergugat <span class="text-xs text-gray-400 font-normal lowercase">(opsional)</span>
                        </h3>
                        <button type="button" onclick="addPihakRow('turut')"
                            class="text-xs text-gray-600 hover:text-gray-800 font-medium flex items-center gap-1 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                            + Tambah Turut Tergugat
                        </button>
                    </div>
                    <div id="container-turut" class="space-y-3">
                        <?php foreach ($turuts as $idx => $p): ?>
                        <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <input type="hidden" name="pihak_turut[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak</label>
                                <input type="text" name="pihak_turut[<?= $idx ?>][nama]" value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
                                <input type="text" name="pihak_turut[<?= $idx ?>][kuasa_hukum]" value="<?= htmlspecialchars($p->kuasa_hukum ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                                <input type="email" name="pihak_turut[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                <input type="tel" name="pihak_turut[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="<?= site_url("pp/monitor/detail/{$perkara->id}") ?>" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all shadow-md shadow-blue-600/20">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let counter = { penggugat: <?= count($penggugats) ?: 1 ?>, tergugat: <?= count($tergugats) ?: 1 ?>, turut: <?= count($turuts) ?: 0 ?> };

function addPihakRow(type) {
    const idx = counter[type]++;
    const labelText = type === 'penggugat' ? 'Penggugat' : (type === 'tergugat' ? 'Tergugat' : 'Turut Tergugat');
    const nameReq = type !== 'turut' ? 'required' : '';
    const reqBadge = type !== 'turut' ? '<span class="text-red-500">*</span>' : '';

    const html = `
    <div class="pihak-row grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-200 relative animate-fade-in">
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pihak ${reqBadge}</label>
            <input type="text" name="pihak_${type}[${idx}][nama]" ${nameReq} placeholder="Nama ${labelText}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kuasa Hukum</label>
            <input type="text" name="pihak_${type}[${idx}][kuasa_hukum]" placeholder="Kuasa Hukum" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email</label>
            <input type="email" name="pihak_${type}[${idx}][email]" placeholder="email@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA</label>
            <input type="tel" name="pihak_${type}[${idx}][no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono">
        </div>
    </div>`;

    document.getElementById(`container-${type}`).insertAdjacentHTML('beforeend', html);
}
</script>
