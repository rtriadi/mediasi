<div class="max-w-xl">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?= site_url('admin/master_user') ?>" class="hover:text-gray-700">Kelola User</a>
        <span>›</span>
        <span class="text-gray-900 font-medium"><?= $title ?></span>
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6"><?= $title ?></h2>

        <form id="form-user" method="POST" action="<?= $user_data ? site_url("admin/master_user/edit/{$user_data->id}") : site_url('admin/master_user/tambah') ?>">

            <!-- Nama -->
            <div class="mb-5">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="nama" name="nama" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    value="<?= htmlspecialchars($user_data->nama ?? set_value('nama')) ?>"
                    placeholder="Contoh: Ahmad Fauzi">
            </div>

            <!-- Username -->
            <div class="mb-5">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                    value="<?= htmlspecialchars($user_data->username ?? set_value('username')) ?>"
                    placeholder="Hanya huruf, angka, garis bawah">
                <p class="text-xs text-gray-400 mt-1">Hanya boleh huruf, angka, dan underscore (_)</p>
            </div>

            <!-- ID SIPP & NIP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="id_sipp" class="block text-sm font-medium text-gray-700 mb-1.5">ID SIPP</label>
                    <input type="text" id="id_sipp" name="id_sipp"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-gray-900"
                        value="<?= htmlspecialchars($user_data->id_sipp ?? set_value('id_sipp')) ?>"
                        placeholder="Contoh: 38">
                    <p class="text-xs text-gray-400 mt-1">ID Pegawai pada Database SIPP</p>
                </div>
                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1.5">NIP</label>
                    <input type="text" id="nip" name="nip"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-gray-900"
                        value="<?= htmlspecialchars($user_data->nip ?? set_value('nip')) ?>"
                        placeholder="Contoh: 196610121992032002">
                    <p class="text-xs text-gray-400 mt-1">Nomor Induk Pegawai</p>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password <?= $user_data ? '' : '<span class="text-red-500">*</span>' ?>
                </label>
                <input type="password" id="password" name="password" <?= $user_data ? '' : 'required' ?> minlength="6"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="<?= $user_data ? 'Kosongkan jika tidak ingin mengubah' : 'Min. 6 karakter' ?>">
                <?php if ($user_data): ?>
                <p class="text-xs text-gray-400 mt-1">Biarkan kosong untuk tidak mengubah password</p>
                <?php endif; ?>
            </div>

            <!-- Role (Mendukung Multi-Role / Pilih 2 Role) -->
            <div class="mb-5 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <label class="block text-sm font-semibold text-slate-800 mb-1">
                    Role Akses User <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-3">Pilih role akses akun ini. Anda dapat memilih lebih dari 1 role (misal: Hakim sekaligus Mediator).</p>

                <?php
                $user_roles = [];
                if (isset($user_data) && $user_data->role) {
                    $user_roles = explode(',', $user_data->role);
                } elseif (set_value('roles')) {
                    $user_roles = (array)set_value('roles');
                }
                $roles_def = [
                    'pp'       => ['Panitera Pengganti', 'fa-file-circle-plus', 'bg-blue-100 text-blue-800'],
                    'hakim'    => ['Hakim', 'fa-gavel', 'bg-indigo-100 text-indigo-800'],
                    'mediator' => ['Mediator Mediasi', 'fa-user-tie', 'bg-emerald-100 text-emerald-800'],
                    'pimpinan' => ['Pimpinan PA', 'fa-chart-pie', 'bg-amber-100 text-amber-800'],
                ];
                ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <?php foreach ($roles_def as $r_key => $r_val): ?>
                    <?php $checked = in_array($r_key, $user_roles) ? 'checked' : ''; ?>
                    <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-blue-400 transition-colors shadow-sm">
                        <input type="checkbox" name="roles[]" value="<?= $r_key ?>" <?= $checked ?>
                            class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-800"><?= $r_val[0] ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                        <?= (!isset($user_data) || $user_data->is_active) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Akun Aktif</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" id="btn-simpan"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    <?= $user_data ? 'Simpan Perubahan' : 'Tambah User' ?>
                </button>
                <a href="<?= site_url('admin/master_user') ?>" class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
