<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_pengajuan_lengkap');
        DB::unprepared('DROP VIEW IF EXISTS v_penilaian_detail');
        DB::unprepared('DROP VIEW IF EXISTS v_antrian_marketing');
        DB::unprepared('DROP VIEW IF EXISTS v_statistik_bulanan');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_hitung_smart');
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_pengajuan_lengkap AS
SELECT p.id AS pengajuan_id, p.kode_pengajuan, p.status, p.tgl_submitted, p.tgl_selesai,
       u.id AS debitur_id, u.nama_lengkap AS nama_debitur, u.email AS email_debitur, dp.nik, dp.no_hp,
       pr.nama_proyek, tu.nama_tipe, un.kode_unit, p.harga_properti, p.uang_muka, p.persen_dp,
       p.jumlah_pinjaman, p.tenor_tahun, p.estimasi_angsuran, p.rasio_angsuran,
       um.nama_lengkap AS nama_marketing, ua.nama_lengkap AS nama_admin,
       pn.skor_akhir, pn.hasil AS hasil_smart
FROM pengajuan p
JOIN users u ON u.id = p.user_id
LEFT JOIN debitur_pribadi dp ON dp.user_id = p.user_id
JOIN unit un ON un.id = p.unit_id
JOIN tipe_unit tu ON tu.id = un.tipe_unit_id
JOIN proyek pr ON pr.id = tu.proyek_id
LEFT JOIN users um ON um.id = p.marketing_id
LEFT JOIN users ua ON ua.id = p.admin_id
LEFT JOIN penilaian pn ON pn.pengajuan_id = p.id;
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_penilaian_detail AS
SELECT pn.id AS penilaian_id, pn.pengajuan_id, p.kode_pengajuan, u.nama_lengkap AS nama_debitur,
       pn.tgl_penilaian, pn.skor_akhir, pn.threshold, pn.hasil, ua.nama_lengkap AS nama_admin,
       k.kode_kriteria, k.nama_kriteria, k.tipe AS tipe_kriteria, pd.nilai_input,
       pd.nilai_normalisasi, pd.bobot_snapshot, pd.skor_kontribusi
FROM penilaian pn
JOIN pengajuan p ON p.id = pn.pengajuan_id
JOIN users u ON u.id = p.user_id
JOIN users ua ON ua.id = pn.admin_id
JOIN penilaian_detail pd ON pd.penilaian_id = pn.id
JOIN kriteria k ON k.id = pd.kriteria_id
ORDER BY pn.id, k.urutan;
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_antrian_marketing AS
SELECT p.id AS pengajuan_id, p.kode_pengajuan, p.status, p.tgl_submitted, u.nama_lengkap AS nama_debitur,
       dp.no_hp, pr.nama_proyek, tu.nama_tipe, p.jumlah_pinjaman, p.tenor_tahun,
       p.marketing_id, vm.keputusan AS keputusan_marketing, vm.rekomendasi_marketing
FROM pengajuan p
JOIN users u ON u.id = p.user_id
LEFT JOIN debitur_pribadi dp ON dp.user_id = u.id
JOIN unit un ON un.id = p.unit_id
JOIN tipe_unit tu ON tu.id = un.tipe_unit_id
JOIN proyek pr ON pr.id = tu.proyek_id
LEFT JOIN verifikasi_marketing vm ON vm.pengajuan_id = p.id
WHERE p.status IN ('submitted','verifikasi_marketing','revisi_debitur');
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_statistik_bulanan AS
SELECT DATE_FORMAT(p.tgl_submitted, '%Y-%m') AS periode,
       COUNT(*) AS total_pengajuan,
       SUM(p.status = 'disetujui_sistem') AS jumlah_disetujui,
       SUM(p.status IN ('ditolak_sistem','ditolak_marketing')) AS jumlah_ditolak,
       SUM(p.status NOT IN ('disetujui_sistem','ditolak_sistem','ditolak_marketing')) AS jumlah_proses,
       ROUND(AVG(pn.skor_akhir), 2) AS rata_skor_smart,
       ROUND(SUM(p.status = 'disetujui_sistem') / COUNT(*) * 100, 1) AS approval_rate_pct
FROM pengajuan p
LEFT JOIN penilaian pn ON pn.pengajuan_id = p.id
WHERE p.tgl_submitted IS NOT NULL
GROUP BY DATE_FORMAT(p.tgl_submitted, '%Y-%m')
ORDER BY periode DESC;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_hitung_smart(IN p_penilaian_id INT UNSIGNED)
BEGIN
  DECLARE v_skor_total DECIMAL(8,4) DEFAULT 0;
  DECLARE v_hasil VARCHAR(20);
  DECLARE v_threshold DECIMAL(8,4);

  UPDATE penilaian_detail pd
  JOIN kriteria k ON k.id = pd.kriteria_id
  SET pd.nilai_normalisasi = CASE
      WHEN k.tipe = 'benefit' THEN GREATEST(0, LEAST(1, (pd.nilai_input - k.nilai_min) / NULLIF(k.nilai_max - k.nilai_min, 0)))
      WHEN k.tipe = 'cost' THEN GREATEST(0, LEAST(1, (k.nilai_max - pd.nilai_input) / NULLIF(k.nilai_max - k.nilai_min, 0)))
      ELSE 0 END,
      pd.skor_kontribusi = ROUND(CASE
      WHEN k.tipe = 'benefit' THEN GREATEST(0, LEAST(1, (pd.nilai_input - k.nilai_min) / NULLIF(k.nilai_max - k.nilai_min, 0)))
      WHEN k.tipe = 'cost' THEN GREATEST(0, LEAST(1, (k.nilai_max - pd.nilai_input) / NULLIF(k.nilai_max - k.nilai_min, 0)))
      ELSE 0 END * pd.bobot_snapshot * 100, 4)
  WHERE pd.penilaian_id = p_penilaian_id;

  SELECT SUM(skor_kontribusi) INTO v_skor_total FROM penilaian_detail WHERE penilaian_id = p_penilaian_id;
  SELECT threshold INTO v_threshold FROM penilaian WHERE id = p_penilaian_id;
  SET v_hasil = IF(v_skor_total >= v_threshold, 'layak', 'tidak_layak');

  UPDATE penilaian SET skor_akhir = ROUND(v_skor_total, 4), hasil = v_hasil, updated_at = NOW() WHERE id = p_penilaian_id;
  UPDATE pengajuan p
  JOIN penilaian pn ON pn.id = p_penilaian_id AND pn.pengajuan_id = p.id
  SET p.status = IF(v_hasil = 'layak', 'disetujui_sistem', 'ditolak_sistem'), p.tgl_selesai = NOW(), p.updated_at = NOW();

  SELECT v_skor_total AS skor_akhir, v_hasil AS hasil;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_hitung_smart');
        DB::unprepared('DROP VIEW IF EXISTS v_statistik_bulanan');
        DB::unprepared('DROP VIEW IF EXISTS v_antrian_marketing');
        DB::unprepared('DROP VIEW IF EXISTS v_penilaian_detail');
        DB::unprepared('DROP VIEW IF EXISTS v_pengajuan_lengkap');
    }
};
