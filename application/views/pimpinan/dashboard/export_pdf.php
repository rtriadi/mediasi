<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Mediasi — <?= htmlspecialchars(get_app_setting('nama_satker', 'PA Gorontalo')) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: #111; margin: 0; padding: 20px; background: #fff; }

        /* KOP SURAT */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 6px; }
        .kop-logo { width: 80px; text-align: center; flex-shrink: 0; }
        .kop-logo img { height: 72px; }
        .kop-logo-placeholder { height: 72px; width: 72px; border: 2px solid #555; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; text-align: center; color: #555; margin: 0 auto; padding: 6px; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .instansi-atas { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; }
        .kop-text .instansi-utama { font-size: 17px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; line-height: 1.3; }
        .kop-text .instansi-alamat { font-size: 10px; color: #444; margin-top: 3px; }

        /* JUDUL */
        .doc-title { text-align: center; margin: 14px 0 4px 0; }
        .doc-title h2 { font-size: 14px; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .doc-title p { font-size: 11px; margin: 3px 0 0 0; color: #444; }
        .doc-divider { border: none; border-top: 1px solid #999; margin: 10px 0; }

        /* SUMMARY */
        .summary-grid { display: flex; gap: 6px; margin-bottom: 16px; }
        .summary-box { flex: 1; border: 1px solid #ccc; border-radius: 4px; padding: 10px 8px; text-align: center; }
        .summary-box .label { font-size: 10px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-box .value { font-size: 22px; font-weight: bold; margin: 4px 0 2px; }
        .summary-box .pct { font-size: 10px; font-weight: bold; }
        .box-total { border-color: #555; }
        .box-total .value { color: #111; }
        .box-berhasil { border-color: #2e7d32; background: #f1f8e9; }
        .box-berhasil .value { color: #2e7d32; }
        .box-berhasil .pct { color: #388e3c; }
        .box-sebagian { border-color: #f57f17; background: #fffde7; }
        .box-sebagian .value { color: #e65100; }
        .box-sebagian .pct { color: #bf360c; }
        .box-gagal { border-color: #c62828; background: #fce4e4; }
        .box-gagal .value { color: #c62828; }
        .box-gagal .pct { color: #b71c1c; }

        /* TABEL */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 11px; }
        .data-table thead tr { background: #e8eaf6; }
        .data-table th { border: 1px solid #999; padding: 6px 7px; text-align: left; font-weight: bold; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-table td { border: 1px solid #ccc; padding: 5px 7px; vertical-align: top; }
        .data-table tbody tr:nth-child(even) { background: #f8f8ff; }
        .td-no { text-align: center; width: 28px; }
        .td-center { text-align: center; }
        .badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-berhasil { background: #c8e6c9; color: #1b5e20; border: 1px solid #a5d6a7; }
        .badge-sebagian { background: #fff9c4; color: #7c4d00; border: 1px solid #ffe082; }
        .badge-gagal    { background: #ffcdd2; color: #7f0000; border: 1px solid #ef9a9a; }

        /* TANDA TANGAN */
        .ttd-section { margin-top: 28px; page-break-inside: avoid; display: flex; justify-content: space-between; align-items: flex-start; }
        .ttd-kiri { font-size: 11px; }
        .ttd-kiri p { margin: 2px 0; }
        .ttd-kanan { text-align: center; width: 220px; }
        .ttd-kanan .ttd-title { font-size: 11px; }
        .ttd-kanan .ttd-space { height: 65px; }
        .ttd-kanan .ttd-nama { font-size: 11px; font-weight: bold; border-top: 1px solid #333; padding-top: 4px; }
        .ttd-kanan .ttd-nip  { font-size: 10px; color: #555; margin-top: 3px; }

        .print-btn { display: block; text-align: center; margin: 22px auto 0; cursor: pointer; padding: 9px 32px; background: #1a237e; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; }
        @media print {
            .print-btn { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">
            <?php $logo = get_app_logo_url(); ?>
            <?php if ($logo): ?>
                <img src="<?= $logo ?>" alt="Logo Satker">
            <?php else: ?>
                <div class="kop-logo-placeholder">LOGO<br>SATKER</div>
            <?php endif; ?>
        </div>
        <div class="kop-text">
            <div class="instansi-atas">Mahkamah Agung Republik Indonesia</div>
            <div class="instansi-utama"><?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?></div>
            <div class="instansi-alamat">Sistem Informasi Pengelolaan Mediasi Perkara (<?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?>)</div>
        </div>
        <div style="width:80px"></div>
    </div>

    <!-- JUDUL -->
    <div class="doc-title">
        <h2>Laporan Rekapitulasi Hasil Mediasi Perkara</h2>
        <p>
            Periode:
            <?php
            if (!empty($filter['bulan']) && !empty($filter['tahun'])) {
                $bln = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                echo htmlspecialchars($bln[(int)$filter['bulan']] . ' ' . $filter['tahun']);
            } elseif (!empty($filter['triwulan']) && !empty($filter['tahun'])) {
                $tw = ['I (Januari – Maret)', 'II (April – Juni)', 'III (Juli – September)', 'IV (Oktober – Desember)'];
                echo 'Triwulan ' . htmlspecialchars(($tw[$filter['triwulan'] - 1] ?? $filter['triwulan']) . ' ' . $filter['tahun']);
            } elseif (!empty($filter['tahun'])) {
                echo 'Tahun ' . htmlspecialchars($filter['tahun']);
            } else {
                echo 'Semua Periode';
            }
            ?>
        </p>
    </div>
    <hr class="doc-divider">

    <!-- RINGKASAN STATISTIK -->
    <?php
        $total    = (int)$summary['total'];
        $berhasil = (int)$summary['berhasil'];
        $sebagian = (int)$summary['berhasil_sebagian'];
        $gagal    = (int)$summary['tidak_berhasil'];
        $total_berhasil = $berhasil + $sebagian;
        $pct_b = $total > 0 ? round(($total_berhasil / $total) * 100, 1) : 0;
        $pct_s = $total > 0 ? round(($sebagian / $total) * 100, 1) : 0;
        $pct_g = $total > 0 ? round(($gagal / $total) * 100, 1) : 0;
    ?>
    <div class="summary-grid">
        <div class="summary-box box-total">
            <div class="label">Total Mediasi Selesai</div>
            <div class="value"><?= $total ?></div>
            <div class="pct">Perkara</div>
        </div>
        <div class="summary-box box-berhasil">
            <div class="label">Berhasil Sepenuhnya</div>
            <div class="value"><?= $berhasil ?></div>
            <div class="pct"><?= $pct_b ?>% Keberhasilan</div>
        </div>
        <div class="summary-box box-sebagian">
            <div class="label">Berhasil Sebagian</div>
            <div class="value"><?= $sebagian ?></div>
            <div class="pct"><?= $pct_s ?>% dari Total</div>
        </div>
        <div class="summary-box box-gagal">
            <div class="label">Tidak Berhasil</div>
            <div class="value"><?= $gagal ?></div>
            <div class="pct"><?= $pct_g ?>% dari Total</div>
        </div>
    </div>

    <!-- TABEL DETAIL PERKARA -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="td-no">No</th>
                <th>Nomor Perkara</th>
                <th>Jenis Perkara</th>
                <th>Mediator</th>
                <th class="td-center">Hasil Mediasi</th>
                <th class="td-center" style="width:72px">Batas Mediasi</th>
                <th class="td-center" style="width:72px">Tgl Selesai</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detail)): ?>
            <tr><td colspan="7" style="text-align:center; padding: 14px; color: #888;">Tidak ada data pada periode yang dipilih.</td></tr>
            <?php else: ?>
            <?php $no = 1; foreach ($detail as $d): ?>
            <tr>
                <td class="td-no"><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($d->nomor_perkara) ?></strong></td>
                <td><?= htmlspecialchars($d->jenis_perkara) ?></td>
                <td><?= htmlspecialchars($d->mediator) ?></td>
                <td class="td-center">
                    <?php if ($d->hasil === 'berhasil'): ?>
                        <span class="badge badge-berhasil">✓ Berhasil</span>
                    <?php elseif ($d->hasil === 'berhasil_sebagian'): ?>
                        <span class="badge badge-sebagian">~ Berhasil Sebagian</span>
                    <?php else: ?>
                        <span class="badge badge-gagal">✕ Tidak Berhasil</span>
                    <?php endif; ?>
                </td>
                <td class="td-center"><?= date('d/m/Y', strtotime($d->tgl_batas_mediasi)) ?></td>
                <td class="td-center"><?= date('d/m/Y', strtotime($d->tgl_hasil)) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-section">
        <div class="ttd-kiri">
            <p>Dicetak oleh sistem pada:</p>
            <p><strong><?= date('l, d F Y \P\u\k\u\l H:i') ?> WITA</strong></p>
            <p style="color:#666; font-size:10px; margin-top:6px;"><?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?></p>
        </div>
        <div class="ttd-kanan">
            <div class="ttd-title">Gorontalo, <?= date('d F Y') ?></div>
            <div class="ttd-title" style="margin-top:4px;">Ketua <?= htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo')) ?></div>
            <div class="ttd-space"></div>
            <div class="ttd-nama">( ________________________ )</div>
            <div class="ttd-nip">NIP. _______________________</div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>

</body>
</html>
