<?php
// Badge helper
function role_badge($role) {
    $map = [
        'admin'    => ['bg-rose-100 text-rose-800 border-rose-200',    'Admin'],
        'pp'       => ['bg-amber-100 text-amber-800 border-amber-200', 'Panitera Pengganti'],
        'hakim'    => ['bg-purple-100 text-purple-800 border-purple-200', 'Hakim'],
        'mediator' => ['bg-blue-100 text-blue-800 border-blue-200',  'Mediator'],
        'pimpinan' => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'Pimpinan'],
    ];
    $d = $map[$role] ?? ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($role)];
    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {$d[0]}\">{$d[1]}</span>";
}
?>

<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Kelola User</h2>
        <p class="text-xs text-slate-500 mt-1">Total: <strong><?= $total ?></strong> user terdaftar dalam sistem</p>
    </div>
    <a href="<?= site_url('admin/master_user/tambah') ?>" id="btn-tambah-user"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-blue-600/20">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>Tambah User Baru</span>
    </a>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form id="form-filter" method="GET" action="<?= site_url('admin/master_user') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Filter Role</label>
                <select name="role" id="filter-role" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Role</option>
                    <?php foreach (['pp','hakim','mediator','pimpinan'] as $r): ?>
                    <option value="<?= $r ?>" <?= ($filter['role'] === $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Cari User</label>
                <div class="relative">
                    <input type="text" name="search" id="filter-search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Ketik nama atau username...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
        <?php if ($filter['role'] || $filter['search']): ?>
        <a href="<?= site_url('admin/master_user') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
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
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Nama Lengkap</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Username</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Role</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Dibuat</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($users)): ?>
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400 font-medium">Tidak ada data user ditemukan.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*10 : 0) + 1; foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-semibold text-slate-900"><?= htmlspecialchars($u->nama) ?></td>
                    <td class="px-4 py-3.5 text-slate-600 font-mono text-xs">@<?= htmlspecialchars($u->username) ?></td>
                    <td class="px-4 py-3.5"><?= role_badge($u->role) ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($u->is_active): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full font-bold border border-emerald-200">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full font-semibold border border-slate-200">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Nonaktif
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500 font-mono"><?= date('d/m/Y', strtotime($u->created_at)) ?></td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="#"
                                onclick="return showConfirmModal('<?= site_url("admin/master_user/reset_password/{$u->id}") ?>', 'Reset Password User', 'Yakin reset password user <?= htmlspecialchars($u->username) ?> ke default (123456)?', 'Ya, Reset Password')"
                                class="text-xs text-amber-600 hover:text-amber-800 font-bold px-2.5 py-1 rounded-lg hover:bg-amber-50 transition-colors"
                                title="Reset Password ke default: 123456">
                                <i class="fa-solid fa-key text-[10px] mr-1"></i>Reset Pass
                            </a>
                            <a href="<?= site_url("admin/master_user/edit/{$u->id}") ?>"
                                class="text-xs text-blue-600 hover:text-blue-800 font-bold px-2.5 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-[10px] mr-1"></i>Edit
                            </a>
                            <a href="<?= site_url("admin/master_user/toggle_aktif/{$u->id}") ?>"
                                class="text-xs <?= $u->is_active ? 'text-amber-600 hover:text-amber-800 hover:bg-amber-50' : 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50' ?> font-bold px-2.5 py-1 rounded-lg transition-colors">
                                <?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </a>
                            <a href="#"
                                onclick="return showConfirmModal('<?= site_url("admin/master_user/hapus/{$u->id}") ?>', 'Hapus User', 'Yakin hapus user <?= htmlspecialchars($u->nama) ?>? Data tidak dapat dikembalikan.')"
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
    <div class="px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
        <?= $pagination ?>
    </div>
    <?php endif; ?>
</div>
