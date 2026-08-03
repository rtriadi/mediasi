<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Login — Mediasi PA Gorontalo' ?></title>
    <meta name="description" content="Login Sistem Informasi Pengelolaan Mediasi Perkara Pengadilan Agama Gorontalo">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        @keyframes float-orb-1 {

            0%,
            100% {
                transform: translate3d(0px, 0px, 0) scale(1);
                opacity: 0.35;
            }

            33% {
                transform: translate3d(60px, -80px, 0) scale(1.15);
                opacity: 0.55;
            }

            66% {
                transform: translate3d(-40px, 50px, 0) scale(0.9);
                opacity: 0.4;
            }
        }

        @keyframes float-orb-2 {

            0%,
            100% {
                transform: translate3d(0px, 0px, 0) scale(1);
                opacity: 0.4;
            }

            50% {
                transform: translate3d(-80px, 60px, 0) scale(1.25);
                opacity: 0.6;
            }
        }

        @keyframes float-orb-3 {

            0%,
            100% {
                transform: translate3d(0px, 0px, 0) scale(0.9);
                opacity: 0.3;
            }

            40% {
                transform: translate3d(70px, 70px, 0) scale(1.2);
                opacity: 0.5;
            }

            80% {
                transform: translate3d(-50px, -40px, 0) scale(0.35);
                opacity: 0.35;
            }
        }

        @keyframes pulse-mesh {

            0%,
            100% {
                opacity: 0.15;
                background-position: 0% 50%;
            }

            50% {
                opacity: 0.3;
                background-position: 100% 50%;
            }
        }

        @keyframes bounce-subtle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        .orb-1 {
            animation: float-orb-1 18s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .orb-2 {
            animation: float-orb-2 22s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .orb-3 {
            animation: float-orb-3 15s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 2.5s ease-in-out infinite;
        }

        .bg-mesh-grid {
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: pulse-mesh 10s ease-in-out infinite alternate;
        }

        @media (prefers-reduced-motion: reduce) {

            .orb-1,
            .orb-2,
            .orb-3,
            .bg-mesh-grid,
            .animate-bounce-subtle {
                animation: none !important;
            }
        }
    </style>
</head>

<body
    class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 relative overflow-hidden antialiased">

    <?php
    $logo_url = get_app_logo_url();
    $has_error_or_post = !empty($this->session->flashdata('error')) || ($_SERVER['REQUEST_METHOD'] === 'POST');
    ?>

    <?php if (!$has_error_or_post): ?>
        <!-- Professional Splash Screen Overlay (Hanya saat buka/reload halaman pertama kali) -->
        <div id="splash-screen"
            class="fixed inset-0 bg-slate-950 z-50 flex flex-col items-center justify-center transition-all duration-700 ease-out">
            <div class="relative flex flex-col items-center text-center p-6">
                <!-- Glowing Ambient Light behind logo -->
                <div class="absolute w-44 h-44 bg-blue-600/30 rounded-full blur-3xl animate-pulse"></div>

                <!-- Logo Container -->
                <div
                    class="relative z-10 w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-3 shadow-2xl shadow-blue-500/40 ring-1 ring-white/20 flex items-center justify-center animate-bounce-subtle mb-5">
                    <?php if ($logo_url): ?>
                        <img src="<?= $logo_url ?>" alt="Logo" class="w-14 h-14 object-contain">
                    <?php else: ?>
                        <i class="fa-solid fa-scale-balanced text-3xl text-white"></i>
                    <?php endif; ?>
                </div>

                <!-- App Name & Subtitle -->
                <h1
                    class="relative z-10 text-2xl md:text-3xl font-extrabold font-heading text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-blue-300 tracking-tight">
                    <?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?>
                </h1>
                <p class="relative z-10 text-xs font-semibold text-blue-400/90 tracking-widest uppercase mt-1">
                    <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>
                </p>

                <!-- Loading Progress Bar -->
                <div
                    class="relative z-10 w-48 h-1.5 bg-slate-800 rounded-full overflow-hidden mt-8 border border-slate-700/50 shadow-inner">
                    <div id="splash-bar"
                        class="h-full bg-gradient-to-r from-blue-500 via-indigo-400 to-cyan-400 rounded-full w-0 transition-all duration-1000 ease-out">
                    </div>
                </div>
                <p id="splash-status" class="relative z-10 text-[11px] text-slate-400 font-mono mt-2 tracking-wide">Memuat
                    Sistem...</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Animated Background Layer -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-mesh-grid"></div>

        <!-- Floating Light Orbs -->
        <div
            class="orb-1 absolute -top-20 -left-20 w-[480px] h-[480px] bg-gradient-to-tr from-blue-600/35 to-cyan-500/30 rounded-full blur-[140px]">
        </div>
        <div
            class="orb-2 absolute -bottom-20 -right-20 w-[550px] h-[550px] bg-gradient-to-br from-indigo-600/40 to-purple-600/30 rounded-full blur-[160px]">
        </div>
        <div
            class="orb-3 absolute top-1/3 left-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-gradient-to-r from-blue-500/25 to-emerald-500/20 rounded-full blur-[130px]">
        </div>

        <!-- Subtle Glowing Vignette -->
        <div class="absolute inset-0 bg-radial from-transparent via-slate-950/50 to-slate-950/90"></div>
    </div>

    <!-- Login Container (Main Box) -->
    <div id="login-box"
        class="relative w-full max-w-md z-10 transition-all duration-700 <?= $has_error_or_post ? '' : 'opacity-0 translate-y-4' ?>">

        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl mb-4 shadow-xl shadow-blue-500/30 ring-1 ring-white/20 hover:scale-105 transition-transform p-2">
                <?php if ($logo_url): ?>
                    <img src="<?= $logo_url ?>" alt="Logo Aplikasi" class="w-12 h-12 object-contain">
                <?php else: ?>
                    <i class="fa-solid fa-scale-balanced text-2xl text-white"></i>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight font-heading">
                <?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?>
            </h1>
            <p class="text-blue-300/80 text-sm font-medium mt-1">
                <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>
            </p>
        </div>

        <!-- Login Card -->
        <div
            class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-slate-800/90 shadow-2xl p-8 md:p-10 relative ring-1 ring-white/10 hover:border-slate-700/80 transition-all">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-white font-heading">Masuk ke Sistem</h2>
                <p class="text-xs text-slate-400 mt-1">Masukkan kredensial akun petugas yang terdaftar</p>
            </div>

            <!-- Error Flash -->
            <?php if ($this->session->flashdata('error')): ?>
                <div
                    class="bg-red-500/10 border border-red-500/30 text-red-300 text-xs rounded-2xl p-4 mb-6 flex items-start gap-3 animate-fade-in">
                    <i class="fa-solid fa-circle-exclamation text-red-400 mt-0.5 text-sm"></i>
                    <p class="font-semibold leading-relaxed"><?= $this->session->flashdata('error') ?></p>
                </div>
            <?php endif; ?>

            <form id="form-login" action="<?= site_url('auth/login') ?>" method="POST" class="space-y-5">
                <!-- Username -->
                <div>
                    <label for="username"
                        class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Username</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                            class="w-full bg-slate-950/70 border border-slate-800 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono"
                            placeholder="Masukkan username" autocomplete="username">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password"
                        class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-slate-950/70 border border-slate-800 rounded-xl pl-10 pr-12 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Masukkan password" autocomplete="current-password">
                        <button type="button" id="btn-toggle-pw"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                            <i class="fa-solid fa-eye text-sm" id="icon-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-login"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:from-blue-700 active:to-indigo-700 text-white font-bold py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 text-sm tracking-wide mt-2">
                    <span>Masuk ke Akun</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Public Portal Link -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
                <a href="<?= site_url('publik') ?>"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-blue-400 hover:text-blue-300 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cek Jadwal Mediasi Pihak (Publik)</span>
                </a>
            </div>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">
            © <?= date('Y') ?> Pengadilan Agama Gorontalo · Hak Cipta Dilindungi
        </p>
    </div>

    <script>
        document.getElementById('btn-toggle-pw').addEventListener('click', function () {
            const pw = document.getElementById('password');
            const icon = document.getElementById('icon-eye');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.className = 'fa-solid fa-eye-slash text-sm';
            } else {
                pw.type = 'password';
                icon.className = 'fa-solid fa-eye text-sm';
            }
        });

        <?php if (!$has_error_or_post): ?>
            // Splash Screen Handler (Hanya berjalan saat buka/reload halaman pertama kali)
            window.addEventListener('load', function () {
                const splash = document.getElementById('splash-screen');
                const bar = document.getElementById('splash-bar');
                const status = document.getElementById('splash-status');
                const loginBox = document.getElementById('login-box');

                setTimeout(() => {
                    if (bar) bar.style.width = '100%';
                    if (status) status.innerText = 'P A S T I !';
                }, 150);

                setTimeout(() => {
                    if (splash) {
                        splash.classList.add('opacity-0', 'scale-105', 'pointer-events-none');
                        setTimeout(() => splash.remove(), 700);
                    }
                    if (loginBox) {
                        loginBox.classList.remove('opacity-0', 'translate-y-4');
                    }
                }, 1100);
            });
        <?php endif; ?>
    </script>
</body>

</html>