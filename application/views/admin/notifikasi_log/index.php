<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Riwayat Log Notifikasi</h2>
        <p class="text-xs text-slate-500 mt-1">Status pengiriman notifikasi otomatis WhatsApp & Email beserta fitur Kirim Ulang</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
    <form method="GET" action="<?= site_url('admin/notifikasi_log') ?>" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-wrap gap-3 items-end w-full sm:w-auto">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Status</option>
                    <option value="terkirim" <?= ($filter['status'] === 'terkirim') ? 'selected' : '' ?>>Terkirim</option>
                    <option value="gagal" <?= ($filter['status'] === 'gagal') ? 'selected' : '' ?>>Gagal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Notifikasi</label>
                <select name="jenis" class="border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 bg-white">
                    <option value="">Semua Jenis</option>
                    <option value="email" <?= ($filter['jenis'] === 'email') ? 'selected' : '' ?>>Email</option>
                    <option value="wa" <?= ($filter['jenis'] === 'wa') ? 'selected' : '' ?>>WhatsApp</option>
                </select>
            </div>
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Penerima / Perkara</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                    placeholder="Penerima / No Perkara..."
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-600">
            </div>
            <button type="submit" class="bg-blue-600 text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-blue-700 transition-colors">Filter</button>
        </div>
        <?php if (!empty($filter['status']) || !empty($filter['jenis']) || !empty($filter['search'])): ?>
        <a href="<?= site_url('admin/notifikasi_log') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200">
            ✕ Reset Filter
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-100/80 border-b border-slate-200 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider w-8">#</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Waktu WAP</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Jenis</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Penerima</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Perkara / Subjek</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Keterangan Error</th>
                    <th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($logs)): ?>
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400 font-medium">Belum ada catatan log notifikasi.</td></tr>
                <?php else: ?>
                <?php $no = (isset($page) ? ($page-1)*15 : 0) + 1; foreach ($logs as $l): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-4 py-3.5 font-mono text-slate-500 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($l->created_at)) ?></td>
                    <td class="px-4 py-3.5 font-bold">
                        <?php if ($l->jenis === 'email'): ?>
                        <span class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">
                            <i class="fa-solid fa-envelope text-[10px]"></i> Email
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            <i class="fa-brands fa-whatsapp text-[10px]"></i> WA
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 font-bold font-mono text-slate-900"><?= htmlspecialchars($l->penerima) ?></td>
                    <td class="px-4 py-3.5">
                        <p class="font-bold text-slate-800 font-mono"><?= htmlspecialchars($l->nomor_perkara ?: '—') ?></p>
                        <p class="text-[11px] text-slate-500 truncate max-w-xs"><?= htmlspecialchars($l->subjek ?: '-') ?></p>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php if ($l->status === 'terkirim'): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            ✓ Terkirim
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                            ✕ Gagal
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($l->error_message ?? '') ?>">
                        <?= htmlspecialchars($l->error_message ?: '—') ?>
                    </td>
                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                        <a href="<?= site_url("admin/notifikasi_log/kirim_ulang/{$l->id}") ?>"
                            onclick="return confirm('Coba kirim ulang notifikasi ke <?= htmlspecialchars($l->penerima) ?>?')"
                            class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2.5 py-1 rounded-lg transition-colors border border-blue-200">
                            <i class="fa-solid fa-rotate text-[10px]"></i>
                            <span>Kirim Ulang</span>
                        </a>
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
