CREATE DATABASE IF NOT EXISTS lostlink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lostlink_db;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nim VARCHAR(30) DEFAULT NULL,
  role ENUM('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  judul VARCHAR(150) NOT NULL,
  deskripsi TEXT DEFAULT NULL,
  kategori ENUM('Elektronik','Aksesori','Kartu & Identitas','Buku & Alat Tulis','Pakaian','Lainnya') NOT NULL DEFAULT 'Lainnya',
  tipe ENUM('hilang','temuan') NOT NULL DEFAULT 'temuan',
  lokasi VARCHAR(150) NOT NULL,
  tanggal_kejadian DATE DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  status ENUM('tersedia','proses_klaim','dikembalikan') NOT NULL DEFAULT 'tersedia',
  kontak VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_items_tipe_status (tipe,status),
  INDEX idx_items_kategori (kategori),
  INDEX idx_items_tanggal (tanggal_kejadian)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS claims (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  item_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  nama_pemohon VARCHAR(100) NOT NULL,
  email_pemohon VARCHAR(150) NOT NULL,
  bukti_kepemilikan TEXT NOT NULL,
  status ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
  CONSTRAINT fk_claims_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Contoh data agar halaman Landing langsung menampilkan kartu barang.
INSERT INTO items (judul, deskripsi, kategori, tipe, lokasi, tanggal_kejadian, foto, status, kontak)
SELECT 'Botol minum biru', 'Botol minum warna biru ditemukan di area perpustakaan.', 'Aksesori', 'temuan', 'Perpustakaan Kampus', CURDATE() - INTERVAL 1 DAY, NULL, 'tersedia', NULL
WHERE NOT EXISTS (SELECT 1 FROM items WHERE judul = 'Botol minum biru');

INSERT INTO items (judul, deskripsi, kategori, tipe, lokasi, tanggal_kejadian, foto, status, kontak)
SELECT 'Kartu mahasiswa', 'Kartu mahasiswa ditemukan dekat gedung fakultas.', 'Kartu & Identitas', 'temuan', 'Gedung Fakultas', CURDATE() - INTERVAL 2 DAY, NULL, 'tersedia', NULL
WHERE NOT EXISTS (SELECT 1 FROM items WHERE judul = 'Kartu mahasiswa');

INSERT INTO items (judul, deskripsi, kategori, tipe, lokasi, tanggal_kejadian, foto, status, kontak)
SELECT 'Headphone hitam', 'Headphone hitam tertinggal setelah kegiatan kelas.', 'Elektronik', 'hilang', 'Ruang Kelas B-204', CURDATE() - INTERVAL 3 DAY, NULL, 'tersedia', NULL
WHERE NOT EXISTS (SELECT 1 FROM items WHERE judul = 'Headphone hitam');
