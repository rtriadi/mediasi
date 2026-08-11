<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Kelola Mediator</h2>
        <p class="text-xs text-slate-500 mt-1">Total: <strong><?= $total ?></strong> mediator terdaftar</p>
    </div>
    <a href="<?= site_url('admin/master_mediator/tambah') ?>" id="btn-tambah-mediator"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-blue-600/20">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>Tambah Mediator</span>
    </a>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('admin/master_mediator') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Filter Jenis</label>
                <select name="jenis" id="filter-jenis" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Jenis</option>
                    <option value="hakim" <?= ($filter['jenis'] === 'hakim') ? 'selected' : '' ?>>Hakim</option>
                    <option value="non_hakim" <?= ($filter['jenis'] === 'non_hakim') ? 'selected' : '' ?>>Non-Hakim</option>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Cari Mediator</label>
                <div class="relative">
                    <input type="text" name="search" id="filter-search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Ketik nama mediator...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
        <?php if ($filter['jenis'] || $filter['search']): ?>
        <a href="<?= site_url('admin/master_mediator') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
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
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nama Mediator</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Jenis</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">No. Sertifikat</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">ID Mediator</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Email / HP</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Akun User</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($mediators)): ?>
                <tr><td colspan="9" class="px-4 py-12 text-center text-slate-400 font-medium">Tidak ada data mediator ditemukan.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($mediators as $m): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-semibold text-slate-900"><?= htmlspecialchars($m->nama) ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($m->jenis === 'hakim'): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            HAKIM (Gratis)
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            NON-HAKIM (Berbayar)
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-600 font-mono"><?= htmlspecialchars($m->no_sertifikat ?: '—') ?></td>
                    <td class="px-4 py-3.5 font-mono text-xs">
                        <?php if (!empty($m->id_mediator)): ?>
                            <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold border border-indigo-200 rounded-md">ID: <?= htmlspecialchars($m->id_mediator) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php if (!empty($m->email) || !empty($m->no_hp)): ?>
                        <div class="flex flex-col gap-0.5">
                            <?php if (!empty($m->email)): ?>
                            <span class="flex items-center gap-1 text-slate-600"><i class="fa-solid fa-envelope text-[10px] text-blue-400"></i> <?= htmlspecialchars($m->email) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($m->no_hp)): ?>
                            <span class="flex items-center gap-1 text-slate-600"><i class="fa-brands fa-whatsapp text-[10px] text-emerald-500"></i> <?= htmlspecialchars($m->no_hp) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-slate-400 italic">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-600">
                        <?= $m->username ? '<span class="font-mono text-blue-600">@'.htmlspecialchars($m->username).'</span>' : '<span class="text-slate-400 italic">Tanpa akun</span>' ?>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php if ($m->is_active): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full font-bold border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full font-semibold border border-slate-200">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Nonaktif
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= site_url("admin/master_mediator/edit/{$m->id}") ?>"
                                class="text-xs text-blue-600 hover:text-blue-800 font-bold px-2.5 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-[10px] mr-1"></i>Edit
                            </a>
                            <a href="<?= site_url("admin/master_mediator/toggle_aktif/{$m->id}") ?>"
                                class="text-xs <?= $m->is_active ? 'text-amber-600 hover:text-amber-800 hover:bg-amber-50' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50' ?> font-bold px-2.5 py-1 rounded-lg transition-colors">
                                <?= $m->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </a>
                            <a href="<?= site_url("admin/master_mediator/hapus/{$m->id}") ?>"
                                onclick="return confirm('Yakin hapus mediator <?= htmlspecialchars($m->nama) ?>?')"
                                class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2.5 py-1 rounded-lg hover:bg-rose-50 transition-colors">Hapus</a>
                        </div>
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
