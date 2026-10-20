
<?php
// Memulai session untuk menyimpan informasi login
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Mengamankan teks sebelum ditampilkan di HTML
function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Memeriksa apakah pengguna sudah login
function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['role'])
        && in_array(
            $_SESSION['role'],
            ['admin', 'murid'],
            true
        );
}

// Menentukan halaman berdasarkan role pengguna
function dashboard_url(string $role): string
{
    if ($role === 'admin') {
        return '/pengaduan_sekolah/admin/index.php';
    }

    if ($role === 'murid') {
        return '/pengaduan_sekolah/murid/index.php';
    }

    return '/pengaduan_sekolah/login.php';
}

// Mengarahkan pengguna ke halaman lain
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Memastikan pengguna sudah login
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/pengaduan_sekolah/login.php');
    }
}

// Membatasi halaman hanya untuk role tertentu
function require_role(string $role): void
{
    require_login();

    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('403 - Anda tidak memiliki izin mengakses halaman ini.');
    }
}

// Membuat token keamanan untuk formulir
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

// Memeriksa token keamanan formulir
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token)
        || empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Token keamanan tidak valid. Muat ulang halaman.');
    }
}
