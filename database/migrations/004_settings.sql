-- Pengaturan toko (Duitku, WhatsApp, dll.) yang diubah lewat Admin > Pengaturan.
CREATE TABLE IF NOT EXISTS settings (
  k          VARCHAR(60)  NOT NULL PRIMARY KEY,
  v          MEDIUMTEXT   NULL,
  is_secret  TINYINT(1)   NOT NULL DEFAULT 0,
  updated_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
