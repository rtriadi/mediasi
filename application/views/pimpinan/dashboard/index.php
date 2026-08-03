<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Dashboard & Laporan Mediasi</h2>
        <p class="text-xs text-slate-500 mt-1">Laporan kinerja dan statistik keberhasilan mediasi Pengadilan Agama Gorontalo</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= site_url('pimpinan/dashboard/export_pdf?' . http_build_query($_GET)) ?>" target="_blank"
            class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-rose-600/20">
            <i class="fa-solid fa-file-pdf text-sm"></i>
            <span>Export PDF</span>
        </a>
        <a href="<?= site_url('pimpinan/dashboard/export_excel?' . http_build_query($_GET)) ?>" target="_blank"
            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-emerald-600/20">
            <i class="fa-solid fa-file-excel text-sm"></i>
            <span>Export Excel (.xls)</span>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('pimpinan/dashboard') ?>" class="flex flex-wrap gap-4 items-center justify-between">
        <div class="flex flex-wrap gap-4 items-center w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mediator</label>
                <select name="mediator_id" id="filter-mediator" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Mediator</option>
                    <?php foreach ($mediators as $m): ?>
                    <option value="<?= $m->id ?>" <?= ($filter['mediator_id'] == $m->id) ? 'selected' : '' ?>><?= htmlspecialchars($m->nama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="w-full sm:w-36">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bulan</label>
                <select name="bulan" id="filter-bulan" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Bulan</option>
                    <?php $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; foreach ($months as $n => $m): ?>
                    <option value="<?= $n ?>" <?= ($filter['bulan'] == $n) ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Triwulan</label>
                <select name="triwulan" id="filter-triwulan" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Triwulan</option>
                    <option value="1" <?= ($filter['triwulan'] == 1) ? 'selected' : '' ?>>Triwulan I (Jan - Mar)</option>
                    <option value="2" <?= ($filter['triwulan'] == 2) ? 'selected' : '' ?>>Triwulan II (Apr - Jun)</option>
                    <option value="3" <?= ($filter['triwulan'] == 3) ? 'selected' : '' ?>>Triwulan III (Jul - Sep)</option>
                    <option value="4" <?= ($filter['triwulan'] == 4) ? 'selected' : '' ?>>Triwulan IV (Okt - Des)</option>
                </select>
            </div>

            <div class="w-full sm:w-28">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                <input type="number" name="tahun" id="filter-tahun" value="<?= htmlspecialchars($filter['tahun'] ?? date('Y')) ?>"
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono">
            </div>
        </div>

        <?php if ($filter['mediator_id'] || $filter['bulan'] || $filter['triwulan']): ?>
        <a href="<?= site_url('pimpinan/dashboard') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200">
            <i class="fa-solid fa-rotate-left mr-1"></i> Reset Filter
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- 4 Executive Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    
    <!-- Total Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-all relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Mediasi</span>
            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <i class="fa-solid fa-folder-closed text-sm"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-900 font-heading"><?= $summary['total'] ?></p>
        <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-slate-700 h-full rounded-full" style="width: 100%"></div>
        </div>
        <p class="text-[11px] text-slate-500 mt-2">Perkara mediasi selesai</p>
    </div>

    <!-- Berhasil Card -->
    <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-sm hover:shadow-md transition-all relative overflow-hidden bg-gradient-to-br from-emerald-50/40 to-white">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Berhasil Sepenuhnya</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-circle-check text-sm"></i>
            </div>
        </div>
        <?php 
        $total_berhasil = $summary['berhasil'] + $summary['berhasil_sebagian'];
        $pct_b = $summary['total'] > 0 ? round(($total_berhasil / $summary['total']) * 100, 1) : 0; 
        ?>
        <p class="text-3xl font-extrabold text-emerald-700 font-heading"><?= $summary['berhasil'] ?></p>
        <div class="w-full bg-emerald-100 h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full" style="width: <?= $pct_b ?>%"></div>
        </div>
        <p class="text-[11px] text-emerald-700 font-bold mt-2"><?= $pct_b ?>% Keberhasilan Mediasi</p>
    </div>

    <!-- Berhasil Sebagian Card -->
    <div class="bg-white rounded-2xl border border-amber-200/80 p-5 shadow-sm hover:shadow-md transition-all relative overflow-hidden bg-gradient-to-br from-amber-50/40 to-white">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Berhasil Sebagian</span>
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20">
                <i class="fa-solid fa-handshake-angle text-sm"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-amber-700 font-heading"><?= $summary['berhasil_sebagian'] ?></p>
        <?php $pct_bs = $summary['total'] > 0 ? round(($summary['berhasil_sebagian'] / $summary['total']) * 100, 1) : 0; ?>
        <div class="w-full bg-amber-100 h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-amber-500 h-full rounded-full" style="width: <?= $pct_bs ?>%"></div>
        </div>
        <p class="text-[11px] text-amber-700 font-bold mt-2"><?= $pct_bs ?>% dari total</p>
    </div>

    <!-- Tidak Berhasil Card -->
    <div class="bg-white rounded-2xl border border-rose-200/80 p-5 shadow-sm hover:shadow-md transition-all relative overflow-hidden bg-gradient-to-br from-rose-50/40 to-white">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Tidak Berhasil</span>
            <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center shadow-md shadow-rose-500/20">
                <i class="fa-solid fa-circle-xmark text-sm"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold text-rose-700 font-heading"><?= $summary['tidak_berhasil'] ?></p>
        <?php $pct_tb = $summary['total'] > 0 ? round(($summary['tidak_berhasil'] / $summary['total']) * 100, 1) : 0; ?>
        <div class="w-full bg-rose-100 h-1.5 rounded-full mt-3 overflow-hidden">
            <div class="bg-rose-500 h-full rounded-full" style="width: <?= $pct_tb ?>%"></div>
        </div>
        <p class="text-[11px] text-rose-700 font-bold mt-2"><?= $pct_tb ?>% dari total</p>
    </div>

</div>

<!-- Visual Charts Section (Chart.js) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Donut Chart Ratio -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-slate-900 text-sm font-heading mb-1 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-blue-600"></i>
                <span>Rasio Hasil Mediasi</span>
            </h3>
            <p class="text-[11px] text-slate-500 mb-4">Persentase perbandingan hasil mediasi perkara</p>
        </div>
        <div class="w-full h-52 flex items-center justify-center">
            <canvas id="chart-hasil-donut"></canvas>
        </div>
    </div>

    <!-- Bar Chart Breakdown -->
    <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-slate-900 text-sm font-heading mb-1 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-indigo-600"></i>
                <span>Statistik Jumlah Perkara Berdasarkan Hasil</span>
            </h3>
            <p class="text-[11px] text-slate-500 mb-4">Jumlah perkara mediasi berdasarkan status kelulusan perdamaian</p>
        </div>
        <div class="w-full h-52">
            <canvas id="chart-hasil-bar"></canvas>
        </div>
    </div>
</div>

<!-- ===== ANALITIK INTERAKTIF — 3 Grafik Baru ===== -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-md">
            <i class="fa-solid fa-chart-line text-white text-sm"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-900 text-sm font-heading">Analitik Interaktif</h3>
            <p class="text-[11px] text-slate-500">Tahun <?= htmlspecialchars($filter['tahun']) ?> — hover untuk melihat detail</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Grafik 1: Tren Bulanan (Line Chart) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-solid fa-chart-line text-blue-600 text-sm"></i>
                <h4 class="font-bold text-slate-900 text-sm">Tren Mediasi Bulanan</h4>
            </div>
            <p class="text-[11px] text-slate-500 mb-4">Jumlah mediasi selesai per bulan sepanjang tahun</p>
            <div class="relative h-56">
                <canvas id="chart-trend-bulanan"></canvas>
            </div>
        </div>

        <!-- Grafik 2: Distribusi Jenis Perkara (Doughnut) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-solid fa-chart-donut text-purple-600 text-sm"></i>
                <h4 class="font-bold text-slate-900 text-sm">Distribusi Jenis Perkara</h4>
            </div>
            <p class="text-[11px] text-slate-500 mb-4">Proporsi jenis perkara yang dimediasi</p>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="chart-distribusi-jenis"></canvas>
            </div>
        </div>

    </div>

    <!-- Grafik 3: Kinerja Mediator (Horizontal Bar) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mt-6">
        <div class="flex items-center gap-2 mb-1">
            <i class="fa-solid fa-ranking-star text-emerald-600 text-sm"></i>
            <h4 class="font-bold text-slate-900 text-sm">Kinerja Mediator — Tingkat Keberhasilan</h4>
        </div>
        <p class="text-[11px] text-slate-500 mb-4">Perbandingan jumlah mediasi berhasil, berhasil sebagian, dan tidak berhasil per mediator</p>

        <?php if (empty($kinerja_mediator)): ?>
        <div class="text-center py-8 text-slate-400 text-sm">Belum ada data kinerja untuk tahun ini.</div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($kinerja_mediator as $km): ?>
            <?php
                $km_total = (int)$km->total;
                $pct_b    = $km_total > 0 ? round(($km->berhasil / $km_total) * 100) : 0;
                $pct_bs   = $km_total > 0 ? round(($km->berhasil_sebagian / $km_total) * 100) : 0;
                $pct_tb   = $km_total > 0 ? round(($km->tidak_berhasil / $km_total) * 100) : 0;
                $pct_tot  = $km_total > 0 ? round((($km->berhasil + $km->berhasil_sebagian) / $km_total) * 100) : 0;
            ?>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-semibold text-slate-700"><?= htmlspecialchars($km->nama) ?></span>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <span class="text-emerald-600 font-bold" title="Berhasil Sepenuhnya"><?= $km->berhasil ?>✓</span>
                        <span class="text-amber-500 font-bold" title="Berhasil Sebagian"><?= $km->berhasil_sebagian ?>~</span>
                        <span class="text-rose-500 font-bold" title="Tidak Berhasil"><?= $km->tidak_berhasil ?>✕</span>
                        <span class="text-slate-400">| <?= $km_total ?> total</span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200" title="Total Tingkat Keberhasilan Mediasi"><?= $pct_tot ?>%</span>
                    </div>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-emerald-500 transition-all" style="width:<?= $pct_b ?>%" title="Berhasil: <?= $km->berhasil ?>"></div>
                    <div class="h-full bg-amber-400 transition-all" style="width:<?= $pct_bs ?>%" title="Berhasil Sebagian: <?= $km->berhasil_sebagian ?>"></div>
                    <div class="h-full bg-rose-400 transition-all" style="width:<?= $pct_tb ?>%" title="Tidak Berhasil: <?= $km->tidak_berhasil ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Legenda -->
        <div class="flex items-center gap-4 mt-4 text-[11px] text-slate-500">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Berhasil</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Berhasil Sebagian</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span> Tidak Berhasil</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Encode data untuk Chart.js
$trend_labels      = json_encode(array_column($trend_bulanan, 'label'));
$trend_berhasil    = json_encode(array_column($trend_bulanan, 'berhasil'));
$trend_sebagian    = json_encode(array_column($trend_bulanan, 'berhasil_sebagian'));
$trend_gagal       = json_encode(array_column($trend_bulanan, 'tidak_berhasil'));

$jenis_labels      = json_encode(array_column((array)$distribusi_jenis, 'jenis_perkara'));
$jenis_data        = json_encode(array_column((array)$distribusi_jenis, 'total'));
?>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
(function() {
    const fontFamily = "'Inter', 'Segoe UI', sans-serif";
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: { family: fontFamily, size: 11 }, boxWidth: 12 } } },
    };

    // ---- Chart 1: Tren Bulanan (Line) ----
    const trendCtx = document.getElementById('chart-trend-bulanan');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: <?= $trend_labels ?>,
                datasets: [
                    {
                        label: 'Berhasil',
                        data: <?= $trend_berhasil ?>,
                        backgroundColor: 'rgba(16,185,129,0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Berhasil Sebagian',
                        data: <?= $trend_sebagian ?>,
                        backgroundColor: 'rgba(245,158,11,0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Tidak Berhasil',
                        data: <?= $trend_gagal ?>,
                        backgroundColor: 'rgba(239,68,68,0.7)',
                        borderRadius: 4,
                    },
                ]
            },
            options: {
                ...defaultOptions,
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { stacked: true, beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } }
                },
                plugins: { ...defaultOptions.plugins, tooltip: { mode: 'index', intersect: false } }
            }
        });
    }

    // ---- Chart 2: Distribusi Jenis Perkara (Doughnut) ----
    const jenisPalette = [
        '#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#14b8a6','#ec4899','#84cc16'
    ];
    const jenisCtx = document.getElementById('chart-distribusi-jenis');
    if (jenisCtx) {
        const jLabels = <?= $jenis_labels ?>;
        const jData   = <?= $jenis_data ?>;
        const colors  = jLabels.map((_, i) => jenisPalette[i % jenisPalette.length]);
        new Chart(jenisCtx, {
            type: 'doughnut',
            data: {
                labels: jLabels,
                datasets: [{ data: jData, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: fontFamily, size: 10 }, boxWidth: 10, padding: 8 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
            }
        });
    }
})();
</script>

