<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= strip_tags($heading) ?> — SIPO-MEDIASI PA Gorontalo</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
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
        body { font-family: 'Inter', sans-serif; }
        .bg-grid-pattern {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center p-4 bg-grid-pattern relative overflow-hidden">

    <!-- Ambient Light Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-rose-600/20 rounded-full blur-[128px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-600/20 rounded-full blur-[128px] pointer-events-none"></div>

    <div class="max-w-2xl w-full z-10">
        <!-- Main Error Card -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-rose-950/20 text-center relative overflow-hidden">
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 via-purple-500 to-indigo-500"></div>

            <!-- Icon Header -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner shadow-rose-500/10">
                <i class="fa-solid fa-database text-rose-500 text-3xl sm:text-4xl"></i>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-heading tracking-tight mb-3">
                <?= htmlspecialchars(strip_tags($heading)) ?>
            </h1>

            <p class="text-slate-400 text-xs sm:text-sm mb-6">
                Terjadi kendala pada pengolahan query basis data. Silakan coba kembali atau hubungi administrator sistem.
            </p>

            <!-- Message / Code Block -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 mb-6 text-xs text-rose-300 font-mono leading-relaxed text-left overflow-x-auto max-h-60">
                <?= $message ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button onclick="history.back()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-5 py-2.5 rounded-xl transition-all border border-slate-700/80 text-sm shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </button>
                <button onclick="location.reload()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold px-6 py-2.5 rounded-xl transition-all text-sm shadow-lg shadow-purple-600/20">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    <span>Coba Lagi</span>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-500 mt-6 font-medium">
            SIPO-MEDIASI &bull; Pengadilan Agama Gorontalo
        </p>
    </div>

</body>
</html>