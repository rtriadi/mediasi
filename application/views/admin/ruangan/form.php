<div class="max-w-xl">
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('admin/master_ruangan') ?>" class="hover:text-gray-700">Kelola Ruangan</a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6"><?= $title ?></h2>

        <form id="form-ruangan" method="POST" action="<?= $ruangan_data ? site_url("admin/master_ruangan/edit/{$ruangan_data->id}") : site_url('admin/master_ruangan/tambah') ?>">
            <div class="mb-5">
                <label for="nama_ruangan" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Ruangan <span class="text-red-500">*</span></label>
                <input type="text" id="nama_ruangan" name="nama_ruangan" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    value="<?= htmlspecialchars($ruangan_data->nama_ruangan ?? set_value('nama_ruangan')) ?>"
                    placeholder="Contoh: Ruang Mediasi 1">
            </div>

            <div class="mb-5">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Lokasi</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Contoh: Lantai 1 Gedung Utama PA Gorontalo"><?= htmlspecialchars($ruangan_data->keterangan ?? set_value('keterangan')) ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                        <?= (!isset($ruangan_data) || $ruangan_data->is_active) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Ruangan Aktif</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" id="btn-simpan"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    <?= $ruangan_data ? 'Simpan Perubahan' : 'Tambah Ruangan' ?>
                </button>
                <a href="<?= site_url('admin/master_ruangan') ?>" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
