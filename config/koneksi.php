<?php
// Koneksi database (PDO) - pengaturan default Laragon
$DB_HOST = 'localhost';
$DB_NAME = 'pengaduan_sekolah';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL Laragon sudah berjalan dan database '
        . 'pengaduan_sekolah sudah diimpor.');
}

// BASE_URL otomatis: aman dipakai di localhost/pengaduan_sekolah maupun pengaduan_sekolah.test
$root = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$dir  = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
define('BASE_URL', ($root !== '' && stripos($dir, $root) === 0) ? substr($dir, strlen($root)) : '');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
