<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
	<div>
		<h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Monitor Perkara Mediasi</h2>
		<p class="text-xs text-slate-500 mt-1">Daftar seluruh perkara mediasi yang Anda inputkan</p>
	</div>
	<div class="flex items-center gap-3">
		<button type="button" onclick="triggerSippApiSync()" id="btn-sync-api"
			class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-indigo-600/20 active:scale-95">
			<i class="fa-solid fa-rotate text-xs animate-spin-reverse" id="icon-sync-api"></i>
			<span id="text-sync-api">Tarik Data SIPP API</span>
		</button>
	</div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
	<form id="form-filter" method="GET" action="<?= site_url('pp/monitor') ?>" class="flex flex-wrap gap-4 items-end justify-between">
		<div class="flex flex-wrap gap-4 items-end w-full sm:w-auto">
			<div class="w-full sm:w-48">
				<label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 whitespace-nowrap">Status Perkara</label>
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
			<a href="<?= site_url('pp/monitor') ?>" class="text-xs text-rose-600 hover:text-rose-800 font-bold py-2 px-3 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-1">
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
					<th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Mediator</th>
					<th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Batas Akhir Mediasi</th>
					<th class="text-left px-4 py-3.5 font-bold uppercase tracking-wider">Status / Hasil</th>
					<th class="text-right px-4 py-3.5 font-bold uppercase tracking-wider">Aksi</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-slate-100">
				<?php if (empty($perkaras)): ?>
					<tr>
						<td colspan="7" class="px-4 py-12 text-center text-slate-400 font-medium">Tidak ada data perkara ditemukan.</td>
					</tr>
				<?php else: ?>
					<?php $no = (isset($page) ? ($page - 1) * 10 : 0) + 1;
					foreach ($perkaras as $p): ?>
						<tr class="hover:bg-slate-50/80 transition-colors">
							<td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
							<td class="px-4 py-3.5 font-bold text-slate-900 font-mono text-xs"><?= htmlspecialchars($p->nomor_perkara) ?></td>
							<td class="px-4 py-3.5 text-slate-700 font-medium"><?= htmlspecialchars($p->jenis_perkara) ?></td>
							<td class="px-4 py-3.5 font-semibold text-slate-800"><?= htmlspecialchars($p->nama_mediator ?: 'Belum ditetapkan') ?></td>
							<td class="px-4 py-3.5">
								<?php
								$diff = ceil((strtotime($p->tgl_batas_mediasi) - time()) / 86400);
								$is_urgent = ($p->status !== 'selesai' && $diff < 7);
								?>
								<span class="font-mono text-xs <?= $is_urgent ? 'text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200' : 'text-slate-600' ?>">
									<?= date('d/m/Y', strtotime($p->tgl_batas_mediasi)) ?>
									<?= $is_urgent ? ' (sisa <7 hr)' : '' ?>
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
								<div class="flex items-center justify-end gap-1.5">
									<?php if ($p->status !== 'selesai' && empty($p->hasil)): ?>
										<a href="<?= site_url("pp/perkara/edit/{$p->id}") ?>"
											class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-2.5 py-1 rounded-lg transition-colors">
											<i class="fa-solid fa-pen-to-square text-[10px]"></i>
											<span>Edit</span>
										</a>
									<?php endif; ?>
									<a href="<?= site_url("pp/monitor/detail/{$p->id}") ?>"
										class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2.5 py-1 rounded-lg transition-colors">
										<span>Detail</span>
										<i class="fa-solid fa-chevron-right text-[10px]"></i>
									</a>
									<?php if ($p->status === 'selesai' && $p->hasil): ?>
										<a href="<?= site_url("pp/monitor/download_laporan/{$p->id}") ?>"
											class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 px-2.5 py-1 rounded-lg transition-colors">
											<i class="fa-solid fa-download text-[10px]"></i>
											<span>Laporan</span>
										</a>
									<?php endif; ?>
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

<!-- Fullscreen Sync Loader Modal PP -->
<div id="pp-sync-loader-modal" style="z-index: 999999 !important;" class="fixed inset-0 bg-slate-900/70 backdrop-blur-md flex items-center justify-center hidden transition-all">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center border border-slate-100 flex flex-col items-center animate-in fade-in zoom-in duration-200">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-4 relative">
            <i class="fa-solid fa-cloud-arrow-down text-2xl text-indigo-600 animate-bounce"></i>
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-600 rounded-full animate-ping"></div>
        </div>
        <h3 class="text-base font-extrabold text-slate-900 font-heading">Menarik Data SIPP API...</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">Sedang mengimpor data perkara baru ke database mediasi_db.</p>
        
        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mb-2">
            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-600 h-full w-full animate-pulse"></div>
        </div>
        <span class="text-[11px] text-indigo-600 font-bold font-mono">Mohon tunggu sebentar...</span>
    </div>
</div>

<script>
function triggerSippApiSync() {
	const btn = document.getElementById('btn-sync-api');
	const icon = document.getElementById('icon-sync-api');
	const text = document.getElementById('text-sync-api');
	const loaderModal = document.getElementById('pp-sync-loader-modal');

	btn.disabled = true;
	btn.classList.add('opacity-75', 'cursor-not-allowed');
	icon.classList.add('fa-spin');
	text.innerText = 'Menarik Data API...';

	if (loaderModal) loaderModal.classList.remove('hidden');

	fetch('<?= site_url("pp/api_sync/run") ?>', {
		method: 'GET',
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		}
	})
	.then(res => res.json())
	.then(data => {
		if (loaderModal) loaderModal.classList.add('hidden');

		btn.disabled = false;
		btn.classList.remove('opacity-75', 'cursor-not-allowed');
		icon.classList.remove('fa-spin');
		text.innerText = 'Tarik Data SIPP API';

		if (data.status === 'success') {
			alert(data.message);
			window.location.reload();
		} else {
			alert('Sinkronisasi Gagal: ' + (data.message || 'Error tidak diketahui'));
		}
	})
	.catch(err => {
		if (loaderModal) loaderModal.classList.add('hidden');

		btn.disabled = false;
		btn.classList.remove('opacity-75', 'cursor-not-allowed');
		icon.classList.remove('fa-spin');
		text.innerText = 'Tarik Data SIPP API';
		alert('Terjadi kesalahan koneksi saat menarik data SIPP API.');
		console.error(err);
	});
}
</script>
