<div class="max-w-xl">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('admin/master_mediator') ?>" class="hover:text-gray-700">Kelola Mediator</a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6"><?= $title ?></h2>

        <form id="form-mediator" method="POST" action="<?= $mediator_data ? site_url("admin/master_mediator/edit/{$mediator_data->id}") : site_url('admin/master_mediator/tambah') ?>">

            <!-- Nama -->
            <div class="mb-5">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap Mediator <span class="text-red-500">*</span></label>
                <input type="text" id="nama" name="nama" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    value="<?= htmlspecialchars($mediator_data->nama ?? set_value('nama')) ?>"
                    placeholder="Contoh: Dr. H. Ahmad Dahlan, S.H., M.H.">
            </div>

            <!-- Jenis -->
            <div class="mb-5">
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Mediator <span class="text-red-500">*</span></label>
                <select id="jenis" name="jenis" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">— Pilih Jenis Mediator —</option>
                    <option value="hakim" <?= (isset($mediator_data) && $mediator_data->jenis === 'hakim') ? 'selected' : '' ?>>Hakim (Gratis / Tidak Dipungut Biaya)</option>
                    <option value="non_hakim" <?= (isset($mediator_data) && $mediator_data->jenis === 'non_hakim') ? 'selected' : '' ?>>Non-Hakim (Berbayar Sesuai Ketentuan)</option>
                </select>
            </div>

            <!-- No. Sertifikat & ID SIPP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="no_sertifikat" class="block text-sm font-medium text-gray-700 mb-1.5">No. Sertifikat Mediator</label>
                    <input type="text" id="no_sertifikat" name="no_sertifikat"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-gray-900"
                        value="<?= htmlspecialchars($mediator_data->no_sertifikat ?? set_value('no_sertifikat')) ?>"
                        placeholder="Contoh: 123/SERT-MED/MA/2022">
                </div>
                <div>
                    <label for="id_sipp" class="block text-sm font-medium text-gray-700 mb-1.5">ID Mediator (SIPP)</label>
                    <input type="text" id="id_sipp" name="id_sipp"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-gray-900"
                        value="<?= htmlspecialchars($mediator_data->id_sipp ?? set_value('id_sipp')) ?>"
                        placeholder="Contoh: 44">
                    <p class="text-xs text-gray-400 mt-1">ID Mediator pada API SIPP</p>
                </div>
            </div>

            <!-- Kontak: Email & No HP -->
            <div class="mb-5 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-3">
                    <i class="fa-solid fa-bell mr-1"></i> Kontak untuk Notifikasi Penugasan
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?= htmlspecialchars($mediator_data->email ?? set_value('email')) ?>"
                            placeholder="mediator@contoh.com">
                    </div>
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" id="no_hp" name="no_hp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?= htmlspecialchars($mediator_data->no_hp ?? set_value('no_hp')) ?>"
                            placeholder="Contoh: 0812xxxxxxxx">
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-2">Digunakan untuk notifikasi otomatis saat mediator mendapat penugasan perkara.</p>
            </div>

            <!-- Link Akun User -->
            <div class="mb-5">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">Hubungkan ke Akun User</label>
                <select id="user_id" name="user_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">— Tidak dihubungkan (Tanpa Akses Login) —</option>
                    <?php foreach ($available_users as $u): ?>
                    <option value="<?= $u->id ?>" <?= (isset($mediator_data) && $mediator_data->user_id == $u->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u->nama) ?> (@<?= htmlspecialchars($u->username) ?> - <?= strtoupper($u->role) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-400 mt-1">Pilih akun user jika mediator ini ingin bisa login untuk kelola perkara & jadwal</p>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                        <?= (!isset($mediator_data) || $mediator_data->is_active) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Mediator Aktif</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" id="btn-simpan"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    <?= $mediator_data ? 'Simpan Perubahan' : 'Tambah Mediator' ?>
                </button>
                <a href="<?= site_url('admin/master_mediator') ?>" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
