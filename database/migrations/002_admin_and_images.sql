-- Untuk database yang sudah dibuat dari schema.sql versi lama.
ALTER TABLE games ADD COLUMN image VARCHAR(255) NULL AFTER color;

CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  name          VARCHAR(80)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  last_login_at DATETIME     NULL,
  created_at    DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS login_attempts (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip         VARCHAR(45) NOT NULL,
  created_at DATETIME    NOT NULL,
  KEY idx_attempts (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
