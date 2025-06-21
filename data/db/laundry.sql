-- Active: 1742043512440@@127.0.0.1@3306@laundry3
CREATE DATABASE laundry3;
USE laundry3;

-- Buat tabel laundry terlebih dahulu
CREATE TABLE laundry (
    laundry_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_laundry VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL
);

-- Tambahkan beberapa data laundry
INSERT INTO laundry (nama_laundry, alamat) VALUES
('Intan Laundry', 'Jalan Pemuda No 26, Boja'),
('UT Laundry', 'Alamat UT Laundry'),
('YNS Laundry', 'Alamat YNS Laundry'),
('Annadif Laundry', 'Alamat Annadif Laundry');

-- Buat tabel user
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100),
    no_hp VARCHAR(15),
    alamat TEXT,
    level ENUM('superadmin', 'admin', 'pengguna') DEFAULT 'pengguna',
    laundry_id INT, -- Superadmin tidak memiliki laundry_id
    FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
);
-- Superadmin (Tidak terikat pada laundry)
INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) VALUES 
('superadmin', 'superadmin', 'Super Admin', '62895328096161', 'Dusun Jurang Brengos, Desa Merbuh', 'superadmin', NULL);

-- Admin Intan Laundry
INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) VALUES 
('intan', 'intan', 'Intan Laundry', '6285740407769', 'Jalan Pemuda No 26, Dusun Jagalan', 'admin', 1);
INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) VALUES
('ut', 'ut', 'ut Laundry', '6285740407769', 'Jalan Pemuda No 26, Dusun Jagalan', 'admin', 2);

-- Pengguna biasa yang menjadi pelanggan Intan Laundry
INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) VALUES 
('joko', 'joko', 'Joko Santoso', '6281234567890', 'Kendal', 'pengguna', 1);


DROP TABLE IF EXISTS pakaian;

CREATE TABLE pakaian (
    pakaian_id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    jenis_paket ENUM('ck', 'cs', 'dc') NOT NULL, -- jenis paket cuci
    id_paket INT NOT NULL,                       -- ID dari tabel ck/cs/dc
    pakaian_jenis VARCHAR(255) NOT NULL,         -- nama pakaian
    pakaian_jumlah INT NOT NULL CHECK (pakaian_jumlah >= 0),
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(transaksi_id) ON DELETE CASCADE
) ENGINE=InnoDB;
ALTER TABLE pakaian
ADD COLUMN pakaian_tarif INT DEFAULT 0 AFTER pakaian_jumlah;

-- Buat tabel pemilik
CREATE TABLE pemilik (
    pemilik_id INT AUTO_INCREMENT PRIMARY KEY,
    pemilik_nama VARCHAR(255) NOT NULL,
    pemilik_hp VARCHAR(20) NOT NULL,
    laundry_id INT NOT NULL, -- Menyimpan ID admin yang menambahkan
    FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
);

-- Buat tabel transaksiw
CREATE TABLE transaksi (
    transaksi_id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_tgl DATE NOT NULL,
    transaksi_pelanggan INT NOT NULL,
    transaksi_berat FLOAT NOT NULL, -- Berat pakaian (dalam Kg)
    transaksi_total_harga INT NOT NULL,
    transaksi_tgl_selesai DATE NOT NULL,
    transaksi_status ENUM('menunggu', 'proses', 'selesai', 'diantar') NOT NULL,
    laundry_id INT NOT NULL, -- Hubungkan transaksi ke laundry tertentu
    FOREIGN KEY (transaksi_pelanggan) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
) ENGINE=InnoDB;
ALTER TABLE transaksi
MODIFY COLUMN transaksi_status ENUM('menunggu', 'proses', 'selesai', 'diantar') NOT NULL DEFAULT 'menunggu';


ALTER TABLE transaksi
DROP COLUMN transaksi_harga_kiloan,
DROP COLUMN transaksi_harga_layanan_tambahan;

