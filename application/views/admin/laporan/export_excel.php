<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mediasi <?= bulan_indo($bulan) ?> <?= $tahun ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .subtitle { font-size: 12px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="title">LAPORAN REKAPITULASI PELAKSANAAN MEDIASI PERKARA</div>
    <div class="subtitle"><?= strtoupper(get_app_setting('nama_satker', 'PENGADILAN AGAMA GORONTALO')) ?><br>PERIODE: <?= strtoupper(bulan_indo($bulan)) ?> <?= $tahun ?></div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">NO</th>
                <th>NOMOR PERKARA</th>
                <th>JENIS PERKARA</th>
                <th>MAJELIS HAKIM</th>
                <th>MEDIATOR DITUGASKAN</th>
                <th>BATAS MEDIASI</th>
                <th>STATUS / HASIL MEDIASI</th>
                <th>TANGGAL LAPORAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($perkaras)): ?>
            <tr><td colspan="8" style="text-align: center;">Tidak ada data perkara.</td></tr>
            <?php else: ?>
            <?php $no = 1; foreach ($perkaras as $p): ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td><?= htmlspecialchars($p->nomor_perkara) ?></td>
                <td><?= htmlspecialchars($p->jenis_perkara) ?></td>
                <td><?= htmlspecialchars($p->majelis_hakim ?? $p->nama_hakim ?? '—') ?></td>
                <td><?= htmlspecialchars($p->nama_mediator ?: '—') ?></td>
                <td><?= date('d/m/Y', strtotime($p->tgl_batas_mediasi)) ?></td>
                <td>
                    <?php
                    if ($p->status === 'selesai') {
                        if ($p->hasil === 'berhasil') echo 'Berhasil (Damai)';
                        elseif ($p->hasil === 'berhasil_sebagian') echo 'Berhasil Sebagian';
                        else echo 'Tidak Berhasil';
                    } elseif ($p->status === 'proses') {
                        echo 'Dalam Proses Mediasi';
                    } else {
                        echo 'Menunggu Jadwal Sesi 1';
                    }
                    ?>
                </td>
                <td><?= $p->tgl_laporan ? date('d/m/Y', strtotime($p->tgl_laporan)) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
