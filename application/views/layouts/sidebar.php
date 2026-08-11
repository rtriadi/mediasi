<?php
$role        = isset($user) ? $user['role'] : '';
$is_mediator = isset($user) ? $user['is_mediator'] : false;

function nav_item_v2($url, $label, $icon_class, $active_segment = '')
{
	$ci = &get_instance();
	$seg1 = $ci->uri->segment(1);
	$seg2 = $ci->uri->segment(2);
	$full_seg = $seg1 . ($seg2 ? '/' . $seg2 : '');
	$is_active = ($active_segment && (strpos($full_seg, $active_segment) !== false));

	$base = "flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 group relative";
	$active_style = "bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25";
	$inactive_style = "text-slate-300 hover:bg-slate-800/80 hover:text-white";
	$cls = $is_active ? "{$base} {$active_style}" : "{$base} {$inactive_style}";

	$icon_active = $is_active ? "text-white" : "text-slate-400 group-hover:text-blue-400";

	echo "<a href=\"{$url}\" class=\"{$cls}\">";
	if ($is_active) {
		echo "<span class=\"absolute left-0 top-2 bottom-2 w-1 bg-white rounded-r-full\"></span>";
	}
	echo "<i class=\"{$icon_class} text-sm w-5 text-center transition-transform group-hover:scale-110 {$icon_active}\"></i>";
	echo "<span>{$label}</span>";
	echo "</a>";
}

$logo_url = get_app_logo_url();
$nama_satker = get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo');
$nama_aplikasi = get_app_setting('nama_aplikasi', 'SIPO-MEDIASI');
?>

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="sidebar-backdrop" onclick="toggleSidebar(false)" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 hidden transition-opacity duration-300 lg:hidden"></div>

<!-- Sidebar Component -->
<aside id="app-sidebar" class="fixed lg:sticky top-0 h-screen w-64 bg-slate-950 text-slate-100 flex flex-col flex-shrink-0 border-r border-slate-800/80 z-50 shadow-2xl transition-all duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">

	<!-- Logo & Brand Header -->
	<div class="px-5 py-5 border-b border-slate-800/80 bg-slate-900/40 flex items-center justify-between">
		<div class="flex items-center gap-3">
			<?php if ($logo_url): ?>
				<img src="<?= $logo_url ?>" alt="Logo Satker" class="w-10 h-10 object-contain rounded-xl bg-white/10 p-1 shadow-md ring-1 ring-white/20">
			<?php else: ?>
				<div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/30 ring-1 ring-white/20 flex-shrink-0">
					<i class="fa-solid fa-scale-balanced text-white text-lg"></i>
				</div>
			<?php endif; ?>
			<div class="min-w-0 flex-1">
				<h2 class="font-heading font-extrabold text-sm tracking-tight text-white leading-tight truncate"><?= htmlspecialchars($nama_satker) ?></h2>
				<span class="text-[10px] font-semibold tracking-wider uppercase text-blue-400 block mt-0.5 truncate"><?= htmlspecialchars($nama_aplikasi) ?></span>
			</div>
		</div>
		<!-- Mobile Close Button -->
		<button type="button" onclick="toggleSidebar(false)" class="lg:hidden text-slate-400 hover:text-white p-1 ml-1">
			<i class="fa-solid fa-xmark text-lg"></i>
		</button>
	</div>

	<!-- Navigation Menu -->
	<nav class="flex-1 px-3.5 py-5 space-y-6 overflow-y-auto">

		<?php
		$user_roles   = isset($user['roles']) && is_array($user['roles']) ? $user['roles'] : [$role];
		$has_mediator = in_array('mediator', $user_roles);
		?>

		<?php if (in_array('admin', $user_roles)): ?>
			<div>
				<p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Master Data & Config</p>
				<div class="space-y-1">
					<?php
					nav_item_v2(site_url('pimpinan/dashboard'), 'Dashboard & Grafik', 'fa-solid fa-chart-pie', 'pimpinan/dashboard');
					nav_item_v2(site_url('admin/master_user'), 'Kelola User', 'fa-solid fa-users-gear', 'admin/master_user');
					nav_item_v2(site_url('admin/master_mediator'), 'Kelola Mediator', 'fa-solid fa-user-tie', 'admin/master_mediator');
					nav_item_v2(site_url('admin/master_ruangan'), 'Kelola Ruangan', 'fa-solid fa-door-open', 'admin/master_ruangan');
					nav_item_v2(site_url('admin/master_jenis_perkara'), 'Jenis Perkara', 'fa-solid fa-gavel', 'admin/master_jenis_perkara');
					nav_item_v2(site_url('admin/laporan'), 'Laporan Mediasi', 'fa-solid fa-chart-line', 'admin/laporan');
					nav_item_v2(site_url('admin/notifikasi_log'), 'Log Notifikasi', 'fa-solid fa-clock-rotate-left', 'admin/notifikasi_log');
					nav_item_v2(site_url('admin/pengaturan'), 'Pengaturan Aplikasi', 'fa-solid fa-sliders', 'admin/pengaturan');
					nav_item_v2(site_url('admin/backup'), 'Backup Database', 'fa-solid fa-database', 'admin/backup');
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (in_array('pp', $user_roles)): ?>
			<div>
				<p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Panitera Pengganti</p>
				<div class="space-y-1">
					<?php
					nav_item_v2(site_url('pp/monitor'), 'Monitor Perkara', 'fa-solid fa-list-check', 'pp/monitor');
					nav_item_v2(site_url('pimpinan/dashboard'), 'Grafik & Laporan', 'fa-solid fa-chart-pie', 'pimpinan/dashboard');
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (in_array('hakim', $user_roles)): ?>
			<div>
				<p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Hakim</p>
				<div class="space-y-1">
					<?php
					nav_item_v2(site_url('hakim/perkara'), 'Semua Perkara', 'fa-solid fa-folder-tree', 'hakim/perkara');
					nav_item_v2(site_url('pimpinan/dashboard'), 'Grafik & Laporan', 'fa-solid fa-chart-pie', 'pimpinan/dashboard');
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (in_array('pimpinan', $user_roles)): ?>
			<div>
				<p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">Pimpinan PA</p>
				<div class="space-y-1">
					<?php
					nav_item_v2(site_url('pimpinan/dashboard'), 'Dashboard Statistik', 'fa-solid fa-chart-pie', 'pimpinan/dashboard');
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($has_mediator): ?>
			<div>
				<p class="px-3 text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2.5">Mediator Perkara</p>
				<div class="space-y-1">
					<?php
					nav_item_v2(site_url('mediator/perkara_saya'), 'Perkara Mediasi Saya', 'fa-solid fa-briefcase', 'mediator/perkara_saya');
					nav_item_v2(site_url('mediator/jadwal'), 'Jadwal Mediasi Saya', 'fa-solid fa-calendar-days', 'mediator/jadwal');
					?>
				</div>
			</div>
		<?php endif; ?>

	</nav>

	<!-- Sidebar Footer -->
	<div class="px-4 py-4 border-t border-slate-800/80 bg-slate-900/60">
		<a href="<?= site_url('publik') ?>" target="_blank"
			class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-blue-600/20 text-slate-300 hover:text-blue-300 text-xs font-semibold border border-slate-700/60 transition-all duration-200 group">
			<span class="flex items-center gap-2">
				<i class="fa-solid fa-globe text-blue-400"></i>
				Portal Publik
			</span>
			<i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 group-hover:text-blue-300"></i>
		</a>
	</div>

</aside>
