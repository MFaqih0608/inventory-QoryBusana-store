-- Buat database
CREATE DATABASE IF NOT EXISTS toko_inventory;
USE toko_inventory;

-- Buat tabel barang
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga DECIMAL(15,0) NOT NULL,
    stok INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data contoh
INSERT INTO barang (nama_barang, kategori, harga, stok) VALUES
('Kemeja Flannel', 'Atasan', 150000, 25),
('Celana Jeans Slim', 'Bawahan', 220000, 12),
('Dress Floral', 'Dress', 185000, 8),
('Jaket Denim', 'Outerwear', 350000, 3),
('Kaos Polos', 'Atasan', 75000, 0),
('Rok Mini', 'Bawahan', 120000, 15);
