<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Kelola Ruangan Mediasi</h2>
        <p class="text-xs text-slate-500 mt-1">Total: <strong><?= $total ?></strong> lokasi & ruangan mediasi</p>
    </div>
    <a href="<?= site_url('admin/master_ruangan/tambah') ?>" id="btn-tambah-ruangan"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-blue-600/20">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Tambah Ruangan</span>
    </a>
</div>

<!-- Filter & Search Bar -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('admin/master_ruangan') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Filter Status</label>
                <select name="status" id="filter-status" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="aktif" <?= (isset($filter['status']) && $filter['status'] === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= (isset($filter['status']) && $filter['status'] === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Cari Ruangan</label>
                <div class="relative">
                    <input type="text" name="search" id="filter-search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Ketik nama atau lokasi ruangan...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
        <?php if (!empty($filter['status']) || !empty($filter['search'])): ?>
        <a href="<?= site_url('admin/master_ruangan') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
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
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nama Ruangan</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Keterangan / Lokasi</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($ruangans)): ?>
                <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400 font-medium">Belum ada data ruangan.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($ruangans as $r): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-semibold text-slate-900"><?= htmlspecialchars($r->nama_ruangan) ?></td>
                    <td class="px-4 py-3.5 text-slate-600"><?= htmlspecialchars($r->keterangan ?: '—') ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($r->is_active): ?>
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
                            <a href="<?= site_url("admin/master_ruangan/edit/{$r->id}") ?>"
                                class="text-xs text-blue-600 hover:text-blue-800 font-bold px-2.5 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-[10px] mr-1"></i>Edit
                            </a>
                            <a href="<?= site_url("admin/master_ruangan/toggle_aktif/{$r->id}") ?>"
                                class="text-xs <?= $r->is_active ? 'text-amber-600 hover:text-amber-800 hover:bg-amber-50' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50' ?> font-bold px-2.5 py-1 rounded-lg transition-colors">
                                <?= $r->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </a>
                            <a href="<?= site_url("admin/master_ruangan/hapus/{$r->id}") ?>"
                                onclick="return confirm('Yakin hapus ruangan <?= htmlspecialchars($r->nama_ruangan) ?>?')"
                                class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2.5 py-1 rounded-lg hover:bg-rose-50 transition-colors">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination) && $pagination): ?>
    <div class="px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
        <?= $pagination ?>
    </div>
    <?php endif; ?>
</div>
