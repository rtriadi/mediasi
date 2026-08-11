<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Laporan Rekapitulasi Mediasi</h2>
        <p class="text-xs text-slate-500 mt-1">Laporan pelaksanaan dan hasil mediasi bulanan sesuai format resmi Pengadilan</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= site_url("admin/laporan/export_excel?bulan={$bulan}&tahun={$tahun}") ?>"
            class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <i class="fa-solid fa-file-excel text-sm"></i>
            <span>Ekspor Excel (.xls)</span>
        </a>
        <a href="<?= site_url("admin/laporan/cetak_pdf?bulan={$bulan}&tahun={$tahun}") ?>" target="_blank"
            class="inline-flex items-center gap-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2.5 rounded-xl transition-all shadow-md shadow-blue-600/20">
            <i class="fa-solid fa-print text-sm"></i>
            <span>Cetak / PDF Laporan</span>
        </a>
    </div>
</div>

<!-- Filter Bulan & Tahun -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form method="GET" action="<?= site_url('admin/laporan') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="w-48">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Bulan</label>
            <select name="bulan" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-600 bg-white font-semibold">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($m == $bulan) ? 'selected' : '' ?>><?= bulan_indo($m) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Tahun</label>
            <select name="tahun" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-600 bg-white font-semibold">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition-colors">
            Tampilkan Laporan
        </button>
    </form>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Perkara</p>
        <p class="text-2xl font-extrabold font-heading text-slate-900 mt-1"><?= $stat['total'] ?></p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">✓ Berhasil</p>
        <p class="text-2xl font-extrabold font-heading text-emerald-600 mt-1"><?= $stat['berhasil'] ?></p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">~ Sebagian</p>
        <p class="text-2xl font-extrabold font-heading text-amber-600 mt-1"><?= $stat['berhasil_sebagian'] ?></p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">✕ Gagal</p>
        <p class="text-2xl font-extrabold font-heading text-rose-600 mt-1"><?= $stat['tidak_berhasil'] ?></p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Dalam Proses</p>
        <p class="text-2xl font-extrabold font-heading text-blue-600 mt-1"><?= $stat['proses'] + $stat['menunggu'] ?></p>
    </div>
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-4 rounded-2xl shadow-md">
        <p class="text-[11px] font-bold text-blue-100 uppercase tracking-wider">% Keberhasilan</p>
        <p class="text-2xl font-extrabold font-heading text-white mt-1"><?= $stat['persen_berhasil'] ?>%</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Daftar Perkara Mediasi Periode <?= bulan_indo($bulan) ?> <?= $tahun ?></h3>
        <span class="text-xs text-slate-500">Total: <?= count($perkaras) ?> Perkara</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-100/80 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider w-8">#</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nomor Perkara</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Jenis Perkara</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Majelis Hakim</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Mediator</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Batas Mediasi</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status / Hasil</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($perkaras)): ?>
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400 font-medium">Tidak ada data perkara pada periode ini.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($perkaras as $p): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-bold text-slate-900 font-mono"><?= htmlspecialchars($p->nomor_perkara) ?></td>
                    <td class="px-4 py-3.5 font-medium text-slate-700"><?= htmlspecialchars($p->jenis_perkara) ?></td>
                    <td class="px-4 py-3.5 text-slate-600"><?= htmlspecialchars($p->majelis_hakim ?? $p->nama_hakim ?? '—') ?></td>
                    <td class="px-4 py-3.5 font-bold text-slate-800"><?= htmlspecialchars($p->nama_mediator ?: '—') ?></td>
                    <td class="px-4 py-3.5 font-mono text-slate-600"><?= date('d/m/Y', strtotime($p->tgl_batas_mediasi)) ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($p->status === 'menunggu'): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">Menunggu</span>
                        <?php elseif ($p->status === 'proses'): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Dalam Proses</span>
                        <?php elseif ($p->status === 'selesai'): ?>
                            <?php if (in_array($p->hasil, ['berhasil', 'berhasil_seluruhnya'])): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">✓ Berhasil Seluruhnya</span>
                            <?php elseif ($p->hasil === 'berhasil_sebagian'): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">~ Berhasil Sebagian</span>
                            <?php elseif ($p->hasil === 'tidak_dapat_dilaksanakan'): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">⊘ Tidak Dapat Dilaksanakan</span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">✕ Tidak Berhasil</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
