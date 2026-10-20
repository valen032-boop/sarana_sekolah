<?php
// Helper session, hak akses, dan fungsi umum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/koneksi.php';

const STATUS_LIST = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

function e($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

function redirect(string $path): void {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function isLogin(): bool { return isset($_SESSION['user']); }
function user(): ?array  { return $_SESSION['user'] ?? null; }

// Batasi halaman berdasarkan role
function wajibRole(string $role): void {
    if (!isLogin()) redirect('login.php');
    if ($_SESSION['user']['role'] !== $role) {
        redirect($_SESSION['user']['role'] === 'admin' ? 'admin/dashboard.php' : 'murid/dashboard.php');
    }
}

function setFlash(string $tipe, string $pesan): void { $_SESSION['flash'] = [$tipe, $pesan]; }
function getFlash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// CSRF sederhana
function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrfField(): string { return '<input type="hidden" name="csrf" value="' . csrfToken() . '">'; }
function csrfCek(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function badgeStatus(string $s): string {
    $map = ['Menunggu' => 'warning text-dark', 'Diproses' => 'info text-dark', 'Selesai' => 'success', 'Ditolak' => 'danger'];
    return '<span class="badge bg-' . ($map[$s] ?? 'secondary') . '">' . e($s) . '</span>';
}

function tgl(?string $d, bool $jam = false): string {
    if (!$d) return '-';
    return date($jam ? 'd/m/Y H:i' : 'd/m/Y', strtotime($d));
}
