<div class="max-w-4xl mx-auto my-4 md:my-8">

    <div class="mb-6 flex items-center justify-between">
        <a href="<?= site_url('publik') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-all">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Cari Perkara Lain</span>
        </a>
    </div>

    <?php if (!$perkara): ?>
    <!-- Not Found State -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-lg p-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-50 text-red-500 rounded-3xl mb-4 border border-red-100">
            <i class="fa-solid fa-file-circle-xmark text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 font-heading mb-2">Perkara Tidak Ditemukan</h3>
        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
            Perkara dengan nomor <strong class="font-mono text-slate-800 bg-slate-100 px-2 py-0.5 rounded">"<?= htmlspecialchars($nomor) ?>"</strong> tidak ditemukan dalam sistem mediasi. Pastikan nomor perkara diinput lengkap dan benar.
        </p>
        <a href="<?= site_url('publik') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl text-sm transition-all shadow-md">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Coba Cari Lagi</span>
        </a>
    </div>

    <?php else: ?>
    <!-- Result Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden mb-6">
        
        <!-- Top Case Hero Card -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white p-6 md:p-8 relative">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <span class="text-xs font-bold text-blue-300 uppercase tracking-widest bg-blue-500/20 px-3 py-1 rounded-full border border-blue-400/30">
                    <?= htmlspecialchars($perkara->jenis_perkara) ?>
                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider
                    <?= $perkara->status === 'proses' ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' ?>">
                    Status: <?= $perkara->status ?>
                </span>
            </div>

            <h2 class="text-2xl md:text-3xl font-extrabold font-mono text-white tracking-tight"><?= htmlspecialchars($perkara->nomor_perkara) ?></h2>
            <p class="text-xs text-slate-400 mt-1">Majelis Hakim: <strong class="text-slate-200"><?= htmlspecialchars($perkara->nama_hakim) ?></strong></p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-800 text-xs">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                    <span class="text-slate-400 block mb-1">Mediator Ditugaskan</span>
                    <strong class="text-sm text-white font-heading"><?= htmlspecialchars($perkara->nama_mediator ?: 'Belum ditetapkan') ?></strong>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                    <span class="text-slate-400 block mb-1">Jenis Mediator</span>
                    <strong class="text-sm text-blue-300 capitalize">Mediator <?= $perkara->jenis_mediator === 'hakim' ? 'Hakim (Gratis)' : 'Non-Hakim' ?></strong>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                    <span class="text-slate-400 block mb-1">Batas Akhir Mediasi</span>
                    <strong class="text-sm text-white font-mono"><?= tgl_indo($perkara->tgl_batas_mediasi, false) ?></strong>
                </div>
            </div>
        </div>

        <!-- Section Para Pihak -->
        <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fa-solid fa-users text-blue-600"></i>
                <span>Para Pihak Berperkara</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach (['penggugat' => 'Penggugat / Pemohon', 'tergugat' => 'Tergugat / Termohon', 'turut_tergugat' => 'Turut Tergugat'] as $k => $label): ?>
                    <?php
                    $list = array_filter($pihak, function($p) use ($k) { return $p->jenis === $k; });
                    if (!empty($list)):
                    ?>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2"><?= $label ?></span>
                        <ul class="space-y-2">
                            <?php foreach ($list as $p): ?>
                            <li class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                <span class="w-2 h-2 rounded-full <?= $k === 'penggugat' ? 'bg-blue-600' : ($k === 'tergugat' ? 'bg-amber-500' : 'bg-purple-500') ?>"></span>
                                <span><?= htmlspecialchars($p->nama) ?></span>
                                <?php if ($p->kuasa_hukum): ?>
                                <span class="text-xs font-normal text-slate-500">(Kuasa: <?= htmlspecialchars($p->kuasa_hukum) ?>)</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section Sesi Mediasi -->
        <div class="p-6 md:p-8">
            <h3 class="text-base font-bold text-slate-900 font-heading mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-blue-600"></i>
                <span>Jadwal & Agenda Sesi Mediasi</span>
            </h3>

            <?php if (empty($jadwal)): ?>
            <div class="p-6 bg-amber-50/80 rounded-2xl border border-amber-200 text-amber-900 text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-amber-600 text-lg mt-0.5"></i>
                <div>
                    <p class="font-semibold">Sesi Mediasi Belum Dijadwalkan</p>
                    <p class="text-xs text-amber-800 mt-1">Mediator perkara belum memasukkan tanggal sesi mediasi. Harap menunggu panggilan atau konfirmasi dari mediator.</p>
                </div>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($jadwal as $idx => $s): ?>
                <?php
                    $status_sesi = $s->status_sesi ?? 'terjadwal';
                    $is_aktif    = !in_array($status_sesi, ['batal', 'dijadwal_ulang']);
                    $is_virtual  = !empty($s->link_virtual);

                    // Styling kard
                    if ($status_sesi === 'dijadwal_ulang') {
                        $card_style  = 'border-amber-200 bg-amber-50/40 opacity-75';
                        $num_style   = 'bg-amber-500 text-white';
                        $time_style  = 'bg-amber-100 text-amber-900 border-amber-200';
                        $badge_html  = '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-200 text-amber-900 border border-amber-300">↩ Dijadwal Ulang</span>';
                    } elseif ($status_sesi === 'batal') {
                        $card_style  = 'border-rose-200 bg-rose-50/40 opacity-75';
                        $num_style   = 'bg-rose-500 text-white';
                        $time_style  = 'bg-rose-100 text-rose-900 border-rose-200';
                        $badge_html  = '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-200 text-rose-900 border border-rose-300">✕ Dibatalkan</span>';
                    } elseif ($status_sesi === 'selesai') {
                        $card_style  = 'border-emerald-300 bg-emerald-50/50 shadow-sm';
                        $num_style   = 'bg-emerald-600 text-white';
                        $time_style  = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                        $badge_html  = '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">✓ Selesai</span>';
                    } else {
                        $card_style  = $s->tgl_mediasi >= date('Y-m-d') ? 'border-blue-500/80 bg-blue-50/40 shadow-sm' : 'border-slate-200 bg-slate-50/50';
                        $num_style   = 'bg-blue-600 text-white';
                        $time_style  = 'bg-blue-100 text-blue-800 border-blue-200';
                        $badge_html  = '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">Terjadwal</span>';
                    }
                ?>
                <div class="p-5 rounded-2xl border-2 transition-all <?= $card_style ?>">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl <?= $num_style ?> font-bold text-xs flex items-center justify-center shadow-sm">
                                <?= $idx + 1 ?>
                            </span>
                            <div>
                                <span class="font-bold text-slate-900 text-base font-heading <?= !$is_aktif ? 'line-through text-slate-500' : '' ?>">
                                    <?= tgl_indo($s->tgl_mediasi, true) ?>
                                </span>
                            </div>
                            <?= $badge_html ?>
                        </div>
                        <span class="px-3.5 py-1 font-mono text-xs font-bold rounded-full border <?= $time_style ?>">
                            🕒 <?= substr($s->jam_mulai,0,5) ?> – <?= substr($s->jam_selesai,0,5) ?> WITA
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-700 pt-3 border-t border-slate-200/80">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tempat / Ruangan:</span>
                            <?php if ($is_virtual): ?>
                                <strong class="text-sm text-violet-800 flex items-center gap-1.5 font-bold">
                                    <span>🎥 <?= htmlspecialchars($s->platform_virtual ?: 'Virtual Meeting') ?></span>
                                </strong>
                                <a href="<?= htmlspecialchars($s->link_virtual) ?>" target="_blank" rel="noopener"
                                    class="text-xs text-violet-600 hover:text-violet-800 underline font-semibold mt-1 inline-block">
                                    🔗 Buka Link Meeting →
                                </a>
                            <?php else: ?>
                                <strong class="text-sm text-slate-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-red-500"></i>
                                    <span><?= htmlspecialchars($s->nama_ruangan ?: $s->tempat_lain ?: '—') ?></span>
                                </strong>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if (!empty($s->alasan_reschedule)): ?>
                                <span class="text-amber-700 font-bold block mb-0.5">Alasan Perubahan / Reschedule:</span>
                                <span class="italic text-amber-900 bg-amber-100/70 px-2.5 py-1 rounded-lg inline-block">"<?= htmlspecialchars($s->alasan_reschedule) ?>"</span>
                            <?php elseif ($s->keterangan): ?>
                                <span class="text-slate-400 block mb-0.5">Catatan Mediator:</span>
                                <span class="italic text-slate-800">"<?= htmlspecialchars($s->keterangan) ?>"</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($hasil): ?>
            <!-- Final Result Banner -->
            <div class="mt-8 p-6 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-950 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block">Hasil Akhir Mediasi</span>
                    <h4 class="text-xl font-extrabold capitalize text-emerald-900 font-heading mt-0.5"><?= str_replace('_', ' ', $hasil->hasil) ?></h4>
                    <p class="text-xs text-emerald-800 mt-1">Proses mediasi perkara ini telah selesai pada tanggal <?= tgl_indo($hasil->tgl_hasil, false) ?>.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php endif; ?>
</div>
