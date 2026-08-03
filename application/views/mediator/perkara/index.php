<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Perkara Mediasi Saya</h2>
        <p class="text-xs text-slate-500 mt-1">Daftar perkara yang ditugaskan kepada Anda sebagai Mediator</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('mediator/perkara_saya') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Filter Status</label>
                <select name="status" id="filter-status" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu" <?= ($filter['status'] === 'menunggu') ? 'selected' : '' ?>>Menunggu</option>
                    <option value="proses" <?= ($filter['status'] === 'proses') ? 'selected' : '' ?>>Dalam Proses</option>
                    <option value="selesai" <?= ($filter['status'] === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Cari Perkara</label>
                <div class="relative">
                    <input type="text" name="search" id="filter-search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Ketik nomor perkara...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
        <?php if ($filter['status'] || $filter['search']): ?>
        <a href="<?= site_url('mediator/perkara_saya') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
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
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nomor Perkara</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Jenis Perkara</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Batas Mediasi</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status / Hasil</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($perkaras)): ?>
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400 font-medium">Tidak ada perkara ditugaskan.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($perkaras as $p): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-bold text-slate-900 font-mono"><?= htmlspecialchars($p->nomor_perkara) ?></td>
                    <td class="px-4 py-3.5 text-slate-700 font-medium"><?= htmlspecialchars($p->jenis_perkara) ?></td>
                    <td class="px-4 py-3.5 font-mono text-xs">
                        <?php
                        $sisa_hari = ceil((strtotime($p->tgl_batas_mediasi) - time()) / 86400);
                        $is_urgent = ($p->status !== 'selesai' && $sisa_hari < 7);
                        ?>
                        <span class="<?= $is_urgent ? 'text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200' : 'text-slate-600' ?>">
                            <?= date('d/m/Y', strtotime($p->tgl_batas_mediasi)) ?>
                            <?= $is_urgent ? " (Sisa {$sisa_hari} hr)" : '' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php if ($p->status === 'menunggu'): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">Menunggu</span>
                        <?php elseif ($p->status === 'proses'): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Dalam Proses</span>
                        <?php elseif ($p->status === 'selesai'): ?>
                            <?php if ($p->hasil === 'berhasil'): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">✓ Berhasil</span>
                            <?php elseif ($p->hasil === 'berhasil_sebagian'): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">~ Berhasil Sebagian</span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">✕ Tidak Berhasil</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <a href="<?= site_url("mediator/perkara_saya/detail/{$p->id}") ?>"
                            class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-bold px-2.5 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                            <span>Kelola Perkara</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </td>
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
