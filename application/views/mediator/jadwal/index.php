<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Jadwal Mediasi Saya</h2>
        <p class="text-xs text-slate-500 mt-1">Daftar seluruh sesi mediasi yang telah Anda jadwalkan (Total: <strong><?= $total ?></strong>)</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= site_url('mediator/jadwal') ?>" class="text-xs font-bold text-white bg-blue-600 px-4 py-2.5 rounded-xl shadow-md shadow-blue-600/20">
            <i class="fa-solid fa-list mr-1"></i> Tampilan Tabel
        </a>
        <a href="<?= site_url('mediator/jadwal/kalender') ?>" class="text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <i class="fa-regular fa-calendar-days mr-1"></i> Tampilan Kalender
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('mediator/jadwal') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Filter Bulan</label>
                <select name="bulan" id="filter-bulan" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Bulan</option>
                    <?php
                    $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    foreach ($months as $num => $m):
                    ?>
                    <option value="<?= $num ?>" <?= ($filter['bulan'] == $num) ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full sm:w-28">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Tahun</label>
                <input type="number" name="tahun" id="filter-tahun" value="<?= htmlspecialchars($filter['tahun'] ?? date('Y')) ?>"
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono">
            </div>
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Cari Perkara</label>
                <div class="relative">
                    <input type="text" name="search" id="filter-search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Ketik nomor perkara/catatan...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
        <?php if (!empty($filter['bulan']) || ($filter['tahun'] ?? date('Y')) != date('Y') || !empty($filter['search'])): ?>
        <a href="<?= site_url('mediator/jadwal') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
            <i class="fa-solid fa-rotate-left"></i> Reset Filter
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-100/80 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider w-8">#</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Waktu (WITA)</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nomor Perkara</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Tempat / Ruangan</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($jadwals)): ?>
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400 font-medium">Belum ada jadwal mediasi.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($jadwals as $j): ?>
                <?php
                    $status_sesi = $j->status_sesi ?? 'terjadwal';
                    $is_aktif = !in_array($status_sesi, ['batal', 'dijadwal_ulang']);
                    $is_virtual = !empty($j->link_virtual);
                    // Row highlight
                    $row_class = '';
                    if ($status_sesi === 'batal') $row_class = 'opacity-50';
                    elseif ($status_sesi === 'dijadwal_ulang') $row_class = 'opacity-60 bg-amber-50/60';
                ?>
                <tr class="hover:bg-slate-50/80 transition-colors <?= $row_class ?>">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-semibold text-slate-900"><?= date('d/m/Y', strtotime($j->tgl_mediasi)) ?></td>
                    <td class="px-4 py-3.5 font-mono text-xs text-blue-700 font-bold"><?= substr($j->jam_mulai,0,5) ?> – <?= substr($j->jam_selesai,0,5) ?></td>
                    <td class="px-4 py-3.5 font-mono text-xs text-slate-800 font-semibold"><?= htmlspecialchars($j->nomor_perkara) ?></td>
                    <td class="px-4 py-3.5 text-slate-700">
                        <?php if ($is_virtual): ?>
                            <span class="inline-flex items-center gap-1 text-violet-700 font-bold text-[11px] bg-violet-50 px-2 py-0.5 rounded-full border border-violet-200">
                                🎥 <?= htmlspecialchars($j->platform_virtual ?: 'Virtual') ?>
                            </span>
                            <div class="mt-1">
                                <a href="<?= htmlspecialchars($j->link_virtual) ?>" target="_blank" rel="noopener"
                                    class="text-[11px] text-violet-600 hover:text-violet-800 underline truncate max-w-[160px] block">
                                    Buka Link Meeting →
                                </a>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-700 font-medium"><?= htmlspecialchars($j->nama_ruangan ?: $j->tempat_lain ?: '—') ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php
                        $badge_map = [
                            'terjadwal'      => ['text-blue-800', 'bg-blue-100', 'Terjadwal'],
                            'selesai'        => ['text-emerald-800', 'bg-emerald-100', '✓ Selesai'],
                            'batal'          => ['text-rose-800', 'bg-rose-100', '✕ Dibatalkan'],
                            'dijadwal_ulang' => ['text-amber-800', 'bg-amber-100', '↩ Dijadwal Ulang'],
                        ];
                        $badge = $badge_map[$status_sesi] ?? ['text-slate-800', 'bg-slate-100', ucfirst($status_sesi)];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $badge[0] ?> <?= $badge[1] ?>">
                            <?= $badge[2] ?>
                        </span>
                        <?php if (!empty($j->alasan_reschedule) && !$is_aktif): ?>
                        <div class="text-[10px] text-slate-400 mt-1 italic" title="<?= htmlspecialchars($j->alasan_reschedule) ?>">
                            <?= htmlspecialchars(mb_strimwidth($j->alasan_reschedule, 0, 40, '…')) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <?php if ($status_sesi === 'terjadwal'): ?>
                        <div class="flex items-center justify-end gap-1.5">
                            <!-- Presensi & Selesai -->
                            <a href="<?= site_url("mediator/jadwal/selesai/{$j->id}") ?>"
                                class="inline-flex items-center gap-1 text-xs text-white bg-emerald-600 hover:bg-emerald-700 font-bold px-2.5 py-1 rounded-lg transition-colors shadow-sm"
                                title="Presensi Kehadiran & Selesaikan Sesi">
                                <i class="fa-solid fa-circle-check"></i>
                                <span class="hidden sm:inline">Presensi</span>
                            </a>

                            <!-- Edit Jadwal -->
                            <a href="<?= site_url("mediator/jadwal/edit/{$j->id}") ?>"
                                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-bold px-2 py-1 rounded-lg hover:bg-blue-50 transition-colors border border-blue-200"
                                title="Edit / Perbarui Jadwal">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="hidden sm:inline">Edit</span>
                            </a>

                            <!-- Reschedule -->
                            <a href="<?= site_url("mediator/jadwal/reschedule/{$j->id}") ?>"
                                class="inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-800 font-bold px-2 py-1 rounded-lg hover:bg-amber-50 transition-colors border border-amber-200"
                                title="Jadwal Ulang">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="hidden sm:inline">Reschedule</span>
                            </a>

                            <!-- Batal (modal confirm) -->
                            <button type="button"
                                onclick="openModalBatal(<?= $j->id ?>, '<?= htmlspecialchars($j->nomor_perkara, ENT_QUOTES) ?>')"
                                class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-800 font-bold px-2 py-1 rounded-lg hover:bg-rose-50 transition-colors border border-rose-200"
                                title="Batalkan Sesi">
                                <i class="fa-solid fa-xmark"></i>
                                <span class="hidden sm:inline">Batal</span>
                            </button>
                        </div>
                        <?php else: ?>
                            <a href="<?= site_url("mediator/perkara_saya/detail/{$j->perkara_id}") ?>"
                                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-bold px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors border border-blue-200">
                                <span>Detail Perkara</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination): ?>
    <div class="px-4 py-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-slate-400">Halaman <strong class="text-slate-600"><?= $page ?></strong> &bull; Total <strong class="text-slate-600"><?= number_format($total) ?></strong> data</p>
        <div class="text-xs"><?= $pagination ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi Pembatalan -->
<div id="modal-batal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 animate-in zoom-in-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Batalkan Sesi Mediasi?</h3>
                <p id="modal-batal-perkara" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
        </div>

        <form id="form-batal" method="POST" action="">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alasan Pembatalan</label>
                <textarea name="alasan" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400"
                    placeholder="Masukkan alasan pembatalan sesi ini..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModalBatal()"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">Kembali</button>
                <button type="submit"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalBatal(sesiId, nomorPerkara) {
    document.getElementById('modal-batal-perkara').textContent = 'Perkara No. ' + nomorPerkara;
    document.getElementById('form-batal').action = '<?= site_url("mediator/jadwal/batal/") ?>' + sesiId;
    const modal = document.getElementById('modal-batal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeModalBatal() {
    const modal = document.getElementById('modal-batal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
// Close on backdrop click
document.getElementById('modal-batal').addEventListener('click', function(e) {
    if (e.target === this) closeModalBatal();
});
</script>
