-- ============================================================
--  Database  : pengaduan_sekolah
--  Aplikasi  : Pengaduan Sarana Sekolah (PHP Native + MySQL)
--  Cara impor: phpMyAdmin / HeidiSQL (Laragon) -> Import file ini
--              (database akan dibuat otomatis)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `pengaduan_sekolah`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `pengaduan_sekolah`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `riwayat_status`;
DROP TABLE IF EXISTS `umpan_balik`;
DROP TABLE IF EXISTS `pengaduan`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Tabel users : akun murid dan admin
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id_user`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100) NOT NULL,
  `username`   VARCHAR(50)  NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('murid','admin') NOT NULL DEFAULT 'murid',
  `kelas`      VARCHAR(20)  DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel kategori : jenis sarana yang diadukan
-- ------------------------------------------------------------
CREATE TABLE `kategori` (
  `id_kategori`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uq_nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel pengaduan : data aduan dari murid
-- Status : Menunggu -> Diproses -> Selesai (atau Ditolak)
-- ------------------------------------------------------------
CREATE TABLE `pengaduan` (
  `id_pengaduan`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user`           INT UNSIGNED NOT NULL,
  `id_kategori`       INT UNSIGNED NOT NULL,
  `judul`             VARCHAR(150) NOT NULL,
  `lokasi`            VARCHAR(100) NOT NULL,
  `deskripsi`         TEXT NOT NULL,
  `foto`              VARCHAR(255) DEFAULT NULL,
  `status`            ENUM('Menunggu','Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `tanggal_pengaduan` DATE NOT NULL,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengaduan`),
  KEY `idx_tanggal` (`tanggal_pengaduan`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_pengaduan_user` FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pengaduan_kategori` FOREIGN KEY (`id_kategori`)
    REFERENCES `kategori` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel umpan_balik : tanggapan admin untuk sebuah pengaduan
-- ------------------------------------------------------------
CREATE TABLE `umpan_balik` (
  `id_umpan_balik` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pengaduan`   INT UNSIGNED NOT NULL,
  `id_admin`       INT UNSIGNED NOT NULL,
  `isi`            TEXT NOT NULL,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_umpan_balik`),
  CONSTRAINT `fk_umpan_pengaduan` FOREIGN KEY (`id_pengaduan`)
    REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_umpan_admin` FOREIGN KEY (`id_admin`)
    REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabel riwayat_status : jejak setiap perubahan status
-- ------------------------------------------------------------
CREATE TABLE `riwayat_status` (
  `id_riwayat`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pengaduan` INT UNSIGNED NOT NULL,
  `id_admin`     INT UNSIGNED DEFAULT NULL,
  `status_lama`  VARCHAR(20) DEFAULT NULL,
  `status_baru`  VARCHAR(20) NOT NULL,
  `catatan`      VARCHAR(255) DEFAULT NULL,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat`),
  CONSTRAINT `fk_riwayat_pengaduan` FOREIGN KEY (`id_pengaduan`)
    REFERENCES `pengaduan` (`id_pengaduan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_riwayat_admin` FOREIGN KEY (`id_admin`)
    REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  DATA AWAL
--  Admin : username admin  / password admin123
--  Murid : username budi, sari, rizky / password murid123
-- ============================================================
INSERT INTO `users` (`id_user`,`nama`,`username`,`email`,`password`,`role`,`kelas`) VALUES
(1,'Administrator','admin','admin@sekolah.sch.id','$2y$12$B7kxBuJkfTU6ZsGnLuhGdu5Z1G1vNo76poA0WILiDXsWmIvkGnpgG','admin',NULL),
(2,'Budi Santoso','budi','budi@sekolah.sch.id','$2y$12$GDYhUWtUIzdKWzdfg39DNujRNfe/r7v9c6coNif2Nlsp16J/G1Rp.','murid','XII RPL 1'),
(3,'Sari Wulandari','sari','sari@sekolah.sch.id','$2y$12$GDYhUWtUIzdKWzdfg39DNujRNfe/r7v9c6coNif2Nlsp16J/G1Rp.','murid','XII RPL 2'),
(4,'Rizky Pratama','rizky','rizky@sekolah.sch.id','$2y$12$GDYhUWtUIzdKWzdfg39DNujRNfe/r7v9c6coNif2Nlsp16J/G1Rp.','murid','XI TKJ 1');

INSERT INTO `kategori` (`id_kategori`,`nama_kategori`) VALUES
(1,'Ruang Kelas'),
(2,'Laboratorium'),
(3,'Toilet'),
(4,'Perpustakaan'),
(5,'Lapangan & Olahraga'),
(6,'Kantin'),
(7,'Listrik & Air'),
(8,'Lainnya');

INSERT INTO `pengaduan`
(`id_pengaduan`,`id_user`,`id_kategori`,`judul`,`lokasi`,`deskripsi`,`foto`,`status`,`tanggal_pengaduan`) VALUES
(1,2,1,'AC kelas tidak dingin','Ruang XII RPL 1','AC di kelas sudah beberapa hari tidak dingin sehingga suasana belajar menjadi panas.',NULL,'Diproses',CURDATE() - INTERVAL 6 DAY),
(2,2,3,'Keran toilet bocor','Toilet lantai 2','Keran wastafel di toilet lantai 2 bocor dan air terus menetes.',NULL,'Selesai',CURDATE() - INTERVAL 20 DAY),
(3,3,2,'Komputer lab tidak menyala','Lab Komputer 1','Tiga unit komputer di Lab Komputer 1 tidak dapat menyala.',NULL,'Menunggu',CURDATE() - INTERVAL 2 DAY),
(4,4,5,'Ring basket patah','Lapangan Basket','Ring basket sebelah utara patah dan berbahaya saat dipakai.',NULL,'Ditolak',CURDATE() - INTERVAL 35 DAY);

INSERT INTO `riwayat_status` (`id_pengaduan`,`id_admin`,`status_lama`,`status_baru`,`catatan`) VALUES
(1,NULL,NULL,'Menunggu','Pengaduan dibuat'),
(1,1,'Menunggu','Diproses','Teknisi dijadwalkan'),
(2,NULL,NULL,'Menunggu','Pengaduan dibuat'),
(2,1,'Menunggu','Diproses','Sedang diperbaiki'),
(2,1,'Diproses','Selesai','Keran sudah diganti'),
(3,NULL,NULL,'Menunggu','Pengaduan dibuat'),
(4,NULL,NULL,'Menunggu','Pengaduan dibuat'),
(4,1,'Menunggu','Ditolak','Sudah ada rencana penggantian pada anggaran berikutnya');

INSERT INTO `umpan_balik` (`id_pengaduan`,`id_admin`,`isi`) VALUES
(1,1,'Terima kasih laporannya. Teknisi AC akan datang minggu ini.'),
(2,1,'Keran sudah diganti. Mohon dilaporkan kembali jika masih bermasalah.'),
(4,1,'Ring basket akan diganti pada anggaran berikutnya. Sementara area ditutup.');
