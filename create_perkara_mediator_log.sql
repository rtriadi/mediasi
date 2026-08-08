CREATE TABLE IF NOT EXISTS perkara_mediator_log (
  id           INT(11) NOT NULL AUTO_INCREMENT,
  perkara_id   INT(11) NOT NULL,
  mediator_id  INT(11) NOT NULL,
  assigned_by  INT(11) NOT NULL,
  tgl_assign   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tgl_diganti  DATETIME DEFAULT NULL COMMENT 'Kapan mediator ini digantikan. NULL = masih aktif',
  diganti_oleh INT(11) DEFAULT NULL COMMENT 'User ID PP/Admin yang mengganti',
  PRIMARY KEY (id),
  KEY idx_log_perkara (perkara_id),
  KEY idx_log_mediator (mediator_id),
  CONSTRAINT pml_perkara_fk   FOREIGN KEY (perkara_id)   REFERENCES perkara   (id) ON DELETE CASCADE,
  CONSTRAINT pml_mediator_fk  FOREIGN KEY (mediator_id)  REFERENCES mediators (id) ON DELETE CASCADE,
  CONSTRAINT pml_assigned_fk  FOREIGN KEY (assigned_by)  REFERENCES users     (id) ON DELETE CASCADE,
  CONSTRAINT pml_diganti_fk   FOREIGN KEY (diganti_oleh) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Riwayat penugasan mediator per perkara';

-- Seed data awal dari perkara_mediator ke perkara_mediator_log jika belum ada
INSERT INTO perkara_mediator_log (perkara_id, mediator_id, assigned_by, tgl_assign)
SELECT pm.perkara_id, pm.mediator_id, pm.assigned_by, pm.tgl_assign
FROM perkara_mediator pm
LEFT JOIN perkara_mediator_log pml ON pml.perkara_id = pm.perkara_id AND pml.mediator_id = pm.mediator_id
WHERE pml.id IS NULL;
