<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Cek Jadwal Mediasi — PA Gorontalo' ?></title>
    <meta name="description" content="Layanan Informasi Cek Jadwal Mediasi Pihak Pengadilan Agama Gorontalo">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    heading: ['Outfit', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                }
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }

        @keyframes float-light-1 {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.4; }
            50% { transform: translate3d(50px, 40px, 0) scale(1.15); opacity: 0.6; }
        }
        @keyframes float-light-2 {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.3; }
            50% { transform: translate3d(-60px, -30px, 0) scale(1.2); opacity: 0.5; }
        }
        .light-orb-1 { animation: float-light-1 20s ease-in-out infinite; will-change: transform; }
        .light-orb-2 { animation: float-light-2 24s ease-in-out infinite; will-change: transform; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased relative overflow-x-hidden">

    <!-- Ambient Animated Background Layer -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="light-orb-1 absolute -top-40 -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-400/10 to-indigo-400/10 rounded-full blur-[140px]"></div>
        <div class="light-orb-2 absolute top-1/2 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-cyan-400/10 to-emerald-400/10 rounded-full blur-[160px]"></div>
    </div>

    <!-- Header Navbar -->
    <header class="bg-slate-950 text-white border-b border-slate-800 sticky top-0 z-30 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between">
            <?php $logo_url = get_app_logo_url(); ?>
            <a href="<?= site_url('publik') ?>" class="flex items-center gap-3.5 group">
                <?php if ($logo_url): ?>
                <img src="<?= $logo_url ?>" alt="Logo Satker" class="w-10 h-10 object-contain rounded-xl bg-white/10 p-1 shadow-md ring-1 ring-white/20 transition-transform group-hover:scale-105">
                <?php else: ?>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-md shadow-blue-600/30 ring-1 ring-white/20 transition-transform group-hover:scale-105">
                    <i class="fa-solid fa-scale-balanced text-white text-lg"></i>
                </div>
                <?php endif; ?>
                <div>
                    <h1 class="font-heading font-extrabold text-base leading-none text-white"><?= htmlspecialchars(get_app_setting('nama_satker', 'PA Gorontalo')) ?></h1>
                    <span class="text-[11px] text-blue-300 font-semibold tracking-wider uppercase block mt-1">Portal Cek Jadwal Mediasi (<?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?>)</span>
                </div>
            </a>
            <div>
                <a href="<?= site_url('auth') ?>" class="inline-flex items-center gap-2 text-xs font-semibold bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2.5 rounded-xl transition-all">
                    <i class="fa-solid fa-lock text-blue-400"></i>
                    <span>Login Petugas</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-4 md:p-8 relative z-10">
        <?php $this->load->view($content_view); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 py-8 text-xs mt-auto relative z-10">
        <div class="max-w-6xl mx-auto px-4 md:px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-building-columns text-blue-400 text-base"></i>
                <span>© <?= date('Y') ?> <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>. Hak Cipta Dilindungi.</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span><i class="fa-solid fa-location-dot text-slate-500 mr-1"></i> Jl. Jend. Sudirman No. 1, Gorontalo</span>
                <span><i class="fa-solid fa-phone text-slate-500 mr-1"></i> (0435) 123456</span>
            </div>
        </div>
    </footer>
</body>
</html>
