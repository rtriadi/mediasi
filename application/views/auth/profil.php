<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Profil Saya</h2>
        <p class="text-xs text-slate-500 mt-1">Kelola data informasi akun dan ubah kata sandi (password)</p>
    </div>
</div>

<div class="max-w-2xl">
    <form action="<?= site_url('auth/profil') ?>" method="POST" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 md:p-8 space-y-6">
        
        <div class="border-b border-slate-100 pb-4 flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white text-xl font-bold font-heading shadow-md">
                <?= strtoupper(substr($user_data->nama, 0, 1)) ?>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 font-heading"><?= htmlspecialchars($user_data->nama) ?></h3>
                <span class="inline-block bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider mt-0.5">
                    Role: <?= htmlspecialchars($user_data->role) ?>
                </span>
            </div>
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="nama" name="nama" required
                value="<?= htmlspecialchars($user_data->nama) ?>"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 font-semibold text-slate-900">
        </div>

        <!-- Username (Readonly) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                Username (Id Login)
            </label>
            <input type="text" readonly
                value="<?= htmlspecialchars($user_data->username) ?>"
                class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm font-mono text-slate-600 cursor-not-allowed">
            <p class="text-[11px] text-slate-400 mt-1">Username digunakan untuk login dan tidak dapat diubah sendiri.</p>
        </div>

        <!-- Section Ganti Password -->
        <div class="border-t border-slate-100 pt-6">
            <h4 class="text-sm font-bold text-slate-900 font-heading mb-1 flex items-center gap-2">
                <i class="fa-solid fa-key text-blue-600"></i>
                <span>Ubah Password (Opsional)</span>
            </h4>
            <p class="text-xs text-slate-500 mb-4">Biarkan kosong jika tidak ingin mengubah password akun Anda.</p>

            <div class="space-y-4">
                <div>
                    <label for="password_lama" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password Saat Ini (Lama)
                    </label>
                    <input type="password" id="password_lama" name="password_lama"
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Masukkan password lama untuk konfirmasi">
                </div>

                <div>
                    <label for="password_baru" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password Baru (Minimal 6 karakter)
                    </label>
                    <input type="password" id="password_baru" name="password_baru"
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                        placeholder="Masukkan password baru">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-6 py-3 rounded-xl text-xs transition-all shadow-md shadow-blue-600/20">
                <i class="fa-solid fa-floppy-disk text-sm"></i>
                <span>Simpan Perubahan Profil</span>
            </button>
        </div>

    </form>
</div>
