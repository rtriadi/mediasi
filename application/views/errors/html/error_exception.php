<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="my-4 p-4 bg-slate-900/90 border border-rose-500/40 rounded-2xl text-slate-200 text-xs font-mono backdrop-blur-md shadow-xl">
    <div class="flex items-center gap-2 text-rose-400 font-bold text-sm mb-2 pb-2 border-b border-slate-800">
        <i class="fa-solid fa-bug"></i>
        <span>Uncaught Exception (<?= htmlspecialchars(get_class($exception)) ?>)</span>
    </div>
    <div class="space-y-1">
        <p><strong class="text-slate-400">Pesan:</strong> <span class="text-rose-300"><?= htmlspecialchars($message) ?></span></p>
        <p><strong class="text-slate-400">File:</strong> <span class="text-amber-200"><?= htmlspecialchars($exception->getFile()) ?></span></p>
        <p><strong class="text-slate-400">Baris:</strong> <span class="text-emerald-300"><?= htmlspecialchars($exception->getLine()) ?></span></p>
    </div>

    <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
    <details class="mt-3 pt-2 border-t border-slate-800">
        <summary class="cursor-pointer text-slate-400 hover:text-white font-sans font-semibold">Tampilkan Exception Backtrace</summary>
        <div class="mt-2 space-y-1.5 pl-2 border-l-2 border-slate-700">
            <?php foreach ($exception->getTrace() as $error): ?>
                <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
                    <p class="text-[11px] text-slate-400">
                        📄 <span class="text-slate-300"><?= htmlspecialchars($error['file']) ?></span> (Line <?= $error['line'] ?? '-' ?>) → <strong class="text-blue-300"><?= htmlspecialchars($error['function'] ?? '') ?>()</strong>
                    </p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </details>
    <?php endif; ?>
</div>