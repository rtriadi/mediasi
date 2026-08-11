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

            <!-- Data Utama Perkara (Informasi API SIPP & Pengaturan Mediasi) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Perkara <span class="text-xs text-blue-600 font-normal">(Informasi API SIPP)</span></label>
                    <div class="p-2.5 bg-gray-100/80 border border-gray-200 rounded-lg text-sm font-mono font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-file-contract text-blue-600"></i>
                        <span><?= htmlspecialchars($perkara->nomor_perkara) ?></span>
                    </div>
                    <input type="hidden" name="nomor_perkara" value="<?= htmlspecialchars($perkara->nomor_perkara) ?>">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Perkara <span class="text-xs text-blue-600 font-normal">(Informasi API SIPP)</span></label>
                    <div class="p-2.5 bg-gray-100/80 border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-indigo-600"></i>
                        <span><?= htmlspecialchars($perkara->jenis_perkara) ?></span>
                    </div>
                    <input type="hidden" name="jenis_perkara_id" value="<?= $perkara->jenis_perkara_id ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Majelis Hakim <span class="text-xs text-blue-600 font-normal">(Informasi API SIPP)</span></label>
                    <?php $clean_nama_hakim = str_replace(['</br>', '<br>', '<br/>', '<br />'], '; ', $perkara->nama_hakim ?? ''); ?>
                    <div class="p-2.5 bg-gray-100/80 border border-gray-200 rounded-lg text-xs font-medium text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-gavel text-amber-600"></i>
                        <span><?= htmlspecialchars($clean_nama_hakim) ?></span>
                    </div>
                    <input type="hidden" name="nama_hakim" value="<?= htmlspecialchars($clean_nama_hakim) ?>">
                </div>

                <div>
                    <label for="tgl_batas_mediasi" class="block text-sm font-semibold text-gray-800 mb-1.5">Batas Akhir Mediasi <span class="text-red-500">*</span></label>
                    <input type="date" id="tgl_batas_mediasi" name="tgl_batas_mediasi" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= $perkara->tgl_batas_mediasi ?>">
                </div>

                <div>
                    <label for="mediator_id" class="block text-sm font-semibold text-gray-800 mb-1.5">Mediator Ditugaskan <span class="text-red-500">*</span></label>
                    <select id="mediator_id" name="mediator_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">— Pilih Mediator —</option>
                        <?php foreach ($mediators as $m): ?>
                        <option value="<?= $m->id ?>" <?= isset($perkara->mediator_id) && $perkara->mediator_id == $m->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m->nama) ?> (Mediator <?= ucfirst($m->jenis) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Data Para Pihak & Kuasa Hukum -->
            <?php
                $penggugats = array_values(array_filter($pihak, function($p){ return $p->jenis === 'penggugat'; }));
                $tergugats  = array_values(array_filter($pihak, function($p){ return $p->jenis === 'tergugat'; }));
                $turuts     = array_values(array_filter($pihak, function($p){ return $p->jenis === 'turut_tergugat'; }));
            ?>
            <div class="space-y-6 mb-8">
                <!-- Section Penggugat -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-blue-600 rounded-full"></span> Penggugat / Pemohon <span class="text-red-500">*</span>
                        </h3>
                    </div>
                    <div id="container-penggugat" class="space-y-3">
                        <?php foreach ($penggugats as $idx => $p): ?>
                        <div class="pihak-row p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5">
                            <input type="hidden" name="pihak_penggugat[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                    <input type="text" name="pihak_penggugat[<?= $idx ?>][nama]" required value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email Pihak</label>
                                    <input type="email" name="pihak_penggugat[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" placeholder="email@pihak.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA Pihak</label>
                                    <input type="tel" name="pihak_penggugat[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                            </div>
                            
                            <!-- Sub-block Kuasa Hukum (Tiap Advokat Memiliki Input Email & No. HP Sendiri) -->
                            <div class="pt-2 border-t border-gray-200/80 space-y-2">
                                <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider">
                                    ⚖️ Daftar Kuasa Hukum <span class="text-[10px] text-blue-500 font-normal">(Informasi API SIPP)</span>
                                </label>
                                <?php if (!empty($p->kuasa_details)): ?>
                                    <div class="space-y-2">
                                        <?php foreach ($p->kuasa_details as $k_idx => $kd): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-blue-50/70 p-3 rounded-lg border border-blue-100 items-center">
                                            <div>
                                                <span class="block text-[10px] font-semibold text-blue-600 uppercase tracking-wider mb-1">Kuasa Hukum <?= $k_idx + 1 ?></span>
                                                <div class="text-xs font-bold text-blue-900 bg-white border border-blue-200 px-2.5 py-1.5 rounded-lg shadow-2xs flex items-center gap-1.5">
                                                    <i class="fa-solid fa-user-shield text-blue-600 text-xs"></i>
                                                    <span><?= htmlspecialchars($kd->nama) ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider mb-1">Email Kuasa Hukum</label>
                                                <input type="email" name="kuasa[<?= $kd->id ?>][email]" value="<?= htmlspecialchars($kd->email ?? '') ?>" placeholder="email.kuasa@domain.com" class="w-full border border-blue-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider mb-1">No. HP / WA Kuasa Hukum</label>
                                                <input type="tel" name="kuasa[<?= $kd->id ?>][no_hp]" value="<?= htmlspecialchars($kd->no_hp ?? '') ?>" placeholder="08xxxxxxxxxx" class="w-full border border-blue-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-2.5 bg-blue-50/40 rounded-lg text-xs text-gray-400 italic">
                                        — Tidak ada Kuasa Hukum (Informasi API SIPP) —
                                    </div>
                                <?php endif; ?>
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
                    </div>
                    <div id="container-tergugat" class="space-y-3">
                        <?php foreach ($tergugats as $idx => $p): ?>
                        <div class="pihak-row p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5">
                            <input type="hidden" name="pihak_tergugat[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                                    <input type="text" name="pihak_tergugat[<?= $idx ?>][nama]" required value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email Pihak</label>
                                    <input type="email" name="pihak_tergugat[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" placeholder="email@pihak.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA Pihak</label>
                                    <input type="tel" name="pihak_tergugat[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                            </div>
                            
                            <!-- Sub-block Kuasa Hukum Tergugat -->
                            <div class="pt-2 border-t border-gray-200/80 space-y-2">
                                <label class="block text-[11px] font-bold text-amber-800 uppercase tracking-wider">
                                    ⚖️ Daftar Kuasa Hukum <span class="text-[10px] text-amber-600 font-normal">(Informasi API SIPP)</span>
                                </label>
                                <?php if (!empty($p->kuasa_details)): ?>
                                    <div class="space-y-2">
                                        <?php foreach ($p->kuasa_details as $k_idx => $kd): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-amber-50/70 p-3 rounded-lg border border-amber-100 items-center">
                                            <div>
                                                <span class="block text-[10px] font-semibold text-amber-700 uppercase tracking-wider mb-1">Kuasa Hukum <?= $k_idx + 1 ?></span>
                                                <div class="text-xs font-bold text-amber-900 bg-white border border-amber-200 px-2.5 py-1.5 rounded-lg shadow-2xs flex items-center gap-1.5">
                                                    <i class="fa-solid fa-user-shield text-amber-600 text-xs"></i>
                                                    <span><?= htmlspecialchars($kd->nama) ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-1">Email Kuasa Hukum</label>
                                                <input type="email" name="kuasa[<?= $kd->id ?>][email]" value="<?= htmlspecialchars($kd->email ?? '') ?>" placeholder="email.kuasa@domain.com" class="w-full border border-amber-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-amber-500 font-mono bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-1">No. HP / WA Kuasa Hukum</label>
                                                <input type="tel" name="kuasa[<?= $kd->id ?>][no_hp]" value="<?= htmlspecialchars($kd->no_hp ?? '') ?>" placeholder="08xxxxxxxxxx" class="w-full border border-amber-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-amber-500 font-mono bg-white">
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-2.5 bg-amber-50/40 rounded-lg text-xs text-gray-400 italic">
                                        — Tidak ada Kuasa Hukum (Informasi API SIPP) —
                                    </div>
                                <?php endif; ?>
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
                    </div>
                    <div id="container-turut" class="space-y-3">
                        <?php foreach ($turuts as $idx => $p): ?>
                        <div class="pihak-row p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5">
                            <input type="hidden" name="pihak_turut[<?= $idx ?>][id]" value="<?= $p->id ?>">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Pihak</label>
                                    <input type="text" name="pihak_turut[<?= $idx ?>][nama]" value="<?= htmlspecialchars($p->nama) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email Pihak</label>
                                    <input type="email" name="pihak_turut[<?= $idx ?>][email]" value="<?= htmlspecialchars($p->email ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA Pihak</label>
                                    <input type="tel" name="pihak_turut[<?= $idx ?>][no_hp]" value="<?= htmlspecialchars($p->no_hp ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                </div>
                            </div>
                            
                            <!-- Sub-block Kuasa Hukum Turut Tergugat -->
                            <div class="pt-2 border-t border-gray-200/80 space-y-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                                    ⚖️ Daftar Kuasa Hukum <span class="text-[10px] text-gray-400 font-normal">(Informasi API SIPP)</span>
                                </label>
                                <?php if (!empty($p->kuasa_details)): ?>
                                    <div class="space-y-2">
                                        <?php foreach ($p->kuasa_details as $k_idx => $kd): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-100/70 p-3 rounded-lg border border-gray-200 items-center">
                                            <div>
                                                <span class="block text-[10px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Kuasa Hukum <?= $k_idx + 1 ?></span>
                                                <div class="text-xs font-bold text-gray-900 bg-white border border-gray-200 px-2.5 py-1.5 rounded-lg shadow-2xs flex items-center gap-1.5">
                                                    <i class="fa-solid fa-user-shield text-gray-600 text-xs"></i>
                                                    <span><?= htmlspecialchars($kd->nama) ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-wider mb-1">Email Kuasa Hukum</label>
                                                <input type="email" name="kuasa[<?= $kd->id ?>][email]" value="<?= htmlspecialchars($kd->email ?? '') ?>" placeholder="email.kuasa@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-wider mb-1">No. HP / WA Kuasa Hukum</label>
                                                <input type="tel" name="kuasa[<?= $kd->id ?>][no_hp]" value="<?= htmlspecialchars($kd->no_hp ?? '') ?>" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-2.5 bg-gray-100/40 rounded-lg text-xs text-gray-400 italic">
                                        — Tidak ada Kuasa Hukum (Informasi API SIPP) —
                                    </div>
                                <?php endif; ?>
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

    const subBg = type === 'penggugat' ? 'bg-blue-50/60 border-blue-200 text-blue-700' : (type === 'tergugat' ? 'bg-amber-50/60 border-amber-200 text-amber-800' : 'bg-gray-100/70 border-gray-200 text-gray-700');

    const html = `
    <div class="pihak-row p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5 relative animate-fade-in">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Pihak ${reqBadge}</label>
                <input type="text" name="pihak_${type}[${idx}][nama]" ${nameReq} placeholder="Nama ${labelText}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email Pihak</label>
                <input type="email" name="pihak_${type}[${idx}][email]" placeholder="email@pihak.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
            </div>
            <div class="relative pr-8">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. HP / WA Pihak</label>
                <input type="tel" name="pihak_${type}[${idx}][no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
                <button type="button" onclick="this.closest('.pihak-row').remove()" class="absolute right-0 top-6 text-red-500 hover:text-red-700 p-1 text-lg font-bold" title="Hapus Pihak">×</button>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3 ${subBg} p-3 rounded-lg">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider mb-1">⚖️ Kuasa Hukum (Nama)</label>
                <input type="text" name="pihak_${type}[${idx}][kuasa_hukum]" placeholder="Nama Kuasa Hukum / Pengacara" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider mb-1">Email Kuasa Hukum</label>
                <input type="email" name="pihak_${type}[${idx}][kuasa_email]" placeholder="kuasa@domain.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider mb-1">No. HP / WA Kuasa Hukum</label>
                <input type="tel" name="pihak_${type}[${idx}][kuasa_no_hp]" placeholder="08xxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 font-mono bg-white">
            </div>
        </div>
    </div>`;

    document.getElementById(`container-${type}`).insertAdjacentHTML('beforeend', html);
}
</script>