-- Tabel tb_cuci_komplit
CREATE TABLE tb_cuci_komplit (
  id_ck INT AUTO_INCREMENT PRIMARY KEY,
  nama_paket_ck VARCHAR(100) NOT NULL,
  waktu_kerja_ck VARCHAR(20) NOT NULL,
  kuantitas_ck INT NOT NULL,
  tarif_ck INT NOT NULL,
  laundry_id INT NOT NULL,
  FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan data ke tb_cuci_komplit
INSERT INTO tb_cuci_komplit (nama_paket_ck, waktu_kerja_ck, kuantitas_ck, tarif_ck, laundry_id) VALUES
('Cuci Komplit Reguler', '2 Hari', 1, 8000, 1),
('Cuci Komplit Kilat', '1 Hari', 1, 15000, 1),
('Cuci Komplit Express', '5 Jam', 1, 20000, 1);
INSERT INTO tb_cuci_komplit (nama_paket_ck, waktu_kerja_ck, kuantitas_ck, tarif_ck, laundry_id) VALUES
('Cuci Komplit Reguler', '2 Hari', 1, 8000, 2),
('Cuci Komplit Kilat', '1 Hari', 1, 15000, 2),
('Cuci Komplit Express', '5 Jam', 1, 20000, 2);
INSERT INTO tb_cuci_komplit (nama_paket_ck, waktu_kerja_ck, kuantitas_ck, tarif_ck, laundry_id) VALUES
('Cuci Komplit Reguler', '2 Hari', 1, 8000, 3),
('Cuci Komplit Kilat', '1 Hari', 1, 15000, 3),
('Cuci Komplit Express', '5 Jam', 1, 20000, 3);

-- Tabel tb_cuci_satuan
CREATE TABLE tb_cuci_satuan (
  id_cs INT AUTO_INCREMENT PRIMARY KEY,
  nama_cs VARCHAR(100) NOT NULL,
  waktu_kerja_cs VARCHAR(20) NOT NULL,
  kuantitas_cs INT NOT NULL,
  tarif_cs INT NOT NULL,
  keterangan_cs TEXT NOT NULL,
  laundry_id INT NOT NULL,
  FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan data ke tb_cuci_satuan
INSERT INTO tb_cuci_satuan (nama_cs, waktu_kerja_cs, kuantitas_cs, tarif_cs, keterangan_cs, laundry_id) VALUES
('Jaket Kulit', '1 Hari', 1, 15000, '', 1),
('Jaket Non Kulit', '1 Hari', 1, 6000, '', 1),
('Boneka Mini', '1 Hari', 1, 3000, '', 1),
('Boneka Kecil', '1 Hari', 1, 6000, '', 1),
('Boneka Sedang', '1 Hari', 1, 10000, '', 1),
('Boneka Besar', '1 Hari', 1, 20000, '', 1),
('Sejadah', '1 Hari', 1, 20000, '', 1),
('Selimut', '1 Hari', 1, 20000, '', 1),
('Keset', '1 Hari', 1, 20000, '', 1);
INSERT INTO tb_cuci_satuan (nama_cs, waktu_kerja_cs, kuantitas_cs, tarif_cs, keterangan_cs, laundry_id) VALUES
('Jaket Kulit', '1 Hari', 1, 15000, '', 3),
('Jaket Non Kulit', '1 Hari', 1, 6000, '', 3),
('Boneka Mini', '1 Hari', 1, 3000, '', 3),
('Boneka Kecil', '1 Hari', 1, 6000, '', 3),
('Boneka Sedang', '1 Hari', 1, 10000, '', 3),
('Boneka Besar', '1 Hari', 1, 20000, '', 3),
('Sejadah', '1 Hari', 1, 20000, '', 3),
('Selimut', '1 Hari', 1, 20000, '', 3),
('Keset', '1 Hari', 1, 20000, '', 3);

-- Tabel tb_dry_clean
CREATE TABLE tb_dry_clean (
  id_dc INT AUTO_INCREMENT PRIMARY KEY,
  nama_paket_dc VARCHAR(100) NOT NULL,
  waktu_kerja_dc VARCHAR(20) NOT NULL,
  kuantitas_dc INT NOT NULL,
  tarif_dc INT NOT NULL,
  laundry_id INT NOT NULL,
  FOREIGN KEY (laundry_id) REFERENCES laundry(laundry_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan data ke tb_dry_clean
INSERT INTO tb_dry_clean (nama_paket_dc, waktu_kerja_dc, kuantitas_dc, tarif_dc, laundry_id) VALUES
('Cuci Kering Reguler', '2 Hari', 1, 6000, 1),
('Cuci Kering Kilat', '1 Hari', 1, 9000, 1),
('Cuci Kering Express', '5 Jam', 1, 15000, 1);
INSERT INTO tb_dry_clean (nama_paket_dc, waktu_kerja_dc, kuantitas_dc, tarif_dc, laundry_id) VALUES
('Cuci Kering Reguler', '2 Hari', 1, 6000, 3),
('Cuci Kering Kilat', '1 Hari', 1, 9000, 3),
('Cuci Kering Express', '5 Jam', 1, 15000, 3);

-- Tabel transaksi_detail
CREATE TABLE transaksi_detail (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    jenis_paket ENUM('ck', 'cs', 'dc') NOT NULL, -- ck = Cuci Komplit, cs = Cuci Satuan, dc = Dry Clean
    id_paket INT NOT NULL, -- ID dari tb_cuci_komplit / tb_cuci_satuan / tb_dry_clean
    jumlah INT NOT NULL DEFAULT 1,
    subtotal INT NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(transaksi_id) ON DELETE CASCADE
);
