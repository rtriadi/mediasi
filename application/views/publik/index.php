<div class="max-w-2xl mx-auto my-6 md:my-12">
    <!-- Hero Banner -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl mb-5 shadow-inner border border-blue-100">
            <i class="fa-solid fa-magnifying-glass-location text-3xl"></i>
        </div>
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight font-heading">Cek Jadwal Mediasi Perkara</h2>
        <p class="text-sm md:text-base text-slate-600 mt-2 max-w-lg mx-auto leading-relaxed">
            Layanan publik untuk mengecek jadwal sesi mediasi, lokasi ruangan, nama mediator, dan perkembangan perkara di <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>.
        </p>
    </div>

    <!-- Search Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl p-6 md:p-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <form id="form-cari-publik" method="POST" action="<?= site_url('publik/cari') ?>" class="space-y-5">
            <div>
                <label for="nomor_perkara" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Nomor Perkara Lengkap
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-file-signature text-base"></i>
                    </div>
                    <input type="text" id="nomor_perkara" name="nomor_perkara" required
                        class="w-full bg-slate-50 border border-slate-300 focus:bg-white rounded-2xl pl-11 pr-4 py-4 text-base md:text-lg text-slate-900 font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all shadow-sm"
                        placeholder="Contoh: 123/Pdt.G/2026/PA.Gtlo">
                </div>
                <p class="text-xs text-slate-400 mt-2.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <span>Masukkan nomor perkara persis seperti di Akta Cerai / Gugatan / Surat Panggilan.</span>
                </p>
            </div>

            <button type="submit" id="btn-cari"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 flex items-center justify-center gap-2.5 text-base tracking-wide">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Cari Informasi Jadwal</span>
            </button>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-calendar-day text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Jadwal Real-Time</h4>
                <p class="text-xs text-slate-500 mt-0.5">Informasi sesi mediasi terupdate dari sistem</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-handshake text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Nama Mediator</h4>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan Hakim / Non-Hakim penanggungjawab</p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-building text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Lokasi Ruangan</h4>
                <p class="text-xs text-slate-500 mt-0.5">Lokasi pasti pelaksanaan mediasi perkara</p>
            </div>
        </div>
    </div>
</div>
