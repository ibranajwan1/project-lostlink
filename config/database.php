<?php
/**
 * Koneksi database LostLink menggunakan PDO.
 * Import database.sql melalui phpMyAdmin terlebih dahulu.
 * Sesuaikan DB_USER dan DB_PASS jika konfigurasi MySQL kamu berbeda.
 */
$host = 'localhost';
$dbname = 'lostlink_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Jangan tampilkan kredensial atau detail server kepada pengunjung.
    error_log('LostLink database connection failed: ' . $e->getMessage());
    $pdo = null;
}
