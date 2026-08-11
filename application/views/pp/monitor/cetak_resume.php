<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 30px; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .kop h3 { margin: 2px 0; font-size: 14px; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 2px 0; font-size: 11px; font-style: italic; }
        .title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 20px; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data td { padding: 5px 8px; vertical-align: top; }
        table.border th, table.border td { border: 1px solid #000; padding: 6px 8px; }
        table.border th { background-color: #f0f0f0; text-transform: uppercase; font-size: 10px; }
        .badge { font-weight: bold; text-transform: uppercase; }
        .footer-sig { margin-top: 40px; float: right; width: 250px; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak / Print Lembar Mediasi
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="kop">
        <h2><?= strtoupper(htmlspecialchars(get_app_setting('nama_satker', 'Pengadilan Agama Gorontalo'))) ?></h2>
        <h3>LEMBAR INFORMASI & RESUME MEDIASI PERKARA</h3>
        <p>Aplikasi <?= htmlspecialchars(get_app_setting('nama_aplikasi', 'SIPO-MEDIASI')) ?> — <?= htmlspecialchars(get_app_setting('slogan_aplikasi', 'Sistem Informasi Pengelolaan Mediasi Perkara')) ?></p>
    </div>

    <!-- Data Utama Perkara -->
    <table>
        <tr>
            <td width="150"><strong>Nomor Perkara</strong></td>
            <td width="10">:</td>
            <td><strong><?= htmlspecialchars($perkara->nomor_perkara) ?></strong></td>
            <td width="120"><strong>Status Perkara</strong></td>
            <td width="10">:</td>
            <td><strong class="badge"><?= strtoupper($perkara->status) ?></strong></td>
        </tr>
        <tr>
            <td><strong>Jenis Perkara</strong></td>
            <td>:</td>
            <td><?= htmlspecialchars($perkara->jenis_perkara) ?></td>
            <td><strong>Batas Mediasi</strong></td>
            <td>:</td>
            <td><?= tgl_indo($perkara->tgl_batas_mediasi, false) ?></td>
        </tr>
        <tr>
            <td><strong>Majelis Hakim</strong></td>
            <td>:</td>
            <td><?= htmlspecialchars($perkara->nama_hakim) ?></td>
            <td><strong>Panitera Pengganti</strong></td>
            <td>:</td>
            <td><?= htmlspecialchars($perkara->nama_pp ?: '—') ?></td>
        </tr>
        <tr>
            <td><strong>Mediator Penanggung Jawab</strong></td>
            <td>:</td>
            <td colspan="4"><strong><?= htmlspecialchars($perkara->nama_mediator ?: 'Belum Ditetapkan') ?></strong> (<?= $perkara->jenis_mediator === 'hakim' ? 'Mediator Hakim' : 'Mediator Non-Hakim' ?>)</td>
        </tr>
    </table>

    <!-- Data Para Pihak -->
    <h4 style="margin-bottom: 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px;">I. PARA PIHAK BERPERKARA</h4>
    <table class="border">
        <thead>
            <tr>
                <th width="30">#</th>
                <th width="120">Kedudukan</th>
                <th>Nama Lengkap Pihak</th>
                <th>Kuasa Hukum</th>
                <th>No. Telp / WA</th>
                <th>Email Notifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pihak)): ?>
            <tr><td colspan="6" align="center">Data pihak belum diinput.</td></tr>
            <?php else: ?>
            <?php $no=1; foreach ($pihak as $p): ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td align="center" style="text-transform: capitalize; font-weight: bold;"><?= str_replace('_', ' ', $p->jenis) ?></td>
                <td><strong><?= htmlspecialchars($p->nama) ?></strong></td>
                <td><?= htmlspecialchars($p->kuasa_hukum ?: '—') ?></td>
                <td><?= htmlspecialchars($p->no_hp ?: '—') ?></td>
                <td><?= htmlspecialchars($p->email ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Riwayat Sesi Mediasi -->
    <h4 style="margin-top: 20px; margin-bottom: 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px;">II. RIWAYAT SESI MEDIASI</h4>
    <table class="border">
        <thead>
            <tr>
                <th width="30">Sesi</th>
                <th>Hari & Tanggal</th>
                <th>Waktu Mediasi</th>
                <th>Tempat / Ruangan</th>
                <th>Catatan Mediator</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jadwal)): ?>
            <tr><td colspan="5" align="center">Belum ada sesi mediasi yang dijadwalkan.</td></tr>
            <?php else: ?>
            <?php $no=1; foreach ($jadwal as $j): ?>
            <tr>
                <td align="center">#<?= $no++ ?></td>
                <td><strong><?= tgl_indo($j->tgl_mediasi, true) ?></strong></td>
                <td align="center"><?= substr($j->jam_mulai,0,5) ?> – <?= substr($j->jam_selesai,0,5) ?> WITA</td>
                <td><?= htmlspecialchars($j->nama_ruangan ?: $j->tempat_lain ?: '—') ?></td>
                <td><?= htmlspecialchars($j->keterangan ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Hasil Akhir Mediasi -->
    <?php if ($hasil): ?>
    <h4 style="margin-top: 20px; margin-bottom: 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px;">III. HASIL AKHIR MEDIASI</h4>
    <table class="data" style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
        <tr>
            <td width="150"><strong>Hasil Mediasi</strong></td>
            <td width="10">:</td>
            <td><strong style="font-size: 13px; text-transform: uppercase; color: #000;"><?= str_replace('_', ' ', $hasil->status_hasil) ?></strong></td>
        </tr>
        <tr>
            <td><strong>Tanggal Selesai</strong></td>
            <td>:</td>
            <td><?= tgl_indo($hasil->tgl_hasil, true) ?></td>
        </tr>
        <?php if ($hasil->catatan): ?>
        <tr>
            <td><strong>Catatan Akhir Mediator</strong></td>
            <td>:</td>
            <td><em>"<?= nl2br(htmlspecialchars($hasil->catatan)) ?>"</em></td>
        </tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <!-- Tanda Tangan -->
    <div class="footer-sig">
        <p>Gorontalo, <?= tgl_indo(date('Y-m-d'), false) ?></p>
        <p>Mediator Penanggung Jawab,</p>
        <br><br><br>
        <p><strong><u><?= htmlspecialchars($perkara->nama_mediator ?: '.........................................') ?></u></strong></p>
    </div>

</body>
</html>
