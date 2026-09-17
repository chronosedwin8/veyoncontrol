-- Veyon Control - esquema de base de datos (MySQL 8, utf8mb4)
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  skey       VARCHAR(64) NOT NULL PRIMARY KEY,
  svalue     TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name                 VARCHAR(120) NOT NULL,
  email                VARCHAR(190) NOT NULL,
  password_hash        VARCHAR(255) NOT NULL,
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  active               TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at        DATETIME NULL,
  created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clients (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  institution   VARCHAR(190) NOT NULL,
  tax_id        VARCHAR(40)  NULL,
  contact_name  VARCHAR(150) NULL,
  position      VARCHAR(120) NULL,
  email         VARCHAR(190) NOT NULL,
  phone         VARCHAR(40)  NULL,
  address       VARCHAR(255) NULL,
  city          VARCHAR(100) NULL,
  country       VARCHAR(80)  NOT NULL DEFAULT 'Colombia',
  password_hash VARCHAR(255) NULL,
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
  notes         TEXT NULL,
  last_login_at DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_clients_email (email),
  KEY ix_clients_institution (institution),
  KEY ix_clients_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plans (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug            VARCHAR(60)  NOT NULL,
  name            VARCHAR(120) NOT NULL,
  capacity_label  VARCHAR(80)  NULL,
  subtitle        VARCHAR(255) NULL,
  price_cop       DECIMAL(14,2) NOT NULL DEFAULT 0,
  period_label    VARCHAR(80)  NULL,
  features        TEXT NULL,
  compare_values  TEXT NULL,
  featured        TINYINT(1) NOT NULL DEFAULT 0,
  ribbon          VARCHAR(40)  NULL,
  online_purchase TINYINT(1) NOT NULL DEFAULT 1,
  active          TINYINT(1) NOT NULL DEFAULT 1,
  sort_order      INT NOT NULL DEFAULT 0,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_plans_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS addon_prices (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  org_type     VARCHAR(150) NOT NULL,
  single_price DECIMAL(14,2) NULL,
  bundle_price DECIMAL(14,2) NULL,
  includes     VARCHAR(190) NULL,
  custom_quote TINYINT(1) NOT NULL DEFAULT 0,
  sort_order   INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS counters (
  name  VARCHAR(32) NOT NULL PRIMARY KEY,
  value INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quotes (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  number      VARCHAR(30) NOT NULL,
  client_id   INT UNSIGNED NOT NULL,
  status      ENUM('draft','sent','accepted','rejected','expired','invoiced') NOT NULL DEFAULT 'draft',
  issue_date  DATE NOT NULL,
  valid_until DATE NULL,
  subtotal    DECIMAL(14,2) NOT NULL DEFAULT 0,
  discount    DECIMAL(14,2) NOT NULL DEFAULT 0,
  tax_rate    DECIMAL(5,2)  NOT NULL DEFAULT 0,
  tax_amount  DECIMAL(14,2) NOT NULL DEFAULT 0,
  total       DECIMAL(14,2) NOT NULL DEFAULT 0,
  notes       TEXT NULL,
  invoice_id  INT UNSIGNED NULL,
  created_by  INT UNSIGNED NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_quotes_number (number),
  KEY ix_quotes_client (client_id, status),
  CONSTRAINT fk_quotes_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_items (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  quote_id    INT UNSIGNED NOT NULL,
  plan_id     INT UNSIGNED NULL,
  description VARCHAR(255) NOT NULL,
  quantity    DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit_price  DECIMAL(14,2) NOT NULL DEFAULT 0,
  line_total  DECIMAL(14,2) NOT NULL DEFAULT 0,
  sort_order  INT NOT NULL DEFAULT 0,
  KEY ix_quote_items_quote (quote_id),
  CONSTRAINT fk_quote_items_quote FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  number           VARCHAR(30) NOT NULL,
  client_id        INT UNSIGNED NOT NULL,
  quote_id         INT UNSIGNED NULL,
  status           ENUM('draft','pending','partial','paid','cancelled') NOT NULL DEFAULT 'pending',
  issue_date       DATE NOT NULL,
  due_date         DATE NULL,
  subtotal         DECIMAL(14,2) NOT NULL DEFAULT 0,
  discount         DECIMAL(14,2) NOT NULL DEFAULT 0,
  tax_rate         DECIMAL(5,2)  NOT NULL DEFAULT 0,
  tax_amount       DECIMAL(14,2) NOT NULL DEFAULT 0,
  total            DECIMAL(14,2) NOT NULL DEFAULT 0,
  amount_paid      DECIMAL(14,2) NOT NULL DEFAULT 0,
  notes            TEXT NULL,
  mp_preference_id VARCHAR(80) NULL,
  paid_at          DATETIME NULL,
  created_by       INT UNSIGNED NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_invoices_number (number),
  KEY ix_invoices_client (client_id, status),
  KEY ix_invoices_due (status, due_date),
  CONSTRAINT fk_invoices_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice_items (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  invoice_id  INT UNSIGNED NOT NULL,
  plan_id     INT UNSIGNED NULL,
  description VARCHAR(255) NOT NULL,
  quantity    DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit_price  DECIMAL(14,2) NOT NULL DEFAULT 0,
  line_total  DECIMAL(14,2) NOT NULL DEFAULT 0,
  sort_order  INT NOT NULL DEFAULT 0,
  KEY ix_invoice_items_invoice (invoice_id),
  CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  client_id        INT UNSIGNED NOT NULL,
  invoice_id       INT UNSIGNED NULL,
  method           ENUM('mercadopago','transferencia','consignacion','efectivo','tarjeta','otro') NOT NULL DEFAULT 'otro',
  amount           DECIMAL(14,2) NOT NULL,
  currency         CHAR(3) NOT NULL DEFAULT 'COP',
  status           ENUM('pending','in_process','approved','rejected','cancelled','refunded','charged_back') NOT NULL DEFAULT 'approved',
  reference        VARCHAR(120) NULL,
  mp_payment_id    VARCHAR(40) NULL,
  mp_status_detail VARCHAR(80) NULL,
  mp_payment_type  VARCHAR(40) NULL,
  paid_at          DATETIME NULL,
  notes            TEXT NULL,
  raw_response     MEDIUMTEXT NULL,
  created_by       INT UNSIGNED NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payments_mp (mp_payment_id),
  KEY ix_payments_client (client_id, paid_at),
  KEY ix_payments_invoice (invoice_id, status),
  CONSTRAINT fk_payments_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
  CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS licenses (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  client_id       INT UNSIGNED NOT NULL,
  plan_id         INT UNSIGNED NULL,
  invoice_id      INT UNSIGNED NULL,
  invoice_item_id INT UNSIGNED NULL,
  description     VARCHAR(190) NOT NULL,
  start_date      DATE NOT NULL,
  end_date        DATE NOT NULL,
  status          ENUM('active','cancelled') NOT NULL DEFAULT 'active',
  notes           VARCHAR(255) NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_licenses_item (invoice_item_id),
  KEY ix_licenses_client (client_id, end_date),
  CONSTRAINT fk_licenses_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  institution  VARCHAR(190) NOT NULL,
  contact_name VARCHAR(150) NOT NULL,
  position     VARCHAR(120) NULL,
  email        VARCHAR(190) NOT NULL,
  phone        VARCHAR(40)  NULL,
  equipment    VARCHAR(80)  NULL,
  os           VARCHAR(40)  NULL,
  message      TEXT NULL,
  status       ENUM('new','contacted','converted','closed') NOT NULL DEFAULT 'new',
  client_id    INT UNSIGNED NULL,
  ip           VARCHAR(45) NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_leads_status (status, created_at),
  KEY ix_leads_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_log (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  actor_type ENUM('admin','client','system') NOT NULL,
  actor_id   INT UNSIGNED NULL,
  action     VARCHAR(60) NOT NULL,
  entity     VARCHAR(40) NULL,
  entity_id  INT UNSIGNED NULL,
  details    VARCHAR(500) NULL,
  ip         VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_activity_entity (entity, entity_id),
  KEY ix_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS webhook_events (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  provider     VARCHAR(20) NOT NULL,
  topic        VARCHAR(40) NULL,
  resource_id  VARCHAR(60) NULL,
  request_id   VARCHAR(80) NULL,
  signature_ok TINYINT(1) NULL,
  payload      MEDIUMTEXT NULL,
  processed    TINYINT(1) NOT NULL DEFAULT 0,
  error        VARCHAR(255) NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_webhook_resource (provider, resource_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_type  ENUM('admin','client') NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at    DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_password_resets_token (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  scope      VARCHAR(10) NOT NULL,
  identifier VARCHAR(190) NOT NULL,
  ip         VARCHAR(45) NOT NULL,
  success    TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_login_identifier (scope, identifier, created_at),
  KEY ix_login_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
