-- RMS keeps using MYSQL_DATABASE ("donpulpo") as the cloud BackOffice schema.
-- POS gets its own separate schema to simulate a branch's local database
-- (no more sharing tables directly with RMS).
CREATE DATABASE IF NOT EXISTS donpulpo_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON donpulpo_pos.* TO 'gs'@'%';
FLUSH PRIVILEGES;
