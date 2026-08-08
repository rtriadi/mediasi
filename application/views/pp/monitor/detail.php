<div class="max-w-5xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('pp/monitor') ?>" class="hover:text-gray-700">Monitor Perkara</a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= htmlspecialchars($perkara->nomor_perkara) ?></span>
    </nav>

    <!-- Header Status -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-5 mb-5">
            <div>
                <span class="text-xs text-blue-600 font-semibold tracking-wider uppercase"><?= htmlspecialchars($perkara->jenis_perkara) ?></span>
                <h2 class="text-2xl font-bold text-gray-900 font-mono mt-1"><?= htmlspecialchars($perkara->nomor_perkara) ?></h2>
                <p class="text-sm text-gray-500 mt-1">Majelis Hakim: <strong><?= htmlspecialchars($perkara->nama_hakim) ?></strong> · PP: <?= htmlspecialchars($perkara->nama_pp) ?></p>
            </div>
            <div class="text-right flex flex-col items-end">
                <span class="text-xs text-gray-400 block mb-1">Status</span>
                <div class="flex items-center gap-3">
                    <a href="<?= site_url("pp/perkara/edit/{$perkara->id}") ?>"
                        class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-all border border-indigo-200">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Perkara</span>
                    </a>
                    <a href="<?= site_url("pp/monitor/cetak_resume/{$perkara->id}") ?>" target="_blank"
                        class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-all border border-slate-300">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Lembar Mediasi</span>
                    </a>
                    <?php if ($perkara->status === 'menunggu'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Menunggu</span>
                    <?php elseif ($perkara->status === 'proses'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dalam Proses</span>
                    <?php elseif ($perkara->status === 'selesai'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Selesai</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Mediator Ditugaskan</p>
                <p class="font-semibold text-gray-900"><?= htmlspecialchars($perkara->nama_mediator ?: '—') ?></p>
                <p class="text-xs text-gray-500 capitalize mt-0.5">Mediator <?= $perkara->jenis_mediator === 'hakim' ? 'Hakim (Gratis)' : 'Non-Hakim' ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Batas Akhir Mediasi</p>
                <p class="font-semibold text-gray-900"><?= date('d F Y', strtotime($perkara->tgl_batas_mediasi)) ?></p>
                <?php
                $sisa_hari = ceil((strtotime($perkara->tgl_batas_mediasi) - time()) / 86400);
                if ($perkara->status !== 'selesai'):
                ?>
                <p class="text-xs font-semibold mt-0.5 <?= $sisa_hari < 7 ? 'text-red-600' : 'text-green-600' ?>">
                    Sisa Waktu: <?= $sisa_hari ?> hari lagi
                </p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Hasil Mediasi</p>
                <?php if ($hasil): ?>
                    <p class="font-bold text-sm capitalize
                        <?= $hasil->hasil === 'berhasil' ? 'text-green-700' : ($hasil->hasil === 'berhasil_sebagian' ? 'text-amber-700' : 'text-red-700') ?>">
                        <?= str_replace('_', ' ', $hasil->hasil) ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">Tgl Hasil: <?= date('d/m/Y', strtotime($hasil->tgl_hasil)) ?></p>
                <?php else: ?>
                    <p class="text-gray-400 italic">Belum ada hasil</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Layout Grid: Pihak & Sesi -->
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
                                <p class="text-indigo-600 font-mono mt-0.5">✉️ <?= htmlspecialchars($p->email) ?></p>
                                <?php endif; ?>
                                <?php if ($p->no_hp): ?>
                                <p class="text-blue-600 font-mono mt-0.5">📱 <?= htmlspecialchars($p->no_hp) ?></p>
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

            <!-- Riwayat Sesi -->
            <div>
                <h3 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Riwayat Sesi Mediasi</h3>

                <?php if (empty($jadwal)): ?>
                <p class="text-sm text-gray-400 italic text-center py-6">Belum ada sesi mediasi yang dijadwalkan.</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jadwal as $idx => $s): ?>
                    <?php
                        $st = $s->status_sesi ?? 'terjadwal';
                        $is_act = !in_array($st, ['batal', 'dijadwal_ulang']);
                    ?>
                    <div class="flex items-start gap-4 p-3 rounded-lg border border-gray-200 bg-gray-50/50 <?= !$is_act ? 'opacity-60 bg-amber-50/30' : '' ?>">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                            #<?= $idx + 1 ?>
                        </div>
                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-sm <?= !$is_act ? 'line-through text-gray-500' : '' ?>"><?= tgl_indo($s->tgl_mediasi, true) ?></span>
                                    <?php if ($st === 'dijadwal_ulang'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">↩ Dijadwal Ulang</span>
                                    <?php elseif ($st === 'batal'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">✕ Dibatalkan</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Terjadwal</span>
                                    <?php endif; ?>
                                </div>
                                <span class="font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded font-medium"><?= substr($s->jam_mulai,0,5) ?> – <?= substr($s->jam_selesai,0,5) ?> WITA</span>
                            </div>
                            <p class="text-gray-600 mt-1">
                                📍 <strong>Tempat:</strong> <?= htmlspecialchars($s->nama_ruangan ?: $s->tempat_lain ?: '—') ?>
                            </p>
                            <?php if (!empty($s->alasan_reschedule)): ?>
                            <p class="text-amber-800 mt-1 italic bg-amber-100/60 px-2 py-0.5 rounded inline-block">"<?= htmlspecialchars($s->alasan_reschedule) ?>"</p>
                            <?php elseif ($s->keterangan): ?>
                            <p class="text-gray-500 mt-1 italic">"<?= htmlspecialchars($s->keterangan) ?>"</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Detail Laporan Akhir -->
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
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            File Laporan PDF Mediasi
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
