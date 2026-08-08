<div class="max-w-5xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('mediator/perkara_saya') ?>" class="hover:text-gray-700">Perkara Saya</a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= htmlspecialchars($perkara->nomor_perkara) ?></span>
    </nav>

    <!-- Top Card Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-5 mb-5">
            <div>
                <span class="text-xs text-blue-600 font-semibold tracking-wider uppercase"><?= htmlspecialchars($perkara->jenis_perkara) ?></span>
                <h2 class="text-2xl font-bold text-gray-900 font-mono mt-1"><?= htmlspecialchars($perkara->nomor_perkara) ?></h2>
                <p class="text-sm text-gray-500 mt-1">Majelis Hakim: <strong><?= htmlspecialchars($perkara->nama_hakim) ?></strong> · PP: <?= htmlspecialchars($perkara->nama_pp) ?></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= site_url("pp/monitor/cetak_resume/{$perkara->id}") ?>" target="_blank"
                    class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-lg transition-all border border-slate-300">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Lembar Mediasi</span>
                </a>
                <?php if ($perkara->status !== 'selesai'): ?>
                <a href="<?= site_url("mediator/jadwal/tambah/{$perkara->id}") ?>"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                    + Buat Jadwal Mediasi
                </a>
                <a href="<?= site_url("mediator/hasil/input/{$perkara->id}") ?>"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                    ✓ Input Hasil Mediasi
                </a>
                <?php else: ?>
                <span class="px-3 py-1 bg-green-100 text-green-800 font-semibold text-sm rounded-full">Mediasi Selesai</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Status Mediasi</p>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase
                    <?= $perkara->status === 'proses' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                    <?= $perkara->status ?>
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Batas Akhir Mediasi</p>
                <p class="font-semibold text-gray-900"><?= date('d F Y', strtotime($perkara->tgl_batas_mediasi)) ?></p>
                <?php
                $sisa_hari = ceil((strtotime($perkara->tgl_batas_mediasi) - time()) / 86400);
                if ($perkara->status !== 'selesai'):
                ?>
                <p class="text-xs font-semibold mt-0.5 <?= $sisa_hari < 7 ? 'text-red-600' : 'text-green-600' ?>">
                    Sisa Waktu: <?= $sisa_hari ?> hari
                </p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Hasil Mediasi</p>
                <?php if ($hasil): ?>
                    <p class="font-bold text-sm capitalize <?= $hasil->hasil === 'berhasil' ? 'text-green-700' : ($hasil->hasil === 'berhasil_sebagian' ? 'text-amber-700' : 'text-red-700') ?>">
                        <?= str_replace('_', ' ', $hasil->hasil) ?>
                    </p>
                <?php else: ?>
                    <p class="text-gray-400 italic">Belum diinput</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pihak & Jadwal Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Col 1: Para Pihak -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:col-span-1">
            <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Para Pihak</h3>

            <div class="space-y-4">
                <?php foreach (['penggugat' => 'Penggugat', 'tergugat' => 'Tergugat', 'turut_tergugat' => 'Turut Tergugat'] as $k => $label): ?>
                    <?php
                    $list = array_filter($pihak, function($p) use ($k) { return $p->jenis === $k; });
                    if (!empty($list)):
                    ?>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"><?= $label ?></p>
                        <ul class="space-y-2">
                            <?php foreach ($list as $p): ?>
                            <li class="p-2.5 bg-gray-50 rounded-lg text-xs">
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($p->nama) ?></p>
                                <?php if ($p->kuasa_hukum): ?>
                                <p class="text-gray-500 mt-0.5">Kuasa: <?= htmlspecialchars($p->kuasa_hukum) ?></p>
                                <?php endif; ?>
                                <?php if ($p->email): ?>
                                <p class="text-indigo-600 font-mono mt-0.5 flex items-center gap-1">
                                    <span>✉️</span>
                                    <a href="mailto:<?= htmlspecialchars($p->email) ?>" class="hover:underline">
                                        <?= htmlspecialchars($p->email) ?>
                                    </a>
                                </p>
                                <?php endif; ?>
                                <?php if ($p->no_hp): ?>
                                <p class="text-blue-600 font-mono mt-0.5 flex items-center gap-1">
                                    <span>📱</span>
                                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', $p->no_hp) ?>" target="_blank" class="hover:underline">
                                        <?= htmlspecialchars($p->no_hp) ?> (Chat WA)
                                    </a>
                                </p>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Riwayat Penugasan Mediator -->
            <?php if (!empty($riwayat_mediator) && count($riwayat_mediator) > 0): ?>
            <div class="mt-6 pt-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Riwayat Mediator</h4>
                    <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium"><?= count($riwayat_mediator) ?> Penugasan</span>
                </div>
                <div class="space-y-2.5 text-xs">
                    <?php foreach ($riwayat_mediator as $rm): ?>
                    <?php $is_active = is_null($rm->tgl_diganti); ?>
                    <div class="p-2.5 rounded-lg border <?= $is_active ? 'border-blue-200 bg-blue-50/40' : 'border-gray-200 bg-gray-50/60 opacity-80' ?>">
                        <div class="flex items-center justify-between font-semibold">
                            <span class="text-gray-900"><?= htmlspecialchars($rm->nama_mediator) ?></span>
                            <?php if ($is_active): ?>
                                <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-full font-bold">Aktif</span>
                            <?php else: ?>
                                <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-semibold">Digantikan</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">
                            🕒 Assign: <?= date('d/m/Y H:i', strtotime($rm->tgl_assign)) ?>
                        </p>
                        <?php if (!$is_active && $rm->tgl_diganti): ?>
                        <p class="text-[11px] text-red-600 mt-0.5 font-medium">
                            🛑 Diganti: <?= date('d/m/Y H:i', strtotime($rm->tgl_diganti)) ?> <?= $rm->nama_diganti_oleh ? '('.htmlspecialchars($rm->nama_diganti_oleh).')' : '' ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Col 2: Riwayat Sesi & Laporan -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:col-span-2 space-y-6">
            <div>
                <?php
                $has_unfinished = false;
                if (!empty($jadwal)) {
                    foreach ($jadwal as $sj) {
                        if (($sj->status_sesi ?? 'terjadwal') === 'terjadwal') { $has_unfinished = true; break; }
                    }
                }
                ?>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Riwayat Sesi Mediasi</h3>
                    <?php if ($perkara->status !== 'selesai'): ?>
                        <?php if ($has_unfinished): ?>
                        <span class="text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 font-semibold" title="Selesaikan sesi mediasi aktif terlebih dahulu">
                            ⚠️ Selesaikan Sesi Aktif Terlebih Dahulu
                        </span>
                        <?php else: ?>
                        <a href="<?= site_url("mediator/jadwal/tambah/{$perkara->id}") ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg transition-all shadow-sm">
                            + Tambah Sesi Mediasi
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if (empty($jadwal)): ?>
                <p class="text-sm text-gray-400 italic text-center py-6">Belum ada sesi mediasi yang dijadwalkan.</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jadwal as $idx => $s): ?>
                    <?php
                        $st = $s->status_sesi ?? 'terjadwal';
                        $is_act = !in_array($st, ['batal', 'dijadwal_ulang']);
                    ?>
                    <div class="flex items-start gap-4 p-3.5 rounded-xl border border-gray-200 bg-gray-50/50 <?= !$is_act ? 'opacity-60' : '' ?>">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                            Sesi <?= $idx + 1 ?>
                        </div>
                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-sm"><?= tgl_indo($s->tgl_mediasi, true) ?></span>
                                    <?php if ($st === 'terjadwal'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Terjadwal</span>
                                    <?php elseif ($st === 'selesai'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Selesai</span>
                                    <?php elseif ($st === 'dijadwal_ulang'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">↩ Dijadwal Ulang</span>
                                    <?php elseif ($st === 'batal'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">✕ Dibatalkan</span>
                                    <?php endif; ?>
                                </div>
                                <span class="font-mono text-blue-700 bg-blue-50 px-2 py-0.5 rounded font-medium"><?= substr($s->jam_mulai,0,5) ?> – <?= substr($s->jam_selesai,0,5) ?> WITA</span>
                            </div>

                            <p class="text-gray-600 mt-1.5">
                                📍 <strong>Tempat:</strong>
                                <?php if (!empty($s->link_virtual)): ?>
                                    <span class="text-violet-700 font-bold">🎥 <?= htmlspecialchars($s->platform_virtual ?: 'Virtual Meeting') ?></span>
                                    — <a href="<?= htmlspecialchars($s->link_virtual) ?>" target="_blank" class="text-violet-600 underline font-semibold">Buka Link Meeting →</a>
                                <?php else: ?>
                                    <?= htmlspecialchars($s->nama_ruangan ?: $s->tempat_lain ?: '—') ?>
                                <?php endif; ?>
                            </p>

                            <?php if ($s->keterangan): ?>
                            <p class="text-gray-500 mt-1 italic">"<?= htmlspecialchars($s->keterangan) ?>"</p>
                            <?php endif; ?>

                            <?php if (!empty($s->catatan_sesi)): ?>
                            <div class="mt-2 p-2 bg-emerald-50/70 rounded-lg border border-emerald-200/70 text-gray-800">
                                <strong class="text-emerald-800 font-bold block mb-0.5">📝 Catatan Jalannya Sesi:</strong>
                                <p class="italic"><?= nl2br(htmlspecialchars($s->catatan_sesi)) ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($s->kehadiran)): ?>
                            <div class="mt-2 p-2.5 bg-gray-100/80 rounded-lg space-y-1">
                                <span class="font-bold text-gray-700 block text-[11px]">📋 Presensi Kehadiran Pihak:</span>
                                <?php foreach ($s->kehadiran as $kh): ?>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-gray-800">• <?= htmlspecialchars($kh->nama_pihak) ?> <span class="text-gray-500">(<?= htmlspecialchars($kh->jenis_pihak) ?>)</span></span>
                                    <span class="font-bold <?= $kh->status_kehadiran === 'hadir' ? 'text-green-700' : ($kh->status_kehadiran === 'kuasa' ? 'text-indigo-700' : 'text-red-600') ?>">
                                        <?= $kh->status_kehadiran === 'hadir' ? '✓ Hadir' : ($kh->status_kehadiran === 'kuasa' ? '👔 Kuasa' : '✕ Absen') ?>
                                        <?= $kh->catatan ? ' <span class="font-normal italic text-gray-500">('.htmlspecialchars($kh->catatan).')</span>' : '' ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php elseif ($st === 'selesai'): ?>
                            <div class="mt-2 p-2 bg-gray-100/60 rounded-lg text-[11px] text-gray-500 italic">
                                📋 Presensi Kehadiran Pihak: (Presensi belum terdata secara rinci pada sesi ini)
                            </div>
                            <?php endif; ?>

                            <?php if ($perkara->status !== 'selesai'): ?>
                            <div class="mt-2.5 pt-2 border-t border-gray-200/60 flex items-center flex-wrap gap-2">
                                <?php if ($st === 'terjadwal'): ?>
                                <a href="<?= site_url("mediator/jadwal/selesai/{$s->id}") ?>" class="text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-1 rounded-lg shadow-sm transition-all">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Presensi & Selesaikan Sesi
                                </a>
                                <a href="<?= site_url("mediator/jadwal/edit/{$s->id}") ?>" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 px-2.5 py-1 bg-blue-50 rounded border border-blue-200">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Jadwal
                                </a>
                                <a href="<?= site_url("mediator/jadwal/reschedule/{$s->id}") ?>" class="text-[11px] font-bold text-amber-700 hover:text-amber-900 px-2.5 py-1 bg-amber-50 rounded border border-amber-200">
                                    <i class="fa-solid fa-calendar-days mr-1"></i> Reschedule
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($hasil): ?>
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Dokumen & Catatan Laporan</h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3 text-sm">
                    <?php if ($hasil->catatan): ?>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Catatan Mediator:</p>
                        <p class="text-gray-800 italic bg-white p-3 rounded-lg border border-gray-200 text-xs">
                            "<?= nl2br(htmlspecialchars($hasil->catatan)) ?>"
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($hasil->file_laporan): ?>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs text-gray-600 flex items-center gap-1.5 font-medium">
                            📄 File Laporan: <?= htmlspecialchars($hasil->file_laporan) ?>
                        </span>
                        <a href="<?= site_url("pp/monitor/download_laporan/{$perkara->id}") ?>"
                            class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                            ↓ Unduh File PDF
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