<!-- Table Detail Card -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="font-bold text-slate-900 text-sm font-heading flex items-center gap-2">
            <i class="fa-solid fa-table-list text-blue-600"></i>
            <span>Rincian Laporan Hasil Mediasi Perkara</span>
        </h3>
        <span class="text-xs text-slate-500">Total Data: <strong><?= $total ?></strong></span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-100/80 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider w-8">#</th>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Nomor Perkara</th>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Jenis Perkara</th>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Mediator</th>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Hasil Mediasi</th>
                    <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Tgl Selesai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($detail)): ?>
                <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400 font-medium">Belum ada data laporan mediasi.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($detail as $d): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-bold text-slate-900 font-mono"><?= htmlspecialchars($d->nomor_perkara) ?></td>
                    <td class="px-4 py-3.5 text-slate-700 font-medium"><?= htmlspecialchars($d->jenis_perkara) ?></td>
                    <td class="px-4 py-3.5 font-semibold text-slate-800"><?= htmlspecialchars($d->mediator) ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($d->hasil === 'berhasil'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i class="fa-solid fa-check text-[10px]"></i> Berhasil
                        </span>
                        <?php elseif ($d->hasil === 'berhasil_sebagian'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <i class="fa-solid fa-handshake-angle text-[10px]"></i> Berhasil Sebagian
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            <i class="fa-solid fa-xmark text-[10px]"></i> Tidak Berhasil
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-600 font-mono"><?= date('d/m/Y', strtotime($d->tgl_hasil)) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination): ?>
    <div class="px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
        <?= $pagination ?>
    </div>
    <?php endif; ?>
</div>

<!-- Executive Summary Donut & Bar Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const total     = <?= (int)$summary['total'] ?>;
    const berhasil  = <?= (int)$summary['berhasil'] ?>;
    const sebagian  = <?= (int)$summary['berhasil_sebagian'] ?>;
    const gagal     = <?= (int)$summary['tidak_berhasil'] ?>;

    // Donut Chart
    const ctxDonut = document.getElementById('chart-hasil-donut').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Berhasil Sepenuhnya', 'Berhasil Sebagian', 'Tidak Berhasil'],
            datasets: [{
                data: [berhasil, sebagian, gagal],
                backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
            },
            cutout: '70%'
        }
    });

    // Bar Chart
    const ctxBar = document.getElementById('chart-hasil-bar').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Berhasil Sepenuhnya', 'Berhasil Sebagian', 'Tidak Berhasil'],
            datasets: [{
                label: 'Jumlah Perkara',
                data: [berhasil, sebagian, gagal],
                backgroundColor: ['rgba(16, 185, 129, 0.85)', 'rgba(245, 158, 11, 0.85)', 'rgba(244, 63, 94, 0.85)'],
                borderRadius: 8,
                barThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                x: { ticks: { font: { size: 11, weight: 'bold' } } }
            }
        }
    });
});
</script>
