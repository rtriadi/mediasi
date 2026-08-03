<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-8 text-white relative">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-blue-600/30 border border-white/20 text-white font-bold text-xl flex items-center justify-center shadow-inner">
                    <?= strtoupper(substr($user_data->nama, 0, 1)) ?>
                </div>
                <div>
                    <h2 class="text-xl font-bold font-heading text-white"><?= htmlspecialchars($user_data->nama) ?></h2>
                    <p class="text-xs text-slate-300 font-mono mt-0.5">@<?= htmlspecialchars($user_data->username) ?> · <span class="uppercase tracking-wider font-semibold text-blue-400"><?= $user_data->role ?></span></p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6 md:p-8">
            <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-key text-blue-600"></i>
                <span>Form Ganti Password</span>
            </h3>
            <p class="text-xs text-slate-500 mb-6">Pastikan Anda mengingat password baru yang akan Anda buat.</p>

            <form method="POST" action="<?= site_url('profil/ganti_password') ?>" class="space-y-5">
                <div>
                    <label for="pass_lama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Saat Ini (Lama) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="pass_lama" name="pass_lama" required
                            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Masukkan password saat ini">
                        <button type="button" onclick="togglePasswordVisibility('pass_lama', this)" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="pass_baru" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="pass_baru" name="pass_baru" required minlength="6"
                            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePasswordVisibility('pass_baru', this)" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Kombinasikan huruf, angka, dan simbol untuk keamanan maksimal.</p>
                </div>

                <div>
                    <label for="konfirmasi_pass" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Konfirmasi Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="konfirmasi_pass" name="konfirmasi_pass" required minlength="6"
                            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all pr-10"
                            placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePasswordVisibility('konfirmasi_pass', this)" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md shadow-blue-500/20 transition-all">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Simpan Password Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
