<div class="max-w-4xl mx-auto">
    <!-- Stepper -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-3 opacity-60">
            <div class="w-8 h-8 rounded-full bg-green-600 text-white font-semibold text-sm flex items-center justify-center">✓</div>
            <span class="text-sm font-medium text-gray-700">Data Perkara & Para Pihak</span>
        </div>
        <div class="w-16 h-0.5 bg-blue-600 mx-4"></div>
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-semibold text-sm flex items-center justify-center shadow-md">2</div>
            <span class="text-sm font-semibold text-blue-600">Pilih & Penetapan Mediator</span>
        </div>
    </div>

    <!-- Ringkasan Perkara -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm">
        <h3 class="font-semibold text-blue-900 mb-2">Ringkasan Perkara</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-blue-950">
            <div><span class="text-blue-600 text-xs block">Nomor Perkara:</span> <strong><?= htmlspecialchars($perkara['perkara']['nomor_perkara']) ?></strong></div>
            <div><span class="text-blue-600 text-xs block">Majelis Hakim:</span> <?= htmlspecialchars($perkara['perkara']['nama_hakim']) ?></div>
            <div><span class="text-blue-600 text-xs block">Batas Mediasi:</span> <?= date('d/m/Y', strtotime($perkara['perkara']['tgl_batas_mediasi'])) ?></div>
            <div><span class="text-blue-600 text-xs block">Total Pihak:</span> <?= count($perkara['pihak']) ?> orang</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Langkah 2: Pilih Mediator Perkara</h2>
        <p class="text-sm text-gray-500 mb-6">Pilih salah satu mediator aktif yang akan ditugaskan untuk menangani perkara ini.</p>

        <form id="form-assign-mediator" method="POST" action="<?= site_url('pp/perkara/assign_mediator') ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <?php foreach ($mediators as $m): ?>
                <label class="relative flex items-start p-4 bg-white rounded-xl border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition-all group has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:shadow-sm">
                    <input type="radio" name="mediator_id" value="<?= $m->id ?>" required
                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900 text-sm group-hover:text-blue-700"><?= htmlspecialchars($m->nama) ?></span>
                            <?php if ($m->jenis === 'hakim'): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">HAKIM</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">NON-HAKIM</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 font-mono">No. Sertifikat: <?= htmlspecialchars($m->no_sertifikat ?: '—') ?></p>
                        <p class="text-xs font-medium mt-2 <?= $m->jenis === 'hakim' ? 'text-emerald-700' : 'text-blue-700' ?>">
                            <?= $m->jenis === 'hakim' ? '✓ Mediator Hakim (Gratis / Tanpa Biaya)' : 'ℹ Mediator Non-Hakim (Biaya Ditanggung Pihak)' ?>
                        </p>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="<?= site_url('pp/perkara/tambah') ?>" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Step 1</a>
                <button type="submit" id="btn-submit-perkara"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors shadow-md">
                    Simpan & Daftarkan Perkara
                </button>
            </div>
        </form>
    </div>
</div>
