-- ============================================================
-- SEED: jenis_perkara
-- Dasar Hukum: PERMA No. 1 Tahun 2016 tentang Prosedur Mediasi
--              di Pengadilan, dan Kompetensi Absolut Pengadilan Agama
--              (UU No. 7 Tahun 1989 jo. UU No. 3 Tahun 2006 jo.
--               UU No. 50 Tahun 2009).
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM jenis_perkara;
ALTER TABLE jenis_perkara AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO jenis_perkara (nama, keterangan, is_active) VALUES

-- ── PERKAWINAN & PERCERAIAN ─────────────────────────────────
('Cerai Gugat',
 'Gugatan cerai yang diajukan istri (penggugat) kepada suami (tergugat). Wajib menempuh mediasi berdasarkan PERMA No. 1 Tahun 2016. Mediasi bertujuan mengupayakan perdamaian / ishlah agar perkawinan dapat dipertahankan.',
 1),

('Cerai Talak',
 'Permohonan ikrar talak yang diajukan suami (pemohon) kepada istri (termohon). Wajib mediasi sesuai PERMA No. 1 Tahun 2016. Mediasi bertujuan memfasilitasi rujuk dan perdamaian sebelum sidang pokok perkara.',
 1),

('Hak Asuh Anak (Hadhanah)',
 'Sengketa hak pemeliharaan dan pengasuhan anak (hadhanah) pasca perceraian. Termasuk gugatan perubahan hak asuh. Wajib mediasi; kesepakatan dapat dituangkan dalam akta perdamaian (akta dading).',
 1),

('Nafkah Iddah, Mut''ah, dan Nafkah Anak',
 'Sengketa kewajiban nafkah pasca perceraian, meliputi: nafkah selama masa iddah, mut''ah (pemberian perpisahan kepada bekas istri), dan nafkah anak. Wajib mediasi sesuai PERMA No. 1 Tahun 2016.',
 1),

('Harta Bersama (Gono-Gini)',
 'Sengketa pembagian harta bersama (matrimonial property / harta gono-gini) dalam perkawinan, baik yang diajukan bersama perceraian maupun berdiri sendiri sebagai perkara terpisah. Wajib mediasi.',
 1),

('Poligami',
 'Permohonan izin beristri lebih dari satu (poligami). Jika ada pihak yang keberatan dan perkara bersifat kontensius, wajib menempuh proses mediasi sesuai PERMA No. 1 Tahun 2016.',
 1),

('Pengesahan / Penetapan Pernikahan (Isbat Nikah)',
 'Permohonan pengesahan pernikahan yang tidak tercatat secara resmi di Kantor Urusan Agama. Apabila ada pihak yang keberatan (kontensius), wajib ditempuh mediasi sebelum pemeriksaan pokok perkara.',
 1),

-- ── KEWARISAN & HARTA KEAGAMAAN ────────────────────────────
('Kewarisan Islam',
 'Sengketa pembagian harta warisan (tirkah) berdasarkan hukum Islam / Kompilasi Hukum Islam. Wajib mediasi; kesepakatan pembagian waris dapat dikuatkan melalui akta perdamaian yang berkekuatan hukum tetap.',
 1),

('Hibah',
 'Sengketa keabsahan atau pelaksanaan pemberian harta secara sukarela (hibah). Wajib mediasi sesuai PERMA No. 1 Tahun 2016. Mediasi bertujuan mencapai kesepakatan damai antar pihak.',
 1),

('Wasiat',
 'Sengketa pelaksanaan atau keabsahan wasiat (pesan akhir mengenai harta). Wajib mediasi sebagai sengketa perdata di lingkungan Pengadilan Agama.',
 1),

('Wakaf',
 'Sengketa peruntukan, pengelolaan, peralihan fungsi, atau keabsahan harta wakaf. Wajib mediasi sesuai PERMA No. 1 Tahun 2016; mediasi bertujuan menyelesaikan sengketa wakaf secara damai tanpa putusan pemaksa.',
 1),

('Zakat / Infak / Sedekah',
 'Sengketa pengelolaan atau penyaluran zakat, infak, dan sedekah (ZIS). Merupakan kompetensi absolut Pengadilan Agama berdasarkan UU No. 3 Tahun 2006. Wajib mediasi.',
 1),

-- ── EKONOMI SYARIAH ─────────────────────────────────────────
('Sengketa Perbankan Syariah',
 'Sengketa antara nasabah dan bank / lembaga keuangan syariah atas akad-akad pembiayaan, seperti: murabahah, mudharabah, musyarakah, ijarah, istishna, salam. Merupakan kompetensi Pengadilan Agama sejak UU No. 3 Tahun 2006. Wajib mediasi.',
 1),

('Sengketa Asuransi Syariah (Takaful)',
 'Sengketa klaim atau pelaksanaan perjanjian asuransi berbasis syariah (takaful). Termasuk kompetensi Pengadilan Agama. Wajib mediasi sesuai PERMA No. 1 Tahun 2016.',
 1),

('Sengketa Ekonomi Syariah Lainnya',
 'Sengketa akad / kontrak bisnis berdasarkan prinsip syariah yang tidak masuk kategori di atas, termasuk: sukuk, reksa dana syariah, pegadaian syariah, koperasi syariah, dan lembaga keuangan mikro syariah. Wajib mediasi.',
 1),

-- ── PERKARA KHUSUS ──────────────────────────────────────────
('Perlawanan (Verzet)',
 'Perlawanan atas putusan verstek (verzet), perlawanan pihak berperkara (partij verzet), maupun perlawanan pihak ketiga (derden verzet) terhadap pelaksanaan putusan yang telah berkekuatan hukum tetap. Secara eksplisit diwajibkan mediasi oleh PERMA No. 1 Tahun 2016 Pasal 4 ayat (1) huruf d.',
 1);
