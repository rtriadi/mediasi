<div class="max-w-2xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('mediator/perkara_saya') ?>" class="hover:text-gray-700">Perkara Saya</a>
        <span>›</span>
        <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="hover:text-gray-700"><?= htmlspecialchars($perkara->nomor_perkara) ?></a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <!-- Info Sesi Lama -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-amber-800">Menjadwalkan Ulang Sesi Mediasi</p>
                <p class="text-xs text-amber-700 mt-1">
                    Jadwal saat ini: <strong><?= tgl_indo($sesi->tgl_mediasi, true) ?></strong>
                    pukul <strong><?= substr($sesi->jam_mulai,0,5) ?> – <?= substr($sesi->jam_selesai,0,5) ?> WITA</strong>
                    <?= $sesi->nama_ruangan ? ' di <strong>' . htmlspecialchars($sesi->nama_ruangan) . '</strong>' : '' ?>
                </p>
                <p class="text-xs text-amber-600 mt-1">Sesi lama akan otomatis ditandai "Dijadwal Ulang" setelah menyimpan.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1"><?= $title ?></h2>
        <p class="text-sm text-gray-500 mb-6">Perkara No. <strong class="font-mono text-gray-800"><?= htmlspecialchars($perkara->nomor_perkara) ?></strong></p>

        <form id="form-reschedule" method="POST" action="<?= site_url("mediator/jadwal/reschedule/{$sesi->id}") ?>">

            <!-- Alasan Reschedule -->
            <div class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                <label for="alasan" class="block text-sm font-semibold text-rose-800 mb-2">
                    <i class="fa-solid fa-comment-dots mr-1"></i> Alasan Penjadwalan Ulang <span class="text-red-600">*</span>
                </label>
                <textarea id="alasan" name="alasan" rows="2" required minlength="5"
                    class="w-full border border-rose-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white"
                    placeholder="Contoh: Pihak Tergugat tidak hadir / Mediator berhalangan / Permintaan Para Pihak"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label for="tgl_mediasi_baru" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Baru <span class="text-red-500">*</span></label>
                    <input type="date" id="tgl_mediasi_baru" name="tgl_mediasi_baru" required
                        max="<?= $perkara->tgl_batas_mediasi ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label for="jam_mulai_baru" class="block text-sm font-medium text-gray-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_mulai_baru" name="jam_mulai_baru" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= substr($sesi->jam_mulai,0,5) ?>">
                </div>
                <div>
                    <label for="jam_selesai_baru" class="block text-sm font-medium text-gray-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_selesai_baru" name="jam_selesai_baru" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        value="<?= substr($sesi->jam_selesai,0,5) ?>">
                </div>
            </div>

            <!-- Ruangan Baru -->
            <div class="mb-5">
                <label for="ruangan_id_baru" class="block text-sm font-medium text-gray-700 mb-1.5">Ruangan Baru (opsional)</label>
                <select id="ruangan_id_baru" name="ruangan_id_baru"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">— Sama seperti sebelumnya / Tanpa ruangan —</option>
                    <?php foreach ($ruangans as $r): ?>
                    <option value="<?= $r->id ?>" <?= ($r->id == $sesi->ruangan_id) ? 'selected' : '' ?>><?= htmlspecialchars($r->nama_ruangan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Link Virtual (opsional) -->
            <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Platform Virtual (jika online)</label>
                    <select name="platform_virtual_baru" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                        <option value="">— Tidak ada —</option>
                        <option value="Zoom">Zoom Meeting</option>
                        <option value="Google Meet">Google Meet</option>
                        <option value="MS Teams">Microsoft Teams</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Link Virtual Meeting (jika online)</label>
                    <input type="url" name="link_virtual_baru"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
                        placeholder="https://zoom.us/j/...">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                <button type="submit" id="btn-simpan-reschedule"
                    class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days"></i>
                    Simpan Jadwal Baru & Kirim Notifikasi
                </button>
            </div>
        </form>
    </div>
</div>
