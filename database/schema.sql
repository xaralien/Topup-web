-- MYTopup – skema MySQL/MariaDB (utf8mb4). Impor: mysql -u root -p mytopup < database/schema.sql
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(80)  NOT NULL,
  email         VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  last_login_at DATETIME     NULL,
  created_at    DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS games (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(80)  NOT NULL UNIQUE,
  name        VARCHAR(120) NOT NULL,
  category    VARCHAR(40)  NOT NULL,
  currency    VARCHAR(60)  NOT NULL,
  color       VARCHAR(9)   NOT NULL DEFAULT '#E2E8F0',
  image       VARCHAR(255) NULL,                 -- path relatif, mis. assets/uploads/games/abc.png
  is_hot      TINYINT(1)   NOT NULL DEFAULT 0,
  needs_zone  TINYINT(1)   NOT NULL DEFAULT 0,   -- 1 = butuh Server/Zone ID (mis. Mobile Legends)
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order  INT          NOT NULL DEFAULT 0,
  KEY idx_games_cat (category, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS packs (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id     INT UNSIGNED NOT NULL,
  qty         VARCHAR(20)  NOT NULL,              -- jumlah item, mis. "86"
  price       INT UNSIGNED NOT NULL,              -- harga jual (Rupiah)
  sku         VARCHAR(60)  NULL,                  -- kode produk di supplier (Digiflazz/VIP Reseller, dst.)
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order  INT          NOT NULL DEFAULT 0,
  CONSTRAINT fk_packs_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
  KEY idx_packs_game (game_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no        VARCHAR(24)  NOT NULL UNIQUE,   -- = merchantOrderId di Duitku
  user_id         INT UNSIGNED NULL,              -- NULL = checkout sebagai tamu
  game_id         INT UNSIGNED NOT NULL,
  pack_id         INT UNSIGNED NOT NULL,
  game_name       VARCHAR(120) NOT NULL,          -- snapshot saat order
  item_label      VARCHAR(160) NOT NULL,
  sku             VARCHAR(60)  NULL,
  target_user_id  VARCHAR(60)  NOT NULL,          -- ID akun game pembeli
  target_zone_id  VARCHAR(20)  NULL,
  email           VARCHAR(160) NOT NULL,
  whatsapp        VARCHAR(20)  NULL,
  amount          INT UNSIGNED NOT NULL,          -- harga snapshot (Rupiah)
  payment_method  VARCHAR(10)  NOT NULL,          -- kode Duitku (SP, DA, BC, ...)
  payment_name    VARCHAR(60)  NOT NULL,
  status          ENUM('pending','paid','processing','success','failed','expired') NOT NULL DEFAULT 'pending',
  duitku_ref      VARCHAR(60)  NULL,
  payment_url     VARCHAR(500) NULL,
  va_number       VARCHAR(60)  NULL,
  qr_string       TEXT         NULL,
  expires_at      DATETIME     NOT NULL,
  paid_at         DATETIME     NULL,
  fulfilled_at    DATETIME     NULL,
  last_checked_at DATETIME     NULL,
  created_at      DATETIME     NOT NULL,
  updated_at      DATETIME     NOT NULL,
  CONSTRAINT fk_orders_game FOREIGN KEY (game_id) REFERENCES games(id),
  CONSTRAINT fk_orders_pack FOREIGN KEY (pack_id) REFERENCES packs(id),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_orders_user (user_id),
  KEY idx_orders_status (status, expires_at),
  KEY idx_orders_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payment_logs (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no   VARCHAR(24)  NULL,
  type       VARCHAR(30)  NOT NULL,               -- inquiry | callback | status_check | fulfill
  payload    MEDIUMTEXT   NULL,
  created_at DATETIME     NOT NULL,
  KEY idx_logs_order (order_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
