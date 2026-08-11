<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 2px 0 0 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0 0 0; font-size: 9pt; font-style: italic; }
        .judul { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 15px; text-transform: uppercase; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px 6px; }
        table.data th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-box { width: 45%; float: right; text-align: center; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background: #e2e8f0; padding: 10px; text-align: center; margin-bottom: 15px; border-radius: 8px;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; font-weight: bold; border-radius: 6px; cursor: pointer;">
            🖨️ Cetak Dokumen Laporan
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="kop">
        <h2>MAHKAMAH AGUNG REPUBLIK INDONESIA</h2>
        <h3><?= strtoupper(get_app_setting('nama_satker', 'PENGADILAN AGAMA GORONTALO')) ?></h3>
        <p>Sistem Informasi Pengelolaan Mediasi Perkara (SIPO-MEDIASI)</p>
    </div>

    <!-- Judul -->
    <div class="judul">
        LAPORAN REKAPITULASI PELAKSANAAN MEDIASI PERKARA<br>
        PERIODE: <?= strtoupper(bulan_indo($bulan)) ?> <?= $tahun ?>
    </div>

    <!-- Tabel Data -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 140px;">NOMOR PERKARA</th>
                <th>JENIS PERKARA</th>
                <th>MAJELIS HAKIM</th>
                <th>MEDIATOR DITUGASKAN</th>
                <th style="width: 85px;">BATAS MEDIASI</th>
                <th style="width: 130px;">STATUS / HASIL</th>
                <th style="width: 85px;">TGL LAPORAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($perkaras)): ?>
            <tr><td colspan="8" style="text-align: center; padding: 15px;">Tidak ada data perkara mediasi untuk periode ini.</td></tr>
            <?php else: ?>
            <?php $no = 1; foreach ($perkaras as $p): ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td style="font-weight: bold; font-family: monospace;"><?= htmlspecialchars($p->nomor_perkara) ?></td>
                <td><?= htmlspecialchars($p->jenis_perkara) ?></td>
                <td><?= htmlspecialchars($p->majelis_hakim ?? $p->nama_hakim ?? '—') ?></td>
                <td><?= htmlspecialchars($p->nama_mediator ?: '—') ?></td>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($p->tgl_batas_mediasi)) ?></td>
                <td style="text-align: center;">
                    <?php
                    if ($p->status === 'selesai') {
                        if ($p->hasil === 'berhasil') echo '✓ Berhasil (Damai)';
                        elseif ($p->hasil === 'berhasil_sebagian') echo '~ Berhasil Sebagian';
                        else echo '✕ Tidak Berhasil';
                    } elseif ($p->status === 'proses') {
                        echo 'Dalam Proses';
                    } else {
                        echo 'Menunggu Sesi 1';
                    }
                    ?>
                </td>
                <td style="text-align: center;"><?= $p->tgl_laporan ? date('d/m/Y', strtotime($p->tgl_laporan)) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Footer -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p><?= get_app_setting('nama_satker', 'Gorontalo') ?>, <?= tgl_indo(date('Y-m-d'), true) ?></p>
            <p>Ketua Pengadilan Agama / Penanggung Jawab Mediasi</p>
            <br><br><br><br>
            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">________________________</p>
            <p style="margin-top: 2px;">NIP. ____________________</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
