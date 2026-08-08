<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="hover:text-gray-700">Detail Perkara</a>
        <span>›</span>
        <span class="text-gray-900 font-medium">Presensi & Selesaikan Sesi</span>
    </nav>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-blue-200 font-semibold tracking-wider uppercase">SIPO-MEDIASI</span>
                    <h2 class="text-xl font-bold font-mono mt-0.5"><?= htmlspecialchars($perkara->nomor_perkara) ?></h2>
                </div>
                <div class="text-right">
                    <span class="text-xs text-blue-200 block">Jadwal Sesi</span>
                    <span class="text-sm font-bold bg-white/10 px-3 py-1 rounded-lg border border-white/20">
                        <?= tgl_indo($sesi->tgl_mediasi, true) ?> (<?= substr($sesi->jam_mulai,0,5) ?> - <?= substr($sesi->jam_selesai,0,5) ?> WITA)
                    </span>
                </div>
            </div>
        </div>

        <form action="<?= site_url("mediator/jadwal/selesai/{$sesi->id}") ?>" method="POST" class="p-6 space-y-6">

            <!-- Flash Error -->
            <?php if ($this->session->flashdata('error')): ?>
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span><?= $this->session->flashdata('error') ?></span>
            </div>
            <?php endif; ?>

            <!-- Section 1: Presensi Kehadiran Pihak -->
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-blue-600"></i>
                    <span>Presensi Kehadiran Para Pihak</span>
                </h3>
                <p class="text-xs text-gray-500 mb-4">Tandai status kehadiran setiap pihak yang hadir pada sesi mediasi ini.</p>

                <?php
                $map_kehadiran = [];
                if (!empty($existing_kehadiran)) {
                    foreach ($existing_kehadiran as $ek) {
                        $map_kehadiran[$ek->pihak_id] = $ek;
                    }
                }
                ?>

                <div class="space-y-3">
                    <?php foreach ($pihak as $p): ?>
                    <?php
                        $ex    = $map_kehadiran[$p->id] ?? null;
                        $cur_st= $ex ? $ex->status_kehadiran : 'hadir';
                        $cur_ct= $ex ? $ex->catatan : '';
                        $jenis_label = $p->jenis === 'penggugat' ? 'Penggugat / Pemohon' : ($p->jenis === 'tergugat' ? 'Tergugat / Termohon' : 'Turut Tergugat');
                        $badge_clr   = $p->jenis === 'penggugat' ? 'bg-blue-100 text-blue-800' : ($p->jenis === 'tergugat' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800');
                    ?>
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 hover:bg-white transition-all space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $badge_clr ?>"><?= $jenis_label ?></span>
                                <h4 class="text-sm font-bold text-gray-900 mt-1"><?= htmlspecialchars($p->nama) ?></h4>
                                <?php if ($p->kuasa_hukum): ?>
                                <p class="text-xs text-gray-500">Kuasa Hukum: <?= htmlspecialchars($p->kuasa_hukum) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Radio Choices -->
                            <div class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-gray-200 text-xs">
                                <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="kehadiran[<?= $p->id ?>][status]" value="hadir" <?= $cur_st === 'hadir' ? 'checked' : '' ?> class="text-blue-600 focus:ring-blue-500">
                                    <span class="font-semibold text-green-700">✓ Hadir</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="kehadiran[<?= $p->id ?>][status]" value="kuasa" <?= $cur_st === 'kuasa' ? 'checked' : '' ?> class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-semibold text-indigo-700">👔 Kuasa Hukum</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="kehadiran[<?= $p->id ?>][status]" value="absen" <?= $cur_st === 'absen' ? 'checked' : '' ?> class="text-red-600 focus:ring-red-500">
                                    <span class="font-semibold text-red-600">✕ Tidak Hadir</span>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Pihak (Opsional) -->
                        <div>
                            <input type="text" name="kehadiran[<?= $p->id ?>][catatan]" value="<?= htmlspecialchars($cur_ct ?? '') ?>"
                                class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                placeholder="Catatan tambahan pihak (opsional, misal: Hadir didampingi Kuasa Hukum)">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Section 2: Catatan Sesi -->
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                    <span>Catatan Jalannya Sesi Mediasi <span class="text-red-500">*</span></span>
                </h3>
                <p class="text-xs text-gray-500 mb-2">Tuliskan resume/catatan jalannya sesi mediasi ini sebelum menyelesaikan sesi.</p>

                <textarea name="catatan_sesi" rows="4" required
                    class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                    placeholder="Contoh: Para pihak hadir lengkap. Pihak Penggugat menyampaikan usulan perdamaian terkait hak asuh anak. Sesi berikutnya disepakati untuk pembacaan tanggapan Tergugat."><?= set_value('catatan_sesi', $sesi->catatan_sesi ?? $sesi->keterangan ?? '') ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    ‹ Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-md">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Simpan & Selesaikan Sesi</span>
                </button>
            </div>

        </form>
    </div>
</div>
