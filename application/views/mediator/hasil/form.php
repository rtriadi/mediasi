<div class="max-w-2xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('mediator/perkara_saya') ?>" class="hover:text-gray-700">Perkara Saya</a>
        <span>›</span>
        <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="hover:text-gray-700"><?= htmlspecialchars($perkara->nomor_perkara) ?></a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1"><?= $title ?></h2>
        <p class="text-sm text-gray-500 mb-6">Laporan akhir hasil pelaksanaan mediasi untuk Perkara No. <strong class="text-gray-800 font-mono"><?= htmlspecialchars($perkara->nomor_perkara) ?></strong></p>

        <form id="form-hasil" method="POST" action="<?= site_url("mediator/hasil/input/{$perkara->id}") ?>" enctype="multipart/form-data">

            <!-- Pilih Hasil — 4 opsi sesuai PERMA No. 1 Tahun 2016 -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-3">Hasil Akhir Mediasi <span class="text-red-500">*</span>
                    <span class="font-normal text-gray-400 ml-1">(sesuai PERMA No. 1/2016)</span>
                </label>

                <div class="space-y-3">
                    <label class="flex items-start p-4 bg-emerald-50/50 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-emerald-500 transition-all has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50" id="label-berhasil-seluruhnya">
                        <input type="radio" name="status_hasil" value="berhasil_seluruhnya" required
                            class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500"
                            onchange="updateConditionalFields(this.value)">
                        <div class="ml-3">
                            <span class="font-bold text-emerald-900 text-sm">✓ Berhasil Seluruhnya</span>
                            <p class="text-xs text-emerald-700 mt-0.5">Para pihak mencapai kesepakatan perdamaian secara menyeluruh atas seluruh objek sengketa.</p>
                        </div>
                    </label>

                    <label class="flex items-start p-4 bg-amber-50/50 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-amber-500 transition-all has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50" id="label-berhasil-sebagian">
                        <input type="radio" name="status_hasil" value="berhasil_sebagian"
                            class="mt-1 w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500"
                            onchange="updateConditionalFields(this.value)">
                        <div class="ml-3">
                            <span class="font-bold text-amber-900 text-sm">~ Berhasil Sebagian</span>
                            <p class="text-xs text-amber-700 mt-0.5">Para pihak mencapai kesepakatan pada sebagian tuntutan/objek sengketa.</p>
                        </div>
                    </label>

                    <label class="flex items-start p-4 bg-red-50/50 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-red-500 transition-all has-[:checked]:border-red-600 has-[:checked]:bg-red-50" id="label-tidak-berhasil">
                        <input type="radio" name="status_hasil" value="tidak_berhasil"
                            class="mt-1 w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                            onchange="updateConditionalFields(this.value)">
                        <div class="ml-3">
                            <span class="font-bold text-red-900 text-sm">✕ Tidak Berhasil</span>
                            <p class="text-xs text-red-700 mt-0.5">Para pihak tidak mencapai kesepakatan setelah pelaksanaan mediasi.</p>
                        </div>
                    </label>

                    <label class="flex items-start p-4 bg-slate-50/50 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-slate-500 transition-all has-[:checked]:border-slate-600 has-[:checked]:bg-slate-50" id="label-tidak-dapat">
                        <input type="radio" name="status_hasil" value="tidak_dapat_dilaksanakan"
                            class="mt-1 w-4 h-4 text-slate-600 border-gray-300 focus:ring-slate-500"
                            onchange="updateConditionalFields(this.value)">
                        <div class="ml-3">
                            <span class="font-bold text-slate-900 text-sm">⊘ Tidak Dapat Dilaksanakan</span>
                            <p class="text-xs text-slate-700 mt-0.5">Mediasi tidak dapat dilaksanakan karena para pihak/salah satu pihak tidak hadir meskipun telah dipanggil secara patut.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Field Kondisional: Ringkasan Kesepakatan (Berhasil / Berhasil Sebagian) -->
            <div id="field-ringkasan" class="mb-5 hidden">
                <label for="ringkasan_kesepakatan" class="block text-sm font-semibold text-gray-900 mb-1.5">
                    Ringkasan Poin Kesepakatan <span class="text-red-500">*</span>
                </label>
                <textarea id="ringkasan_kesepakatan" name="ringkasan_kesepakatan" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Tuliskan poin-poin kesepakatan yang dicapai oleh para pihak..."></textarea>
            </div>

            <!-- Field Kondisional: Alasan Kegagalan (Tidak Berhasil / Tidak Dapat Dilaksanakan) -->
            <div id="field-alasan" class="mb-5 hidden">
                <label for="alasan_kegagalan" class="block text-sm font-semibold text-gray-900 mb-1.5">
                    Alasan / Keterangan Kegagalan
                </label>
                <textarea id="alasan_kegagalan" name="alasan_kegagalan" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Tuliskan alasan mediasi tidak berhasil atau tidak dapat dilaksanakan..."></textarea>
            </div>

            <!-- Upload File Laporan PDF -->
            <div class="mb-6">
                <label for="file_laporan" class="block text-sm font-semibold text-gray-900 mb-1.5">
                    File Laporan Mediator (PDF) <span class="text-red-500">* Wajib</span>
                </label>
                <input type="file" id="file_laporan" name="file_laporan" accept=".pdf" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">Format PDF, Ukuran Maksimal 10MB.</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="<?= site_url("mediator/perkara_saya/detail/{$perkara->id}") ?>" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                <button type="submit" id="btn-simpan-hasil"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Laporan Akhir
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateConditionalFields(val) {
    const isSuccess = ['berhasil_seluruhnya', 'berhasil_sebagian'].includes(val);
    const isFail    = ['tidak_berhasil', 'tidak_dapat_dilaksanakan'].includes(val);

    const fieldRingkasan = document.getElementById('field-ringkasan');
    const fieldAlasan    = document.getElementById('field-alasan');

    fieldRingkasan.classList.toggle('hidden', !isSuccess);
    fieldAlasan.classList.toggle('hidden', !isFail);
}
</script>
