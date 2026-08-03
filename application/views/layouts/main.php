<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — ' . htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) : htmlspecialchars(get_app_setting('nama_satker', 'PA Gorontalo')) ?></title>
    <meta name="description" content="<?= htmlspecialchars(get_app_setting('slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara')) ?> — <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?>">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jQuery & Select2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    heading: ['Outfit', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                colors: {
                    navy: {
                        50: '#f0f4f8',
                        100: '#d9e2ec',
                        200: '#bcccdc',
                        300: '#9fb3c8',
                        400: '#829ab1',
                        500: '#627d98',
                        600: '#486581',
                        700: '#334e68',
                        800: '#243b53',
                        900: '#102a43',
                        950: '#0b1d30',
                    },
                    brand: {
                        50: '#eff6ff',
                        100: '#dbeafe',
                        500: '#2563eb',
                        600: '#1d4ed8',
                        700: '#1e40af',
                    }
                },
                boxShadow: {
                    'card': '0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 1px 2px -1px rgba(15, 23, 42, 0.03)',
                    'card-hover': '0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04)',
                    'glass': '0 8px 32px 0 rgba(15, 23, 42, 0.08)',
                }
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .flash-success { background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; }
        .flash-error   { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Select2 Tailwind styling overrides */
        .select2-container--default .select2-selection--single {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            height: 38px;
            display: flex;
            align-items: center;
            padding-left: 6px;
            padding-right: 6px;
            font-size: 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a;
            font-weight: 500;
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            font-size: 0.75rem;
            z-index: 9999;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
<div class="flex min-h-screen w-full bg-slate-50">

    <!-- Sidebar -->
    <?php $this->load->view('layouts/sidebar'); ?>

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50 min-h-screen">
        
        <!-- Top Bar Header -->
        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 md:px-6 py-3.5 flex items-center justify-between z-10 sticky top-0 shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Hamburger Sidebar Toggle Button (Desktop & Mobile) -->
                <button type="button" onclick="toggleSidebar()" id="btn-toggle-sidebar"
                    title="Toggle Sidebar (Sembunyikan/Tampilkan Menu)"
                    class="text-slate-600 hover:text-slate-900 p-2 rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors focus:outline-none flex items-center justify-center">
                    <i class="fa-solid fa-bars-staggered text-base md:text-lg"></i>
                </button>
                <h1 class="text-base md:text-xl font-bold font-heading text-slate-900 tracking-tight flex items-center gap-2 truncate">
                    <?= isset($title) ? htmlspecialchars($title) : 'Dashboard' ?>
                </h1>
            </div>
            
            <div class="flex items-center gap-3 md:gap-5">
                <!-- Date Pill -->
                <div class="hidden sm:flex items-center gap-2 bg-slate-100/80 text-slate-600 px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200/60">
                    <i class="fa-regular fa-calendar-check text-blue-600"></i>
                    <span><?= tgl_indo() ?></span>
                </div>

                <!-- User Profile Pill -->
                <div class="flex items-center gap-2 md:gap-3 pl-2 md:pl-3 border-l border-slate-200">
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs md:text-sm flex items-center justify-center shadow-sm ring-2 ring-blue-500/20">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-xs font-bold text-slate-900 leading-tight"><?= htmlspecialchars($user['nama']) ?></p>
                        <div class="flex items-center gap-1 mt-0.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] text-slate-500 uppercase tracking-wider font-semibold">
                                <?= $user['role'] ?><?= $user['is_mediator'] ? ' · Mediator' : '' ?>
                            </span>
                        </div>
                    </div>
                    <a href="<?= site_url('profil/ganti_password') ?>" 
                       title="Ganti Password Saya"
                       class="ml-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all duration-200">
                        <i class="fa-solid fa-key text-base"></i>
                    </a>
                    <a href="<?= site_url('logout') ?>" 
                       title="Keluar dari Aplikasi"
                       class="text-slate-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-all duration-200">
                        <i class="fa-solid fa-right-from-bracket text-base"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Workspace Area -->
        <main class="flex-1 p-4 md:p-8 space-y-6">
            <!-- Flash Notifications -->
            <?php if ($this->session->flashdata('success')): ?>
            <div class="flash-success rounded-xl p-4 shadow-sm border border-emerald-200/60 flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-check text-sm"></i>
                    </div>
                    <p class="text-sm font-semibold text-emerald-900"><?= $this->session->flashdata('success') ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 text-sm font-bold px-2">×</button>
            </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
            <div class="flash-error rounded-xl p-4 shadow-sm border border-red-200/60 flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-exclamation text-sm"></i>
                    </div>
                    <p class="text-sm font-semibold text-red-900"><?= $this->session->flashdata('error') ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-950 text-sm font-bold px-2">×</button>
            </div>
            <?php endif; ?>

            <!-- View Injection -->
            <?php $this->load->view($content_view); ?>
        </main>

    </div>
</div>

<!-- Global Tailwind Confirmation Modal -->
<div id="global-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-xl shadow-sm">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 id="confirm-modal-title" class="font-extrabold text-base text-slate-900 font-heading">Konfirmasi Tindakan</h3>
            <p id="confirm-modal-msg" class="text-xs text-slate-500 mt-1 leading-relaxed">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" onclick="closeConfirmModal()" class="w-1/2 px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
            <a id="confirm-modal-btn" href="#" class="w-1/2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-md shadow-rose-600/20 text-center">Ya, Lanjutkan</a>
        </div>
    </div>
</div>

<script>
function showConfirmModal(url, title, msg, btnText) {
    const modal = document.getElementById('global-confirm-modal');
    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-msg');
    const btnEl = document.getElementById('confirm-modal-btn');
    if (!modal) return false;

    if (titleEl) titleEl.innerText = title || 'Konfirmasi Hapus';
    if (msgEl) msgEl.innerText = msg || 'Apakah Anda yakin ingin menghapus data ini?';
    if (btnEl) {
        btnEl.href = url;
        btnEl.innerText = btnText || 'Ya, Lanjutkan';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    return false;
}

function closeConfirmModal() {
    const modal = document.getElementById('global-confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function toggleSidebar(forceState) {
    const sidebar = document.getElementById('app-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar) return;

    const isMobile = window.innerWidth < 1024;

    if (isMobile) {
        if (typeof forceState === 'boolean') {
            if (forceState) {
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }
        } else {
            const isHidden = sidebar.classList.contains('-translate-x-full');
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }
        }
    } else {
        // Desktop Mode: Toggle lg:-ml-64 dengan transisi mulus
        const isCollapsed = sidebar.classList.contains('lg:-ml-64');
        if (isCollapsed) {
            sidebar.classList.remove('lg:-ml-64');
            localStorage.setItem('sidebar_desktop_collapsed', 'false');
        } else {
            sidebar.classList.add('lg:-ml-64');
            localStorage.setItem('sidebar_desktop_collapsed', 'true');
        }
    }
}

// Pulihkan status sidebar desktop dari localStorage saat halaman dimuat
(function() {
    if (window.innerWidth >= 1024 && localStorage.getItem('sidebar_desktop_collapsed') === 'true') {
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('app-sidebar');
            if (sidebar) sidebar.classList.add('lg:-ml-64');
        });
    }
})();

$(document).ready(function() {
    // Init Select2 on all select elements
    $('select').select2({
        width: '100%'
    });

    // Auto submit form filter on select change
    $(document).on('change', 'form#form-filter select', function() {
        $(this).closest('form').submit();
    });

    // Debounced auto submit on text input typing (350ms)
    var searchTimer;
    $(document).on('input', 'form#form-filter input[type="text"]', function() {
        clearTimeout(searchTimer);
        var $form = $(this).closest('form');
        searchTimer = setTimeout(function() {
            $form.submit();
        }, 350);
    });
});
</script>
</body>
</html>
